<?php
/*********************************************************************
    class.mh-dashboard.php

    MiHelpdesk — Tablero del panel de agentes.

    Calcula los indicadores del tablero del tema: estado actual de los
    tickets, tiempos de atención (en minutos), tickets por día y una tabla
    de desempeño por agente, departamento o tema.

    Archivo nuevo de MiHelpdesk, fork de osTicket, distribuido bajo la
    Licencia Pública General de GNU, versión 2. Ver NOTICE.md.

    Todos los cálculos respetan los tickets que el agente puede ver:
    sus departamentos, o solo lo asignado a él y a sus equipos si su acceso
    está limitado.
**********************************************************************/

class MhDashboard {

    const PERIODS = array('30d', '7d', 'month', 'lastmonth');

    var $staff;
    var $period;
    var $start;     // marca de tiempo Unix, inicio del periodo
    var $stop;      // marca de tiempo Unix, fin del periodo (exclusivo)
    var $prevStart; // periodo anterior de la misma duración
    var $prevStop;
    var $tz;        // DateTimeZone del agente

    function __construct(Staff $staff, $period='30d') {
        $this->staff = $staff;
        $this->period = in_array($period, self::PERIODS) ? $period : '30d';
        try {
            $this->tz = new DateTimeZone($staff->getTimezone() ?: date_default_timezone_get());
        } catch (Exception $e) {
            $this->tz = new DateTimeZone(date_default_timezone_get());
        }
        $this->computeRange();
    }

    static function getPeriodChoices() {
        return array(
            '30d' => __('Last 30 days'),
            '7d' => __('Last 7 days'),
            'month' => __('This month'),
            'lastmonth' => __('Last month'),
        );
    }

    function computeRange() {
        $today = new DateTime('today', $this->tz);
        $tomorrow = (clone $today)->modify('+1 day');
        switch ($this->period) {
        case '7d':
            $start = (clone $tomorrow)->modify('-7 days');
            $stop = $tomorrow;
            break;
        case 'month':
            $start = new DateTime('first day of this month midnight', $this->tz);
            $stop = $tomorrow;
            break;
        case 'lastmonth':
            $start = new DateTime('first day of last month midnight', $this->tz);
            $stop = new DateTime('first day of this month midnight', $this->tz);
            break;
        default:
            $start = (clone $tomorrow)->modify('-30 days');
            $stop = $tomorrow;
        }
        $this->start = $start->getTimestamp();
        $this->stop = $stop->getTimestamp();
        $length = $this->stop - $this->start;
        $this->prevStop = $this->start;
        $this->prevStart = $this->start - $length;
    }

    function getRangeDescription() {
        $fmt = function ($ts) {
            return Format::date($ts, false, false, $this->tz->getName());
        };
        return sprintf(__('%s to %s'), $fmt($this->start), $fmt($this->stop - 1));
    }

    // ---------------------------------------------------------- Alcance

    /**
     * Condición SQL (sobre el alias "t" de la tabla de tickets) que limita a
     * los tickets que el agente puede ver.
     */
    function scopeSql() {
        $staff = $this->staff;
        $me = (int) $staff->getId();
        $teams = array_filter(array_map('intval', $staff->getTeams() ?: array()));
        $parts = array("t.staff_id = $me");
        if ($teams)
            $parts[] = 't.team_id IN ('.implode(',', $teams).')';
        if (!$staff->isAccessLimited()
                && ($depts = array_filter(array_map('intval', $staff->getDepts() ?: array()))))
            $parts[] = 't.dept_id IN ('.implode(',', $depts).')';
        return '('.implode(' OR ', $parts).')';
    }

    static function between($column, $start, $stop) {
        return sprintf('%s >= FROM_UNIXTIME(%d) AND %s < FROM_UNIXTIME(%d)',
            $column, $start, $column, $stop);
    }

    // ---------------------------------------------------- Ahora mismo

    function getCurrentCounts() {
        $base = function () {
            return $this->staff->applyVisibility(Ticket::objects())
                ->filter(array('status__state' => 'open'));
        };
        return array(
            'open' => $base()->count(),
            'unassigned' => $base()->filter(array('staff_id' => 0, 'team_id' => 0))->count(),
            'overdue' => $base()->filter(array('isoverdue' => 1))->count(),
            'mine' => $base()->filter(array('staff_id' => $this->staff->getId()))->count(),
        );
    }

    /**
     * Criterios de búsqueda para abrir la lista de tickets de cada tarjeta.
     */
    static function getListCriteria($which) {
        $open = array('status__state', 'includes', array('open' => __('Open')));
        switch ($which) {
        case 'open':
            return array($open);
        case 'unassigned':
            return array($open, array('assignee', '!assigned', null));
        case 'overdue':
            return array($open, array('isoverdue', 'set', null));
        case 'mine':
            return array($open, array('assignee', 'includes', array('M' => __('Me'))));
        }
        return null;
    }

    // ------------------------------------------- Tiempos de atención

    /** Promedio de minutos hasta la primera respuesta de un agente. */
    function firstResponseSql($start, $stop) {
        $scope = $this->scopeSql();
        $created = self::between('t.created', $start, $stop);
        return "SELECT AVG(TIMESTAMPDIFF(MINUTE, x.created, x.first_resp)) AS minutes, COUNT(*) AS n
            FROM (
                SELECT t.ticket_id, t.created, MIN(e.created) AS first_resp
                FROM ".TICKET_TABLE." t
                JOIN ".THREAD_TABLE." th ON th.object_id = t.ticket_id AND th.object_type = 'T'
                JOIN ".THREAD_ENTRY_TABLE." e ON e.thread_id = th.id AND e.type = 'R' AND e.staff_id > 0
                WHERE $created AND $scope
                GROUP BY t.ticket_id
            ) x";
    }

    function avgFirstResponse($start, $stop) {
        $sql = $this->firstResponseSql($start, $stop);
        $row = db_assoc_array(db_query($sql));
        return $row[0]['n'] ? (float) $row[0]['minutes'] : null;
    }

    function closedSql($start, $stop) {
        return self::between('t.closed', $start, $stop)
            ." AND s.state = 'closed' AND ".$this->scopeSql();
    }

    /** Promedio de minutos desde que llega hasta que se resuelve o cierra. */
    function avgResolution($start, $stop) {
        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created, t.closed)) AS minutes, COUNT(*) AS n
            FROM ".TICKET_TABLE." t
            JOIN ".TICKET_STATUS_TABLE." s ON s.id = t.status_id
            WHERE ".$this->closedSql($start, $stop);
        $row = db_assoc_array(db_query($sql));
        return $row[0]['n'] ? (float) $row[0]['minutes'] : null;
    }

    /** Porcentaje de tickets resueltos antes de su fecha de vencimiento. */
    function onTimePercent($start, $stop) {
        $sql = "SELECT SUM(t.closed <= COALESCE(t.duedate, t.est_duedate)) AS ontime, COUNT(*) AS n
            FROM ".TICKET_TABLE." t
            JOIN ".TICKET_STATUS_TABLE." s ON s.id = t.status_id
            WHERE ".$this->closedSql($start, $stop)."
                AND COALESCE(t.duedate, t.est_duedate) IS NOT NULL";
        $row = db_assoc_array(db_query($sql));
        return $row[0]['n'] ? 100 * $row[0]['ontime'] / $row[0]['n'] : null;
    }

    function getTimes() {
        $metrics = array(
            'first_response' => array('avgFirstResponse', 'lower'),
            'resolution' => array('avgResolution', 'lower'),
            'on_time' => array('onTimePercent', 'higher'),
        );
        $out = array();
        foreach ($metrics as $key => $info) {
            list($method, $better) = $info;
            $cur = $this->$method($this->start, $this->stop);
            $prev = $this->$method($this->prevStart, $this->prevStop);
            $change = null;
            if ($cur !== null && $prev !== null) {
                if ($key == 'on_time')
                    $change = $cur - $prev; // puntos porcentuales
                elseif ($prev > 0)
                    $change = 100 * ($cur - $prev) / $prev;
            }
            $improved = null;
            if ($change !== null && abs($change) >= 0.5)
                $improved = ($better == 'lower') ? $change < 0 : $change > 0;
            $out[$key] = array('value' => $cur, 'change' => $change, 'improved' => $improved);
        }
        return $out;
    }

    // ---------------------------------------------- Tickets por día

    function getDaily() {
        $offset = $this->tz->getOffset(new DateTime('@'.$this->start));
        $scope = $this->scopeSql();
        $day = function ($col) use ($offset) {
            return sprintf('FLOOR((UNIX_TIMESTAMP(%s) + %d) / 86400)', $col, $offset);
        };
        $received = db_assoc_array(db_query(
            "SELECT ".$day('t.created')." AS d, COUNT(*) AS n
            FROM ".TICKET_TABLE." t
            WHERE ".self::between('t.created', $this->start, $this->stop)." AND $scope
            GROUP BY d"));
        $resolved = db_assoc_array(db_query(
            "SELECT ".$day('t.closed')." AS d, COUNT(*) AS n
            FROM ".TICKET_TABLE." t
            JOIN ".TICKET_STATUS_TABLE." s ON s.id = t.status_id
            WHERE ".$this->closedSql($this->start, $this->stop)."
            GROUP BY d"));

        $days = array();
        $first = (int) floor(($this->start + $offset) / 86400);
        $last = (int) floor(($this->stop - 1 + $offset) / 86400);
        for ($d = $first; $d <= $last; $d++)
            $days[$d] = array('ts' => $d * 86400 - $offset + 43200, 'received' => 0, 'resolved' => 0);
        foreach ($received as $r)
            if (isset($days[$r['d']])) $days[$r['d']]['received'] = (int) $r['n'];
        foreach ($resolved as $r)
            if (isset($days[$r['d']])) $days[$r['d']]['resolved'] = (int) $r['n'];
        return array_values($days);
    }

    // ------------------------------------------------- Desempeño

    static function getGroupChoices() {
        return array(
            'staff' => __('By agent'),
            'dept' => __('By department'),
            'topic' => __('By topic'),
        );
    }

    function getPerformance($group='staff') {
        $cols = array('staff' => 'staff_id', 'dept' => 'dept_id', 'topic' => 'topic_id');
        $col = $cols[$group] ?? 'staff_id';
        $scope = $this->scopeSql();
        $start = $this->start; $stop = $this->stop;
        $rows = array();
        $add = function ($key, $field, $value) use (&$rows) {
            $key = (int) $key;
            if (!isset($rows[$key]))
                $rows[$key] = array('received' => 0, 'resolved' => 0, 'overdue' => 0,
                    'first_response' => null, 'resolution' => null);
            $rows[$key][$field] = $value;
        };

        foreach (db_assoc_array(db_query(
                "SELECT t.$col AS grp, COUNT(*) AS n FROM ".TICKET_TABLE." t
                WHERE ".self::between('t.created', $start, $stop)." AND $scope
                GROUP BY grp")) as $r)
            $add($r['grp'], 'received', (int) $r['n']);

        foreach (db_assoc_array(db_query(
                "SELECT t.$col AS grp, COUNT(*) AS n,
                    AVG(TIMESTAMPDIFF(MINUTE, t.created, t.closed)) AS minutes
                FROM ".TICKET_TABLE." t
                JOIN ".TICKET_STATUS_TABLE." s ON s.id = t.status_id
                WHERE ".$this->closedSql($start, $stop)."
                GROUP BY grp")) as $r) {
            $add($r['grp'], 'resolved', (int) $r['n']);
            $add($r['grp'], 'resolution', (float) $r['minutes']);
        }

        foreach (db_assoc_array(db_query(
                "SELECT t.$col AS grp, COUNT(*) AS n FROM ".TICKET_TABLE." t
                JOIN ".TICKET_STATUS_TABLE." s ON s.id = t.status_id
                WHERE s.state = 'open' AND t.isoverdue = 1 AND $scope
                GROUP BY grp")) as $r)
            $add($r['grp'], 'overdue', (int) $r['n']);

        foreach (db_assoc_array(db_query(
                "SELECT x.$col AS grp, AVG(TIMESTAMPDIFF(MINUTE, x.created, x.first_resp)) AS minutes
                FROM (
                    SELECT t.ticket_id, t.created, t.$col, MIN(e.created) AS first_resp
                    FROM ".TICKET_TABLE." t
                    JOIN ".THREAD_TABLE." th ON th.object_id = t.ticket_id AND th.object_type = 'T'
                    JOIN ".THREAD_ENTRY_TABLE." e ON e.thread_id = th.id AND e.type = 'R' AND e.staff_id > 0
                    WHERE ".self::between('t.created', $start, $stop)." AND $scope
                    GROUP BY t.ticket_id
                ) x GROUP BY grp")) as $r)
            $add($r['grp'], 'first_response', (float) $r['minutes']);

        // Nombres
        switch ($group) {
        case 'dept':
            $names = Dept::getDepartments(null, true, true);
            $none = __('No department');
            break;
        case 'topic':
            $names = Topic::getHelpTopics(false, true);
            $none = __('No help topic');
            break;
        default:
            $names = Staff::getStaffMembers();
            $none = __('Unassigned');
        }
        $out = array();
        foreach ($rows as $id => $r) {
            $r['id'] = $id;
            $r['name'] = $id ? (string) ($names[$id] ?? sprintf('#%d', $id)) : $none;
            $r['none'] = !$id;
            $out[] = $r;
        }
        // Más trabajo primero; "sin asignar" al final
        usort($out, function ($a, $b) {
            if ($a['none'] != $b['none'])
                return $a['none'] ? 1 : -1;
            return ($b['received'] + $b['resolved']) - ($a['received'] + $a['resolved'])
                ?: strcasecmp($a['name'], $b['name']);
        });
        return $out;
    }

    // ------------------------------------------------- Formato

    /** 35 min · 1 h 12 min · 1 d 4 h */
    static function formatMinutes($minutes) {
        if ($minutes === null)
            return '—';
        $m = (int) round($minutes);
        if ($m < 1)
            return __('< 1 min');
        if ($m < 60)
            return sprintf(__('%d min'), $m);
        $h = intdiv($m, 60);
        if ($h < 24)
            return ($m % 60)
                ? sprintf(__('%d h %d min'), $h, $m % 60)
                : sprintf(__('%d h'), $h);
        $d = intdiv($h, 24);
        return ($h % 24)
            ? sprintf(__('%d d %d h'), $d, $h % 24)
            : sprintf(__('%d d'), $d);
    }
}
