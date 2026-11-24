<?php
/* MiHelpdesk — Instalador: falta el archivo de configuración. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
?>
<h1><?php echo __('The configuration file is missing'); ?></h1>
<p class="mh-lead"><?php echo sprintf(Format::htmlchars(__('The installer saves its settings in %1$s. Create it by copying the template %2$s:')),
    '<code>include/ost-config.php</code>', '<code>include/ost-sampleconfig.php</code>'); ?></p>
<?php if ($errors['err']) { ?><div class="mh-alert mh-alert-error" role="alert"><?php echo Format::htmlchars(__('The file still does not exist.')); ?></div><?php } ?>
<div class="mh-cmds">
    <?php echo mh_setup_cmd(__('Linux, macOS or Git Bash'), 'cp include/ost-sampleconfig.php include/ost-config.php'); ?>
    <?php echo mh_setup_cmd(__('Windows (PowerShell)'), 'Copy-Item include\ost-sampleconfig.php include\ost-config.php'); ?>
</div>
<p class="mh-muted"><?php echo Format::htmlchars(__('With a file manager or FTP: copy the file and rename the copy to ost-config.php.')); ?></p>
<form method="post" action="install.php" class="mh-actions">
    <input type="hidden" name="s" value="config">
    <button class="mh-btn" type="submit" name="submit"><?php echo __('Done, continue'); ?> →</button>
</form>
