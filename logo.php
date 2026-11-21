<?php
// Modificado el 2026-11-21 por varreaga (proyecto MiHelpdesk, fork de osTicket): favicon y logo para modo oscuro. Ver NOTICE.md.
/*********************************************************************
    logo.php

    Simple logo to facilitate serving a customized client-side logo from
    osTicet. The logo is configurable in Admin Panel -> Settings -> Pages

    Peter Rotich <peter@osticket.com>
    Jared Hancock <jared@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
// Use Noop Session Handler
define('NOOP_SESSION', true);
require('client.inc.php');
$ttl = 86400; // max-age
// MiHelpdesk: ?favicon (icono de la pestaña) y ?dark (logo para modo oscuro),
// elegidos en Administración › Configuración › Personalización
require_once INCLUDE_DIR . 'mihelpdesk/class.customize.php';
if (isset($_GET['favicon'])) {
    if (($icon = MhCustomize::getFavicon()))
        $icon->display(false, $ttl);
    header("Cache-Control: private, max-age=$ttl");
    header('Pragma: private');
    Http::redirect(ROOT_PATH . 'images/oscar-favicon-32x32.png');
}
if (isset($_GET['dark']) && ($logo = MhCustomize::getDarkLogo('client')))
    $logo->display(false, $ttl);
if (($logo = $ost->getConfig()->getClientLogo())) {
    $logo->display(false, $ttl);
}

header("Cache-Control: private, max-age=$ttl");
header('Pragma: private');
header('Location: '.ASSETS_PATH.'images/logo.png');
?>
