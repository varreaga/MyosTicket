<?php
/* MiHelpdesk — Instalador: datos de la instalación. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
$info = ($_POST && $errors) ? Format::htmlchars($_POST) : array('prefix' => 'ost_', 'dbhost' => 'localhost',
    'lang_id' => (Internationalization::isLanguageInstalled('es_MX') ? 'es_MX' : 'en_US'));

/** Campo con etiqueta, ayuda y error */
function mh_setup_field($name, $label, $info, $errors, $opts = array()) {
    $type = @$opts['type'] ?: 'text';
    $err = @$errors[$name];
    $id = 'f-'.$name;
    $out = sprintf('<div class="mh-field%s"><label for="%s">%s</label>', $err ? ' has-error' : '', $id, Format::htmlchars($label));
    $out .= sprintf('<input type="%s" id="%s" name="%s" value="%s"%s%s%s>', $type, $id, $name,
        $type == 'password' && !$err ? '' : @$info[$name],
        isset($opts['auto']) ? ' autocomplete="'.$opts['auto'].'"' : '',
        $err ? ' aria-invalid="true" aria-describedby="'.$id.'-e"' : (isset($opts['hint']) ? ' aria-describedby="'.$id.'-h"' : ''),
        @$opts['attrs'] ?: '');
    if ($err)
        $out .= sprintf('<div class="mh-err" id="%s-e">%s</div>', $id, $err);
    elseif (isset($opts['hint']))
        $out .= sprintf('<div class="mh-hint" id="%s-h">%s</div>', $id, Format::htmlchars($opts['hint']));
    return $out.'</div>';
}
?>
<h1><?php echo Format::htmlchars(sprintf(__('Set up %s'), APP_NAME)); ?></h1>
<p class="mh-lead"><?php echo Format::htmlchars(__('Fill in these details to finish the installation. All fields are required.')); ?></p>

<?php if ($errors['err'] || $errors['db']) { ?>
<div class="mh-alert mh-alert-error" role="alert">
    <?php if ($errors['err']) echo '<strong>'.$errors['err'].'</strong>'; ?>
    <?php if ($errors['db']) echo '<p>'.$errors['db'].'</p>'; ?>
</div>
<?php } ?>

<form action="install.php" method="post" id="install" novalidate>
    <input type="hidden" name="s" value="install">
    <input type="hidden" name="timezone" id="timezone">

    <section class="mh-section">
        <h2><?php echo __('Your helpdesk'); ?></h2>
        <p><?php echo Format::htmlchars(__('How your customers will see it.')); ?></p>
        <div class="mh-field"><label><?php echo __('Helpdesk address'); ?></label>
            <div class="mh-static"><?php echo Format::htmlchars(URL); ?></div></div>
        <?php echo mh_setup_field('name', __('Helpdesk name'), $info, $errors, array(
            'hint' => __('Shown on the customer portal, in emails and in the browser title. For example, your company name.'))); ?>
        <?php echo mh_setup_field('email', __('Support email'), $info, $errors, array('type' => 'email', 'auto' => 'off',
            'hint' => __('The address your customers write to, for example support@yourcompany.com.'))); ?>
        <div class="mh-field<?php if ($errors['lang_id']) echo ' has-error'; ?>">
            <label for="f-lang"><?php echo __('Primary language'); ?></label>
            <select name="lang_id" id="f-lang">
<?php foreach (Internationalization::availableLanguages() as $l) { ?>
                <option value="<?php echo $l['code']; ?>" <?php if ($info['lang_id'] == $l['code']) echo 'selected';
                    ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
<?php } ?>
            </select>
            <?php if ($errors['lang_id']) echo '<div class="mh-err">'.$errors['lang_id'].'</div>';
            else echo '<div class="mh-hint">'.Format::htmlchars(__('Used for emails, pages and sample data.')).'</div>'; ?>
        </div>
    </section>

    <section class="mh-section">
        <h2><?php echo __('Administrator account'); ?></h2>
        <p><?php echo Format::htmlchars(__('You will use this account to sign in to the control panel. You can add more agents later.')); ?></p>
        <div class="mh-grid">
            <?php echo mh_setup_field('fname', __('First Name'), $info, $errors, array('auto' => 'given-name')); ?>
            <?php echo mh_setup_field('lname', __('Last Name'), $info, $errors, array('auto' => 'family-name')); ?>
        </div>
        <?php echo mh_setup_field('admin_email', __('Your email'), $info, $errors, array('type' => 'email', 'auto' => 'email',
            'hint' => __('Must be different from the support email.'))); ?>
        <?php echo mh_setup_field('username', __('Username'), $info, $errors, array('auto' => 'off',
            'hint' => __('Without spaces. It cannot be “admin”.'))); ?>
        <div class="mh-grid">
            <?php echo mh_setup_field('passwd', __('Password'), $info, $errors, array('type' => 'password', 'auto' => 'new-password',
                'attrs' => ' maxlength="128"')); ?>
            <?php echo mh_setup_field('passwd2', __('Repeat password'), $info, $errors, array('type' => 'password', 'auto' => 'new-password',
                'attrs' => ' maxlength="128"')); ?>
        </div>
    </section>

    <section class="mh-section">
        <h2><?php echo __('Database'); ?></h2>
        <p><?php echo Format::htmlchars(__('Create an empty database and a user with permissions on it first (for example, in phpMyAdmin).')); ?></p>
        <?php echo mh_setup_field('dbhost', __('Database server'), $info, $errors, array(
            'hint' => __('Usually localhost.'))); ?>
        <?php echo mh_setup_field('dbname', __('Database name'), $info, $errors); ?>
        <div class="mh-grid">
            <?php echo mh_setup_field('dbuser', __('Database user'), $info, $errors, array('auto' => 'off')); ?>
            <?php echo mh_setup_field('dbpass', __('Database password'), $info, $errors, array('type' => 'password', 'auto' => 'off')); ?>
        </div>
        <details class="mh-advanced"<?php if ($errors['prefix']) echo ' open'; ?>>
            <summary><?php echo __('Advanced options'); ?></summary>
            <?php echo mh_setup_field('prefix', __('Table prefix'), $info, $errors, array(
                'hint' => __('Change it only if the database is shared with another installation. It must end with _'))); ?>
        </details>
    </section>

    <div class="mh-actions">
        <button class="mh-btn" type="submit"><?php echo __('Install now'); ?></button>
    </div>
</form>

<div class="mh-busy" id="mh-busy" role="status" aria-live="polite">
    <div class="mh-busy-card">
        <div class="mh-spinner" aria-hidden="true"></div>
        <h2><?php echo __('Installing…'); ?></h2>
        <p class="mh-muted"><?php echo Format::htmlchars(__('Creating the tables and settings. This takes less than a minute; please do not close this page.')); ?></p>
    </div>
</div>
