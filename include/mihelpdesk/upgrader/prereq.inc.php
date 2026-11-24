<?php
/* MiHelpdesk — Actualizador: requisitos. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
require_once INCLUDE_DIR.'mihelpdesk/setup/funciones.php';
$pendiente = $ost->isUpgradePending() && $upgrader->isUpgradable();
?>
<div class="mh-upgrader">
<h1><?php echo __('Update the database'); ?></h1>
<p class="mh-lead"><?php echo Format::htmlchars(sprintf(__('This version of %s needs to update the database. Your requests, users and settings are kept.'), APP_NAME)); ?></p>

<?php if ($errors['err']) { ?>
<div class="mh-alert <?php echo $ost->isUpgradePending() ? 'mh-alert-error' : 'mh-alert-ok'; ?>" role="alert"><?php echo $errors['err']; ?></div>
<?php } ?>
<?php if ($errors['prereq']) { ?>
<div class="mh-alert mh-alert-error" role="alert"><strong><?php echo Format::htmlchars(__('Your server does not meet the minimum requirements.')); ?></strong>
    <?php echo Format::htmlchars(__('Fix the items marked in red and reload this page.')); ?></div>
<?php } ?>

<h2><?php echo __('Required to install'); ?></h2>
<?php echo mh_setup_checks(array(
    array(sprintf(__('PHP %s or newer'), SetupWizard::getPHPVersion()), '', $upgrader->check_php(), PHP_VERSION),
    array(__('MySQLi extension'), __('Connects to the MySQL or MariaDB database'), $upgrader->check_mysql(),
        $upgrader->check_mysql() ? __('Loaded') : __('Missing')),
    array(sprintf(__('MySQL %s or newer'), SetupWizard::getMySQLVersion()), '', $upgrader->check_mysql_version(), db_version()),
)); ?>

<?php if ($pendiente) { ?>
<section class="mh-section">
    <h2><?php echo __('Before you start'); ?></h2>
    <ol class="mh-todo">
        <li><strong><?php echo __('Back up the database'); ?></strong>
            <span class="mh-muted"><?php echo Format::htmlchars(__('If something goes wrong, you can restore it. For example, from phpMyAdmin (Export) or with:')); ?></span>
            <div class="mh-cmds"><?php echo mh_setup_cmd(__('Linux, macOS or Git Bash'), 'mysqldump -u USER -p DATABASE > backup.sql'); ?></div></li>
        <li><strong><?php echo __('Keep this page open'); ?></strong>
            <span class="mh-muted"><?php echo Format::htmlchars(__('The update usually takes less than a minute.')); ?></span></li>
    </ol>
</section>
<form method="post" action="upgrade.php" id="prereq" class="mh-actions">
    <?php csrf_token(); ?>
    <input type="hidden" name="s" value="prereq">
    <button class="mh-btn" type="submit" name="submit"><?php echo __('Start the update'); ?> →</button>
</form>
<?php } else { ?>
<div class="mh-actions"><a class="mh-btn" href="index.php"><?php echo __('Go to the control panel'); ?></a></div>
<?php } ?>
</div>
<?php echo mh_setup_copy_script(); ?>
