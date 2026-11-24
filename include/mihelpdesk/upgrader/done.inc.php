<?php
/* MiHelpdesk — Actualizador: terminado. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
require_once INCLUDE_DIR.'mihelpdesk/setup/funciones.php';
$_SESSION['ost_upgrader'] = null;
$idioma = $cfg ? $cfg->getPrimaryLanguage() : '';
?>
<div class="mh-upgrader">
<div class="mh-done-head">
    <div class="mh-done-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></div>
    <h1><?php echo __('The update is complete'); ?></h1>
    <p class="mh-lead"><?php echo Format::htmlchars(sprintf(__('The database is now up to date with %1$s %2$s.'), APP_NAME, THIS_VERSION)); ?></p>
    <div class="mh-actions"><a class="mh-btn" href="index.php"><?php echo __('Go to the control panel'); ?></a></div>
</div>
<section class="mh-section">
    <h2><?php echo __('Next steps'); ?></h2>
    <ol class="mh-todo">
        <li><strong><?php echo __('Update the texts to the new version'); ?></strong>
            <span class="mh-muted"><?php echo Format::htmlchars(__('Translates the pages, email templates, queues and statuses that came from the previous installation. It only changes texts that nobody edited, and it can be run more than once.')); ?></span>
            <div class="mh-cmds">
                <?php echo mh_setup_cmd(__('Preview the changes'), 'php tools/i18n/cargar-idioma.php '.($idioma ?: 'es_MX')); ?>
                <?php echo mh_setup_cmd(__('Apply them'), 'php tools/i18n/cargar-idioma.php '.($idioma ?: 'es_MX').' --aplicar'); ?>
            </div></li>
        <li><strong><?php echo __('Review your brand'); ?></strong>
            <span class="mh-muted"><?php echo sprintf(Format::htmlchars(__('Color, logos and portal home page are in %s.')),
                '<a href="customize.php">'.Format::htmlchars(__('Admin Panel').' › '.__('Settings').' › '.__('Customization')).'</a>'); ?></span></li>
        <li><strong><?php echo __('Delete the setup folder'); ?></strong>
            <span class="mh-muted"><?php echo Format::htmlchars(__('On a production server, delete it; the control panel will show a warning while it exists.')); ?></span></li>
    </ol>
</section>
</div>
<?php echo mh_setup_copy_script(); ?>
