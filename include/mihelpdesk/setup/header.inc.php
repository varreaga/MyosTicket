<?php
/*********************************************************************
    MiHelpdesk — Instalador: encabezado

    Plantillas del instalador con el estilo del tema. setup/install.php
    las usa en lugar de setup/inc/*.inc.php cuando el tema está activo;
    con define('APP_THEME', '') vuelve el instalador original.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/
if (!defined('SETUPINC')) die('Kwaheri!');
if ($cfg)
    header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");

// Paso actual según la pantalla: 1 requisitos, 2 datos, 3 listo
$mh_steps = array(1 => __('Requirements check'), 2 => __('Installation details'), 3 => __('All set'));
$mh_step = 1;
if (in_array($inc, array('install.inc.php', 'file-missing.inc.php', 'file-perm.inc.php')))
    $mh_step = 2;
elseif (in_array($inc, array('install-done.inc.php', 'subscribe.inc.php', 'file-unclean.inc.php')))
    $mh_step = 3;

require_once __DIR__.'/funciones.php';

$mh_lang = Internationalization::getCurrentLanguage();
$mh_info = Internationalization::getLanguageInfo($mh_lang);
$mh_theme = app_theme();
$mh_css = function ($name) use ($mh_theme) {
    $file = 'assets/'.$mh_theme.'/css/'.$name.'.css';
    return '../'.$file.'?v='.@filemtime(ROOT_DIR.$file);
};
?>
<!DOCTYPE html>
<html lang="<?php echo Format::htmlchars(str_replace('_', '-', $mh_lang)); ?>"<?php
    if (@$mh_info['direction'] == 'rtl') echo ' dir="rtl" class="rtl"'; ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Format::htmlchars(sprintf(__('Install %s'), APP_NAME)); ?></title>
    <?php app_theme_head(); ?>
    <link rel="stylesheet" href="<?php echo $mh_css('tokens'); ?>">
    <link rel="stylesheet" href="<?php echo $mh_css('setup'); ?>">
</head>
<body class="mh-theme mh-setup">
<div class="mh-setup-wrap">
    <header class="mh-setup-top">
        <div class="mh-setup-brand">
            <span class="mh-setup-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2" y="14" width="5" height="6" rx="2"/><rect x="17" y="14" width="5" height="6" rx="2"/></svg></span>
            <div>
                <div class="mh-setup-name"><?php echo Format::htmlchars(APP_NAME); ?></div>
                <div class="mh-setup-sub"><?php echo Format::htmlchars(sprintf(__('Installer · version %s'),
                    $installer->getVersionVerbose())); ?></div>
            </div>
        </div>
<?php
if (($all_langs = Internationalization::availableLanguages()) && count($all_langs) > 1) { ?>
        <nav class="mh-setup-langs" aria-label="<?php echo Format::htmlchars(__('Language')); ?>">
<?php foreach ($all_langs as $code => $info) {
        $code = @$info['code'] ?: $code;
        // Solo el nombre del idioma, sin el país: «Español», «English»
        $name = Internationalization::getLanguageDescription($code);
        $name = trim(preg_replace('/\s*\(.*\)\s*$/', '', $name));
        $name = function_exists('mb_convert_case') ? mb_convert_case($name, MB_CASE_TITLE, 'UTF-8') : ucfirst($name); ?>
            <a href="?lang=<?php echo urlencode($code); ?>" hreflang="<?php echo Format::htmlchars(str_replace('_', '-', $code)); ?>"
                <?php if (strcasecmp($code, $mh_lang) == 0) echo 'aria-current="true"'; ?>><?php echo Format::htmlchars($name); ?></a>
<?php } ?>
        </nav>
<?php } ?>
    </header>
    <ol class="mh-steps">
<?php foreach ($mh_steps as $n => $label) { ?>
        <li class="<?php echo $n < $mh_step ? 'is-done' : ($n == $mh_step ? 'is-current' : ''); ?>"<?php
            if ($n == $mh_step) echo ' aria-current="step"'; ?>><?php echo Format::htmlchars($label); ?></li>
<?php } ?>
    </ol>
<?php if (osTicket::is_ie()) { ?>
    <div class="mh-alert mh-alert-warn"><?php echo Format::htmlchars(sprintf(__('%s does not support Internet Explorer. Use a current browser.'), APP_NAME)); ?></div>
<?php } ?>
    <main class="mh-setup-card">
