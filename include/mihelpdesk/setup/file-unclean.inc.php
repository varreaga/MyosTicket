<?php
/* MiHelpdesk — Instalador: ya instalado. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
?>
<h1><?php echo Format::htmlchars(sprintf(__('%s is already installed'), APP_NAME)); ?></h1>
<p class="mh-lead"><?php echo sprintf(Format::htmlchars(__('The file %s already has a configuration.')), '<code>include/ost-config.php</code>'); ?></p>
<p><?php echo Format::htmlchars(__('To update to a new version, sign in to the control panel. If you think this is a mistake, replace the file with a fresh copy of include/ost-sampleconfig.php and try again.')); ?></p>
<div class="mh-actions">
    <a class="mh-btn" href="../scp/"><?php echo __('Go to the control panel'); ?></a>
    <a class="mh-btn mh-btn-secondary" href="install.php"><?php echo __('Check again'); ?></a>
</div>
