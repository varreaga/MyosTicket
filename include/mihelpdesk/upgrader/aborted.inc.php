<?php
/* MiHelpdesk — Actualizador: detenido por errores. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
?>
<div class="mh-upgrader">
<h1><?php echo __('The update stopped'); ?></h1>
<p class="mh-lead"><?php echo Format::htmlchars(__('An error occurred while updating the database, so the update was stopped.')); ?></p>
<div class="mh-alert mh-alert-error" role="alert">
<?php
if ($upgrader && ($errs = $upgrader->getErrors())) {
    if ($errs['err']) echo '<strong>'.$errs['err'].'</strong>';
    unset($errs['err']);
    if ($errs) {
        echo '<ul>';
        foreach ($errs as $e) echo '<li>'.$e.'</li>';
        echo '</ul>';
    }
} else {
    echo '<strong>'.__('Internal error occurred').'</strong>';
}
?>
</div>
<h2><?php echo __('What to do?'); ?></h2>
<ol class="mh-todo">
    <li><strong><?php echo __('Write down the error'); ?></strong>
        <span class="mh-muted"><?php echo sprintf(Format::htmlchars(__('You can see the details in %s.')),
            '<a href="logs.php">'.__('System Logs').'</a>'); ?></span></li>
    <li><strong><?php echo __('Restore your backup'); ?></strong>
        <span class="mh-muted"><?php echo Format::htmlchars(__('Restore the database and the previous files, then try again or get technical help.')); ?></span></li>
</ol>
</div>
