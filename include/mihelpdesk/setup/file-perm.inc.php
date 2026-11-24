<?php
/* MiHelpdesk — Instalador: no se puede escribir el archivo de configuración. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
?>
<h1><?php echo __('The configuration file cannot be written'); ?></h1>
<p class="mh-lead"><?php echo sprintf(Format::htmlchars(__('The installer needs to write to %s. Give the web server write access to it:')),
    '<code>include/ost-config.php</code>'); ?></p>
<?php if ($errors['err']) { ?><div class="mh-alert mh-alert-error" role="alert"><?php echo Format::htmlchars(__('The file still cannot be written.')); ?></div><?php } ?>
<div class="mh-cmds">
    <?php echo mh_setup_cmd(__('Linux or macOS'), 'chmod 0666 include/ost-config.php'); ?>
    <?php echo mh_setup_cmd(__('Windows (PowerShell)'), "icacls include\\ost-config.php /grant 'Everyone:F'"); ?>
</div>
<p class="mh-muted"><?php echo Format::htmlchars(__('When the installation finishes, we will remind you to remove that access.')); ?></p>
<form method="post" action="install.php" class="mh-actions">
    <input type="hidden" name="s" value="config">
    <button class="mh-btn" type="submit" name="submit"><?php echo __('Done, continue'); ?> →</button>
</form>
