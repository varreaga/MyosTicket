<?php
/* MiHelpdesk — Instalador: requisitos del servidor. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');

$required = array(
    array(sprintf(__('PHP %s or newer'), SetupWizard::getPHPVersion()), '',
        $installer->check_php(), PHP_VERSION),
    array(__('MySQLi extension'), __('Connects to the MySQL or MariaDB database'),
        $installer->check_mysql(), null),
);
$recommended = array(
    array('GD', 'gd', __('Logos, favicon and images')),
    array('Intl', 'intl', __('Dates, numbers and language')),
    array('Mbstring', 'mbstring', __('Text with accents and other alphabets')),
    array('iconv', 'iconv', __('Emails in other character sets')),
    array('ctype', 'ctype', __('Fetching emails')),
    array('XML', 'xml', __('XML API')),
    array('DOM', 'dom', __('HTML emails')),
    array('JSON', 'json', __('Faster performance')),
    array('Phar', 'phar', __('Language packs and plugins')),
    array('OPcache', 'Zend OPcache', __('Faster performance')),
    array('APCu', 'apcu', __('Faster performance (optional)')),
);
$req_ok = true;
foreach ($required as $r) $req_ok = $req_ok && $r[2];
$missing = 0;
foreach ($recommended as $r) if (!extension_loaded($r[1])) $missing++;
?>
<h1><?php echo Format::htmlchars(sprintf(__('Welcome to %s'), APP_NAME)); ?></h1>
<p class="mh-lead"><?php echo Format::htmlchars(__('Your helpdesk will be ready in a few minutes. First, let’s check that your server has everything it needs.')); ?></p>

<?php if (!$req_ok || $errors['prereq']) { ?>
<div class="mh-alert mh-alert-error" role="alert">
    <strong><?php echo Format::htmlchars(__('Your server does not meet the minimum requirements.')); ?></strong>
    <?php echo Format::htmlchars(__('Fix the items marked in red and reload this page.')); ?>
</div>
<?php } elseif ($missing) { ?>
<div class="mh-alert mh-alert-info">
    <strong><?php echo Format::htmlchars(__('Your server meets the minimum requirements.')); ?></strong>
    <?php echo Format::htmlchars(sprintf(_N('You can continue. %d recommended extension is missing; some features may not work.',
        'You can continue. %d recommended extensions are missing; some features may not work.', $missing), $missing)); ?>
</div>
<?php } else { ?>
<div class="mh-alert mh-alert-ok">
    <strong><?php echo Format::htmlchars(__('Your server meets all the requirements.')); ?></strong>
</div>
<?php } ?>

<h2><?php echo __('Required to install'); ?></h2>
<ul class="mh-checks">
<?php foreach ($required as $r) {
    list($name, $desc, $ok, $state) = $r;
    $state = $state ?: ($ok ? __('Loaded') : __('Missing')); ?>
    <li class="<?php echo $ok ? 'is-ok' : 'is-fail'; ?>">
        <span class="mh-check-icon" aria-hidden="true"></span>
        <span class="mh-check-name"><?php echo Format::htmlchars($name);
            if ($desc) echo '<span class="mh-check-desc">'.Format::htmlchars($desc).'</span>'; ?></span>
        <span class="mh-check-state"><?php echo Format::htmlchars($state); ?></span>
    </li>
<?php } ?>
</ul>

<h2><?php echo __('Recommended extensions'); ?></h2>
<p class="mh-muted"><?php echo Format::htmlchars(sprintf(__('%s works without these PHP extensions, but some features need them.'), APP_NAME)); ?></p>
<ul class="mh-checks">
<?php foreach ($recommended as $r) {
    list($name, $ext, $desc) = $r;
    $ok = extension_loaded($ext); ?>
    <li class="<?php echo $ok ? 'is-ok' : 'is-missing'; ?>">
        <span class="mh-check-icon" aria-hidden="true"></span>
        <span class="mh-check-name"><?php echo Format::htmlchars($name); ?>
            <span class="mh-check-desc"><?php echo Format::htmlchars($desc); ?></span></span>
        <span class="mh-check-state"><?php echo Format::htmlchars($ok ? __('Loaded') : __('Not installed')); ?></span>
    </li>
<?php } ?>
</ul>
<?php if ($missing) { ?>
<p class="mh-muted" style="margin-top:12px"><?php echo sprintf(Format::htmlchars(__('To enable an extension, remove the %1$s at the start of its line in %2$s (for example %3$s) and restart the web server.')),
    '<code>;</code>', '<code>php.ini</code>', '<code>extension=intl</code>'); ?></p>
<?php } ?>

<form method="post" action="install.php" class="mh-actions">
    <input type="hidden" name="s" value="prereq">
    <button class="mh-btn" type="submit" name="submit"><?php echo __('Continue'); ?> →</button>
</form>
