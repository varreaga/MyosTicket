<?php
/* MiHelpdesk — Actualizador: aplicar cambios. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

// Modo manual si las llamadas ajax fallan (igual que en osTicket)
if (($mode = $ost->get_var('m', $_GET)) && $mode != $upgrader->getMode()) {
    $upgrader->setMode($mode);
    if ($mode == 'manual')
        $ost->logWarning('Ajax calls are failing',
            'Make sure your server has AcceptPathInfo directive set to "ON" or get technical help');
}
$action = $upgrader->getNextAction();
?>
<div class="mh-upgrader" id="main">
<h1><?php echo Format::htmlchars(sprintf(__('Updating to %1$s %2$s'), APP_NAME, APP_VERSION)); ?></h1>
<p class="mh-lead"><?php echo Format::htmlchars(__('The database changes below will be applied. Do not close or reload this page until it finishes.')); ?></p>

<div class="mh-alert mh-alert-info"><strong id="task"><?php echo Format::htmlchars(sprintf(__('Changes to apply: %s'),
    $upgrader->getCurrentStream()->name)); ?></strong></div>
<?php echo $upgrader->getUpgradeSummary(); ?>

<form method="post" action="upgrade.php" id="upgrade" class="mh-actions">
    <?php csrf_token(); ?>
    <input type="hidden" name="s" value="upgrade">
    <input type="hidden" id="mode" name="m" value="<?php echo $upgrader->getMode(); ?>">
    <input type="hidden" name="sh" value="<?php echo $upgrader->getSchemaSignature(); ?>">
    <input class="mh-btn" type="submit" name="submit" value="<?php echo __('Update now'); ?>">
</form>
</div>

<div id="upgrading" class="mh-upgrading" role="status" aria-live="polite">
    <div class="mh-busy-card">
        <div class="mh-spinner" aria-hidden="true"></div>
        <h2 id="action"><?php echo $action; ?></h2>
        <p class="mh-muted"><?php echo Format::htmlchars(__('Updating the database… Please wait.')); ?></p>
        <div id="msg"></div>
    </div>
</div>
