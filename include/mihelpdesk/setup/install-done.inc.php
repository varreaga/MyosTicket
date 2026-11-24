<?php
/* MiHelpdesk — Instalador: instalación terminada. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
$url = URL;
?>
<div class="mh-done-head">
    <div class="mh-done-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg></div>
    <h1><?php echo Format::htmlchars(sprintf(__('%s is installed!'), APP_NAME)); ?></h1>
    <p class="mh-lead"><?php echo Format::htmlchars(__('You can now sign in to the control panel with the username and password you just created.')); ?></p>
    <div class="mh-actions">
        <a class="mh-btn" href="../scp/admin.php"><?php echo __('Go to the control panel'); ?></a>
        <a class="mh-btn mh-btn-secondary" href="../" target="_blank" rel="noopener"><?php echo __('View the customer portal'); ?></a>
    </div>
</div>
<div class="mh-links">
    <div><span><?php echo __('Customer portal'); ?></span><a href="../"><?php echo Format::htmlchars($url); ?></a></div>
    <div><span><?php echo __('Control panel for agents'); ?></span><a href="../scp/"><?php echo Format::htmlchars($url.'scp/'); ?></a></div>
</div>

<section class="mh-section">
    <h2><?php echo __('Protect your installation'); ?></h2>
    <ol class="mh-todo">
        <li><strong><?php echo __('Remove write access to the configuration file'); ?></strong>
            <span class="mh-muted"><?php echo sprintf(Format::htmlchars(__('The installer no longer needs to write to %s.')), '<code>include/ost-config.php</code>'); ?></span>
            <div class="mh-cmds">
                <?php echo mh_setup_cmd(__('Linux or macOS'), 'chmod 0644 include/ost-config.php'); ?>
                <?php echo mh_setup_cmd(__('Windows (PowerShell)'), 'icacls include\ost-config.php /reset'); ?>
            </div></li>
        <li><strong><?php echo __('Delete the setup folder'); ?></strong>
            <span class="mh-muted"><?php echo Format::htmlchars(__('On a production server, delete it; the control panel will show a warning while it exists.')); ?></span>
            <div class="mh-cmds">
                <?php echo mh_setup_cmd(__('Linux or macOS'), 'rm -rf setup'); ?>
                <?php echo mh_setup_cmd(__('Windows (PowerShell)'), 'Remove-Item -Recurse -Force setup'); ?>
            </div></li>
    </ol>
</section>

<section class="mh-section">
    <h2><?php echo __('First steps'); ?></h2>
    <ul class="mh-next">
        <li><a href="../scp/customize.php"><strong><?php echo __('Make it yours'); ?></strong>
            <span><?php echo __('Name, color, logos and customer portal home page.'); ?></span></a></li>
        <li><a href="../scp/emails.php"><strong><?php echo __('Set up email'); ?></strong>
            <span><?php echo __('Outgoing server (SMTP) and the mailbox your customers write to.'); ?></span></a></li>
        <li><a href="../scp/staff.php"><strong><?php echo __('Add your team'); ?></strong>
            <span><?php echo __('Agents, departments and permissions.'); ?></span></a></li>
        <li><a href="../scp/helptopics.php"><strong><?php echo __('Review help topics'); ?></strong>
            <span><?php echo __('What customers can choose when they open a request.'); ?></span></a></li>
    </ul>
</section>
