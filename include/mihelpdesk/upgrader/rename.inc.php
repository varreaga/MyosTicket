<?php
/* MiHelpdesk — Actualizador: cambiar el nombre del archivo de configuración. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
require_once INCLUDE_DIR.'mihelpdesk/setup/funciones.php';
?>
<div class="mh-upgrader">
<h1><?php echo __('Rename the configuration file'); ?></h1>
<p class="mh-lead"><?php echo sprintf(Format::htmlchars(__('Your installation uses the old name %1$s. Rename it to %2$s and continue.')),
    '<code>include/settings.php</code>', '<code>include/ost-config.php</code>'); ?></p>
<div class="mh-cmds">
    <?php echo mh_setup_cmd(__('Linux, macOS or Git Bash'), 'mv include/settings.php include/ost-config.php'); ?>
    <?php echo mh_setup_cmd(__('Windows (PowerShell)'), 'Rename-Item include\settings.php ost-config.php'); ?>
</div>
<form method="post" action="upgrade.php" class="mh-actions">
    <?php csrf_token(); ?>
    <input type="hidden" name="s" value="prereq">
    <button class="mh-btn" type="submit" name="submit"><?php echo __('Done, continue'); ?> →</button>
</form>
</div>
<?php echo mh_setup_copy_script(); ?>
