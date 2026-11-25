<?php
// Modificado el 2026-11-16, 2026-11-19, 2026-11-21 y 2026-11-24 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca y apariencia del tema (modo oscuro); favicon de Personalización; idioma de la página y recarga del inicio de sesión sin meta refresh. Ver NOTICE.md.
defined('OSTSCPINC') or die('Invalid path');
header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");
?>
<!DOCTYPE html>
<?php $mh_lang = Internationalization::rfc1766(Internationalization::getCurrentLanguage()); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $mh_lang; ?>" lang="<?php echo $mh_lang; ?>">
<head>
<?php app_theme_head(); ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <?php // MiHelpdesk: en lugar de <meta refresh> (que recarga aunque se esté escribiendo),
    // se recarga a las 2 horas solo si los campos siguen vacíos, para renovar el token. ?>
    <script>setTimeout(function(){var f=document.querySelectorAll('input[type=text],input[type=password],input[type=email]');for(var i=0;i<f.length;i++)if(f[i].value)return;location.reload();},7200000);</script>
    <title><?php echo Format::htmlchars(APP_NAME); ?> :: <?php echo __('Agent Login'); ?></title>
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
    <meta name="robots" content="noindex" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo app_favicon_tags(); ?>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("input:not(.dp):visible:enabled:first").focus();
         });
    </script>
    <?php app_theme_styles('login'); ?>
</head>
<body id="loginBody"<?php echo app_theme_body_class('login'); ?>>

