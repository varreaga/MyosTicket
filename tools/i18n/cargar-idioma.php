<?php
/**
 * MiHelpdesk — Pasa a otro idioma los textos que el instalador guardó en la
 * base de datos.
 *
 * Cuando el sistema se instaló en inglés, la página de inicio del portal, las
 * plantillas de correo, los nombres de las colas, estados, prioridades,
 * formularios, departamentos, temas de ayuda y respuestas predefinidas quedan
 * guardados en inglés. Este script los toma del paquete de idioma
 * (include/i18n/<idioma>.phar) y:
 *
 *   - cambia solo los textos que siguen igual a los de la instalación en
 *     inglés; lo que el administrador ya editó se deja como está y se avisa;
 *   - crea un juego de plantillas de correo en el idioma y lo deja como
 *     predeterminado (el juego en inglés no se borra).
 *
 * Uso (desde la raíz del sistema):
 *
 *     php tools/i18n/cargar-idioma.php es_MX            # solo muestra qué cambiaría
 *     php tools/i18n/cargar-idioma.php es_MX --aplicar  # hace los cambios
 *
 * Opciones:
 *     --sin-plantillas   no crea el juego de plantillas de correo
 *
 * Haz un respaldo de la base de datos antes de usar --aplicar.
 */
if (php_sapi_name() !== 'cli')
    die("Solo desde la línea de comandos.\n");

$args = array_slice($argv, 1);
$aplicar = in_array('--aplicar', $args);
$conPlantillas = !in_array('--sin-plantillas', $args);
$args = array_values(array_filter($args, function ($a) { return $a[0] != '-'; }));
$lang = $args[0] ?? 'es_MX';

chdir(dirname(__DIR__, 2));
require_once 'bootstrap.php';
require_once INCLUDE_DIR . 'class.cli.php';
Bootstrap::i18n_prep();
Bootstrap::loadConfig();
Bootstrap::defineTables(TABLE_PREFIX);
Bootstrap::loadCode();
Bootstrap::connect();
require_once INCLUDE_DIR . 'class.i18n.php';
require_once INCLUDE_DIR . 'class.template.php';
require_once INCLUDE_DIR . 'class.draft.php';

global $ost, $cfg;
if (!($ost = osTicket::start()) || !($cfg = $ost->getConfig()))
    die("No se pudo cargar la configuración.\n");

if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $lang) || $lang == 'en_US')
    die("Idioma no válido: $lang (ejemplo: es_MX).\n");
$prueba = new DataTemplate('queue.yaml', array($lang));
if (!$prueba->getData())
    die("No se encontró el paquete de idioma include/i18n/$lang.phar.\n");

echo $aplicar
    ? "Aplicando el idioma $lang…\n\n"
    : "Vista previa (no se cambia nada; usa --aplicar para hacer los cambios).\n\n";

$cuenta = array('cambiado' => 0, 'igual' => 0, 'editado' => 0);

function normal($s) {
    return trim(preg_replace('/\s+/u', ' ', (string) $s));
}

function igual($a, $b) {
    return normal($a) === normal($b);
}

// Textos que se pueden reemplazar aunque ya no estén en inglés: los del
// paquete tal cual (sin las correcciones de MiHelpdesk) y los que MiHelpdesk
// usó en versiones anteriores. Así, volver a correr el script actualiza una
// instalación que ya se había pasado a español.
$reemplazables = array();
foreach (DataTemplate::anteriores($lang) as $t)
    $reemplazables[normal($t)] = true;

/** Datos en inglés y en el idioma pedido de un archivo del paquete. */
function datos($path, $lang) {
    global $reemplazables;
    $en = new DataTemplate($path, array('en_US'));
    $tr = new DataTemplate($path, array($lang));
    $crudo = (new DataTemplate($path, array($lang)))->getData(false);
    if (is_array($crudo))
        array_walk_recursive($crudo, function ($v) use (&$reemplazables) {
            if (is_string($v) && $v !== '')
                $reemplazables[normal($v)] = true;
        });
    return array($en->getData() ?: array(), $tr->getData() ?: array());
}

/**
 * Cambia una columna de una fila si todavía tiene el texto en inglés de la
 * instalación. $donde es un arreglo columna => valor.
 */
function cambiar($tabla, $donde, $col, $en, $nuevo, $etiqueta) {
    global $aplicar, $cuenta, $reemplazables;
    if ($nuevo === null || $nuevo === '' || $en === null)
        return;
    $w = array();
    foreach ($donde as $k => $v)
        $w[] = "`$k`=" . db_input($v);
    $w = implode(' AND ', $w);
    $res = db_query("SELECT `$col` FROM `$tabla` WHERE $w");
    if (!$res || !db_num_rows($res))
        return;
    list($actual) = db_fetch_row($res);
    if (igual($actual, $nuevo)) {
        $cuenta['igual']++;
        return;
    }
    if (!igual($actual, $en) && !isset($reemplazables[normal($actual)])) {
        $cuenta['editado']++;
        printf("  · Se deja (ya editado): %s\n", $etiqueta);
        return;
    }
    $cuenta['cambiado']++;
    $corto = function ($s) {
        $s = trim(preg_replace('/\s+/u', ' ', strip_tags($s)));
        return mb_strlen($s) > 50 ? mb_substr($s, 0, 49) . '…' : $s;
    };
    printf("  ✓ %s: «%s» → «%s»\n", $etiqueta, $corto($actual), $corto($nuevo));
    if ($aplicar)
        db_query("UPDATE `$tabla` SET `$col`=" . db_input($nuevo) . " WHERE $w");
}

/** Recorre listas con id: para cada elemento cambia los campos indicados. */
function porId($path, $tabla, $idYaml, $idCol, $campos, $titulo) {
    global $lang;
    echo "$titulo\n";
    list($en, $tr) = datos($path, $lang);
    $trPorId = array();
    foreach ($tr as $k => $o)
        $trPorId[isset($o[$idYaml]) ? $o[$idYaml] : $k] = $o;
    foreach ($en as $k => $o) {
        $id = isset($o[$idYaml]) ? $o[$idYaml] : $k;
        if (!isset($trPorId[$id]))
            continue;
        foreach ($campos as $yaml => $col)
            cambiar($tabla, array($idCol => $id), $col, $o[$yaml] ?? null,
                $trPorId[$id][$yaml] ?? null, "$titulo #$id ($col)");
    }
}

// ---------------------------------------------------------------- Páginas
echo "Páginas del portal y mensajes\n";
foreach (array('landing', 'thank-you', 'offline', 'registration-staff',
        'pwreset-staff', 'banner-staff', 'registration-client',
        'pwreset-client', 'banner-client', 'registration-confirm',
        'registration-thanks', 'access-link', 'email2fa-staff') as $type) {
    list($en, $tr) = datos("templates/page/$type.yaml", $lang);
    if (!$tr)
        continue;
    foreach (array('name', 'body', 'notes') as $col)
        cambiar(PAGE_TABLE, array('type' => $type), $col, $en[$col] ?? null,
            $tr[$col] ?? null, "Página $type ($col)");
}

// ---------------------------------------------------------------- Catálogos
porId('ticket_status.yaml', TICKET_STATUS_TABLE, 'id', 'id',
    array('name' => 'name'), 'Estado');
porId('priority.yaml', PRIORITY_TABLE, 'priority_id', 'priority_id',
    array('priority_desc' => 'priority_desc'), 'Prioridad');
porId('queue.yaml', QUEUE_TABLE, 'id', 'id', array('title' => 'title', 'config' => 'config'), 'Cola');
porId('queue_column.yaml', COLUMN_TABLE, 'id', 'id', array('name' => 'name'), 'Columna');
porId('queue_sort.yaml', QUEUE_SORT_TABLE, 'id', 'id', array('name' => 'name'), 'Orden');
porId('department.yaml', DEPT_TABLE, 'id', 'id', array('name' => 'name'), 'Departamento');
porId('sla.yaml', SLA_TABLE, 'id', 'id', array('name' => 'name'), 'SLA');
porId('help_topic.yaml', TOPIC_TABLE, 'topic_id', 'topic_id',
    array('topic' => 'topic'), 'Tema de ayuda');
porId('role.yaml', ROLE_TABLE, 'id', 'id', array('name' => 'name'), 'Rol');
porId('schedule.yaml', SCHEDULE_TABLE, 'id', 'id', array('name' => 'name'), 'Horario');

// Encabezados de columnas de cada cola
echo "Encabezados de las colas\n";
list($en, $tr) = datos('queue.yaml', $lang);
$trPorId = array();
foreach ($tr as $q)
    $trPorId[$q['id']] = $q;
foreach ($en as $q) {
    if (!isset($trPorId[$q['id']]['columns']))
        continue;
    $cols = array();
    foreach ($trPorId[$q['id']]['columns'] as $c)
        $cols[$c['column_id']] = $c;
    foreach ($q['columns'] ?? array() as $c)
        if (isset($cols[$c['column_id']]))
            cambiar(QUEUE_COLUMN_TABLE,
                array('queue_id' => $q['id'], 'column_id' => $c['column_id']),
                'heading', $c['heading'] ?? null, $cols[$c['column_id']]['heading'] ?? null,
                "Cola #{$q['id']} columna {$c['column_id']}");
}

// Equipos (sin id en los datos: se buscan por nombre y orden)
echo "Equipos\n";
list($en, $tr) = datos('team.yaml', $lang);
foreach ($en as $i => $t)
    if (isset($tr[$i]['name']))
        cambiar(TEAM_TABLE, array('name' => $t['name']), 'name', $t['name'],
            $tr[$i]['name'], "Equipo «{$t['name']}»");

// Formularios y sus campos. Los de usuario y solicitud traen id; los de
// empresa, organización y tarea se buscan por su tipo. Las propiedades de la
// lista de estados (list.yaml) son también un formulario, de tipo L<id>.
echo "Formularios\n";
function formas($datos, $lang) {
    $r = array();
    foreach ($datos as $f)
        if (isset($f['id']))
            $r['id:' . $f['id']] = $f;
        elseif (isset($f['type']))
            $r['type:' . $f['type']] = $f;
    return $r;
}
list($en, $tr) = datos('form.yaml', $lang);
$en = formas($en, $lang);
$tr = formas($tr, $lang);
list($lEn, $lTr) = datos('list.yaml', $lang);
foreach ($lEn as $i => $l) {
    if (empty($l['properties']) || empty($lTr[$i]['properties']) || empty($l['type']))
        continue;
    $res = db_query('SELECT id FROM ' . LIST_TABLE . ' WHERE type=' . db_input($l['type']));
    if ($res && db_num_rows($res)) {
        $k = 'type:L' . db_result($res);
        $en[$k] = $l['properties'];
        $tr[$k] = $lTr[$i]['properties'];
    }
}
foreach ($en as $k => $f) {
    if (!isset($tr[$k]))
        continue;
    $t = $tr[$k];
    if (strpos($k, 'id:') === 0) {
        $id = substr($k, 3);
    } else {
        $res = db_query('SELECT id FROM ' . FORM_SEC_TABLE . ' WHERE type=' . db_input(substr($k, 5)));
        if (!$res || db_num_rows($res) != 1)
            continue;
        $id = db_result($res);
    }
    foreach (array('title', 'instructions', 'notes') as $col)
        cambiar(FORM_SEC_TABLE, array('id' => $id), $col, $f[$col] ?? null,
            $t[$col] ?? null, "Formulario #$id ($col)");
    $campos = array();
    foreach ($t['fields'] ?? array() as $c)
        if (isset($c['name']))
            $campos[$c['name']] = $c;
    foreach ($f['fields'] ?? array() as $c) {
        if (!isset($c['name']) || !isset($campos[$c['name']]))
            continue;
        foreach (array('label', 'hint') as $col)
            cambiar(FORM_FIELD_TABLE, array('form_id' => $id, 'name' => $c['name']),
                $col, $c[$col] ?? null, $campos[$c['name']][$col] ?? null,
                "Campo {$c['name']} del formulario #$id ($col)");
    }
}

// Descripción de los estados (guardada en la columna properties, en JSON)
echo "Descripción de los estados\n";
list($en, $tr) = datos('ticket_status.yaml', $lang);
$trPorId = array();
foreach ($tr as $st)
    $trPorId[$st['id']] = $st;
foreach ($en as $st) {
    $nuevo = $trPorId[$st['id']]['properties']['description'] ?? null;
    $viejo = $st['properties']['description'] ?? null;
    if (!$nuevo || !$viejo)
        continue;
    $res = db_query('SELECT properties FROM ' . TICKET_STATUS_TABLE . ' WHERE id=' . db_input($st['id']));
    if (!$res || !db_num_rows($res))
        continue;
    $props = json_decode(db_result($res), true);
    if (!is_array($props) || !isset($props['description']))
        continue;
    $actual = $props['description'];
    if (igual($actual, $nuevo)) {
        $cuenta['igual']++;
    } elseif (igual($actual, $viejo) || isset($reemplazables[normal($actual)])) {
        $cuenta['cambiado']++;
        printf("  ✓ Estado #%d (descripción): «%s» → «%s»\n", $st['id'], normal($actual), normal($nuevo));
        if ($aplicar) {
            $props['description'] = trim($nuevo);
            db_query('UPDATE ' . TICKET_STATUS_TABLE . ' SET properties='
                . db_input(json_encode($props)) . ' WHERE id=' . db_input($st['id']));
        }
    } else {
        $cuenta['editado']++;
        printf("  · Se deja (ya editado): Estado #%d (descripción)\n", $st['id']);
    }
}

// Listas personalizadas (nombre de la lista de estados)
echo "Listas\n";
list($en, $tr) = datos('list.yaml', $lang);
foreach ($en as $i => $l)
    if (isset($l['type']) && isset($tr[$i]))
        foreach (array('name', 'name_plural') as $col)
            cambiar(LIST_TABLE, array('type' => $l['type']), $col, $l[$col] ?? null,
                $tr[$i][$col] ?? null, "Lista {$l['type']} ($col)");

// Respuestas predefinidas de ejemplo
echo "Respuestas predefinidas de ejemplo\n";
list($en, $tr) = datos('templates/premade.yaml', $lang);
foreach ($en as $i => $c) {
    if (!isset($tr[$i]['title']))
        continue;
    // Primero la respuesta (se busca por el título en inglés), luego el título
    cambiar(CANNED_TABLE, array('title' => $c['title']), 'response', $c['response'] ?? null,
        $tr[$i]['response'] ?? null, "Respuesta «{$c['title']}» (texto)");
    cambiar(CANNED_TABLE, array('title' => $c['title']), 'title', $c['title'],
        $tr[$i]['title'], "Respuesta «{$c['title']}» (título)");
}

// ---------------------------------------------------------------- Correo
if ($conPlantillas) {
    echo "Plantillas de correo\n";
    $res = db_query('SELECT tpl_id FROM ' . EMAIL_TEMPLATE_GRP_TABLE
        . ' WHERE lang=' . db_input($lang) . ' ORDER BY tpl_id LIMIT 1');
    $tplId = ($res && db_num_rows($res)) ? db_result($res) : null;
    if ($tplId) {
        echo "  · Ya existe un juego de plantillas en $lang (#$tplId).\n";
    } else {
        // Mismo nombre y notas que usaría el instalador en ese idioma
        $grupo = (new DataTemplate('email_template_group.yaml', array($lang)))->getData();
        $grupo = is_array($grupo) ? reset($grupo) : array();
        $nombre = mb_substr($grupo['name'] ?? sprintf('Plantilla %s %s', APP_NAME, $lang), 0, 32);
        echo "  ✓ Se crea el juego «{$nombre}» con las plantillas en $lang.\n";
        if ($aplicar) {
            $errors = array();
            $tplId = EmailTemplateGroup::create(array(
                'name' => $nombre, 'isactive' => 1, 'lang_id' => $lang,
                'notes' => $grupo['notes'] ?? '',
            ), $errors);
            if (!$tplId)
                die("  No se pudo crear: " . implode(' ', $errors) . "\n");
            foreach (EmailTemplateGroup::$all_names as $name => $info) {
                $tp = new DataTemplate("templates/email/$name.yaml", array($lang, 'en_US'));
                if (!($t = $tp->getData()))
                    continue;
                $t['tpl_id'] = $tplId;
                $t['code_name'] = $name;
                $id = EmailTemplate::create($t, $errors);
                if ($id && ($template = EmailTemplate::lookup($id))
                        && ($ids = Draft::getAttachmentIds($t['body'])))
                    $template->attachments->upload($ids, true);
            }
        }
    }
    if ($cfg->getDefaultTemplateId() != $tplId) {
        echo "  ✓ El juego en $lang queda como predeterminado.\n";
        if ($aplicar && $tplId)
            $cfg->set('default_template_id', $tplId);
    }
}

printf("\nResumen: %d por cambiar, %d ya traducidos, %d editados a mano (se dejan).\n",
    $cuenta['cambiado'], $cuenta['igual'], $cuenta['editado']);
if (!$aplicar)
    echo "Nada se cambió. Para aplicar: php tools/i18n/cargar-idioma.php $lang --aplicar\n";
else
    echo "Listo. Si aún no lo hiciste, elige el idioma en Administración → Configuración → Sistema.\n";
