<?php
// Modificado el 2026-11-18 y el 2026-11-19 por varreaga (proyecto MiHelpdesk, fork de osTicket): tablero nuevo del tema; etiqueta de vencidas en la exportación. Ver NOTICE.md.
/*********************************************************************
    dashboard.php

    Staff's Dashboard - basic stats...etc.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');

require_once INCLUDE_DIR . 'class.report.php';

if ($_POST['export']) {
    $report = new OverviewReport($_POST['start'], $_POST['period']);
    switch (true) {
    case ($data = $report->getTabularData($_POST['export'])):
        $ts = date('Ymd');
        $group = Format::slugify($_POST['export']);
        $delimiter = ',';
        if (class_exists('NumberFormatter')) {
            $nf = NumberFormatter::create(Internationalization::getCurrentLocale(),
                NumberFormatter::DECIMAL);
            $s = $nf->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
            if ($s == ',')
                $delimiter = ';';
        }

        Http::download("stats-$group-$ts.csv", 'text/csv');
        $output = fopen('php://output', 'w');
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, $data['columns'], $delimiter, "\"", "");
        foreach ($data['data'] as $row)
            fputcsv($output, $row, $delimiter, "\"", "");
        exit;
    }
}

// MiHelpdesk: tablero del tema (indicadores, tiempos y desempeño)
if (app_theme()) {
    require_once INCLUDE_DIR . 'class.mh-dashboard.php';
    $mh = new MhDashboard($thisstaff, $_GET['period'] ?? '30d');
    $mh_group = array_key_exists($_GET['group'] ?? '', MhDashboard::getGroupChoices())
        ? $_GET['group'] : 'staff';

    // Tarjetas de "Ahora mismo": abren la lista de tickets correspondiente
    if (($go = $_GET['go'] ?? null) && ($criteria = MhDashboard::getListCriteria($go))) {
        $_SESSION['advsearch']['mh'.$go] = $criteria;
        Http::redirect('tickets.php?queue=' . urlencode('adhoc,mh'.$go));
    }

    // Exportar la tabla de desempeño a CSV
    if (isset($_GET['export'])) {
        $delimiter = ',';
        if (class_exists('NumberFormatter')) {
            $nf = NumberFormatter::create(Internationalization::getCurrentLocale(),
                NumberFormatter::DECIMAL);
            if ($nf && $nf->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL) == ',')
                $delimiter = ';';
        }
        Http::download(sprintf('desempeno-%s-%s.csv', $mh_group, date('Ymd')), 'text/csv');
        $out = fopen('php://output', 'w');
        fputs($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $first = array('staff' => __('Agent'), 'dept' => __('Department'), 'topic' => __('Help Topic'));
        fputcsv($out, array($first[$mh_group], __('Received'), __('Resolved'),
            __('Overdue tickets'), __('First response (minutes)'), __('Time to resolve (minutes)')),
            $delimiter, '"', '');
        foreach ($mh->getPerformance($mh_group) as $r)
            fputcsv($out, array($r['name'], $r['received'], $r['resolved'], $r['overdue'],
                $r['first_response'] === null ? '' : round($r['first_response']),
                $r['resolution'] === null ? '' : round($r['resolution'])),
                $delimiter, '"', '');
        exit;
    }

    $nav->setTabActive('dashboard');
    require(STAFFINC_DIR.'header.inc.php');
    include(STAFFINC_DIR.'templates/mh-dashboard.tmpl.php');
    include(STAFFINC_DIR.'footer.inc.php');
    exit;
}

$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.dashboard" />',
    "$('#content').data('tipNamespace', 'dashboard.dashboard');");

require(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.'dashboard.inc.php');
include(STAFFINC_DIR.'footer.inc.php');
?>
