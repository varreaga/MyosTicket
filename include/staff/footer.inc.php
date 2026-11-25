<?php /* Modificado el 2026-11-16, 2026-11-17, 2026-11-18 y 2026-11-19 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca, editor y script del tema; idioma de los textos de JavaScript; el 2026-11-25, versión en la dirección de esas traducciones. Ver NOTICE.md. */ ?>
</div>
</div>
<?php if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
    <div id="footer">
        <?php echo __('Copyright &copy;') ?> 2006-<?php echo date('Y'); ?>&nbsp;<?php
        echo Format::htmlchars((string) $ost->company ?: APP_NAME); ?>&nbsp;<?php echo __('All Rights Reserved.'); ?>
    </div>
<?php
if(is_object($thisstaff) && $thisstaff->isStaff()) { ?>
    <div>
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        <img src="<?php echo ROOT_PATH; ?>scp/autocron.php" alt="" width="1" height="1" border="0" />
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
    </div>
<?php
} ?>
</div>
<div id="overlay"></div>
<div id="loading">
    <i class="icon-spinner icon-spin icon-3x pull-left icon-light"></i>
    <h1><?php echo __('Loading ...');?></h1>
</div>
<div class="dialog draggable" style="display:none;" id="popup">
    <div id="popup-loading">
        <h1 style="margin-bottom: 20px;"><i class="icon-spinner icon-spin icon-large"></i>
        <?php echo __('Loading ...');?></h1>
    </div>
    <div class="body"></div>
</div>
<div style="display:none;" class="dialog" id="alert">
    <h3><i class="icon-warning-sign"></i> <span id="title"></span></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <div id="body" style="min-height: 20px;"></div>
    <hr style="margin-top:3em"/>
    <p class="full-width">
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('OK');?>" class="close ok">
        </span>
     </p>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.pjax.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/scp.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/tips.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/mh-editor.js?v=<?php echo @filemtime(ROOT_DIR.'js/mh-editor.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/jquery.translatable.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/jquery.dropdown.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/bootstrap-tooltip.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>scp/js/jb.overflow.menu.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>scp/css/tooltip.css">
<script type="text/javascript">
    getConfig().resolve(<?php
        include INCLUDE_DIR . 'ajax.config.php';
        $api = new ConfigAjaxAPI();
        print $api->scp(false);
    ?>);
</script>
<?php
// MiHelpdesk: idioma efectivo del agente (si eligió "predeterminado del
// sistema", getLanguage() viene vacío y los textos de JavaScript quedaban
// en inglés aunque el panel estuviera en español)
if ($thisstaff
        && ($lang = Internationalization::getCurrentLanguage($thisstaff))
        && 0 !== strcasecmp($lang, 'en_US')) { ?>
    <script type="text/javascript" src="ajax.php/i18n/<?php
        echo Format::htmlchars($lang); ?>/js?v=<?php echo app_i18n_version($lang); ?>"></script>
<?php } ?>
<?php app_theme_scripts('staff'); ?>
</body>
</html>
<?php } # endif X_PJAX ?>
