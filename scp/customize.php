<?php
/*
 * MiHelpdesk — Administración › Configuración › Personalización.
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2. Archivo nuevo, no existe
 * en osTicket. La lógica está en include/mihelpdesk/class.customize.php y la
 * pantalla en include/staff/customize.inc.php.
 */
require('admin.inc.php');
require_once INCLUDE_DIR . 'mihelpdesk/class.customize.php';
require_once INCLUDE_DIR . 'class.page.php';

$mh_tabs = array('brand', 'home', 'nav', 'login', 'email');
$mh_tab = (isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], $mh_tabs)) ? $_REQUEST['tab'] : 'brand';
$errors = array();

if (!app_theme()) {
    $errors['err'] = __('Customization needs the MiHelpdesk theme to be enabled.');
} elseif ($_POST) {
    if (MhCustomize::save($mh_tab, $_POST, $errors) && !$errors) {
        $_SESSION['::mh_customize_msg'] = __('Changes saved.');
        Http::redirect('customize.php?tab=' . $mh_tab);
    }
    if (!isset($errors['err']))
        $errors['err'] = sprintf('%s %s', __('Unable to save the changes.'),
            __('Correct any errors below and try again.'));
}
if (!empty($_SESSION['::mh_customize_msg'])) {
    $msg = $_SESSION['::mh_customize_msg'];
    unset($_SESSION['::mh_customize_msg']);
}

$nav->setTabActive('settings', 'customize.php');
require_once(STAFFINC_DIR . 'header.inc.php');
include(STAFFINC_DIR . 'customize.inc.php');
include(STAFFINC_DIR . 'footer.inc.php');
