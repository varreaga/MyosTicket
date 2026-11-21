<?php
/*
 * MiHelpdesk — Pantalla de Personalización (Administración › Configuración).
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2. Archivo nuevo, no existe
 * en osTicket. Variables: $mh_tab, $errors, $cfg.
 */
if (!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
require_once INCLUDE_DIR . 'mihelpdesk/class.brand.php';

$P = $_POST ?: array();
// Valor del formulario: lo enviado (si hubo error) o lo guardado
$val = function ($key, $stored) use ($P) {
    return Format::htmlchars($P ? ($P[$key] ?? '') : $stored);
};
$err = function ($key) use ($errors) {
    return !empty($errors[$key]) ? '<div class="error">' . Format::htmlchars($errors[$key]) . '</div>' : '';
};
$tabNames = array(
    'brand' => array(__('Brand'), 'icon-tint'),
    'home' => array(__('Portal home'), 'icon-home'),
    'nav' => array(__('Menu and footer'), 'icon-list'),
    'login' => array(__('Sign in'), 'icon-signin'),
    'email' => array(__('Emails'), 'icon-envelope'),
);
?>
<h2><?php echo __('Customization'); ?></h2>
<p class="mh-cz-intro"><?php echo __('Change how the support center looks for your customers and agents, without programming. Each tab is saved on its own.'); ?></p>

<ul class="clean tabs mh-cz-tabs">
<?php foreach ($tabNames as $k => $t) { ?>
    <li class="<?php echo $k == $mh_tab ? 'active' : ''; ?>"><a class="no-pjax" href="customize.php?tab=<?php echo $k; ?>"><i class="<?php echo $t[1]; ?>"></i> <?php echo $t[0]; ?></a></li>
<?php } ?>
</ul>

<form action="customize.php?tab=<?php echo $mh_tab; ?>" method="post" class="mh-cz" enctype="multipart/form-data">
<?php csrf_token(); ?>
<input type="hidden" name="tab" value="<?php echo $mh_tab; ?>">
<div class="tab_content mh-cz-body">

<?php if ($mh_tab == 'brand') {
    $logos = AttachmentFile::allLogos();
    $sel = array(
        'logo_client' => $P ? ($P['logo_client'] ?? 0) : $cfg->getClientLogoId(),
        'logo_staff' => $P ? ($P['logo_staff'] ?? 0) : $cfg->getStaffLogoId(),
        'logo_dark_client' => $P ? ($P['logo_dark_client'] ?? 0) : MhCustomize::get('logo_dark_client_id'),
        'logo_dark_staff' => $P ? ($P['logo_dark_staff'] ?? 0) : MhCustomize::get('logo_dark_staff_id'),
    );
    $color = $P ? ($P['mh_brand_color'] ?? '') : MhBrand::getConfigured();
    $cols = array(
        'logo_client' => __('Portal'),
        'logo_dark_client' => __('Portal (dark mode)'),
        'logo_staff' => __('Agent panel'),
        'logo_dark_staff' => __('Agent panel (dark mode)'),
    );
    $favicon = MhCustomize::getFavicon();
?>
    <section class="mh-cz-section">
        <h3><?php echo __('Name and color'); ?></h3>
        <div class="mh-cz-field">
            <label for="helpdesk_title"><?php echo __('Support center name'); ?></label>
            <input type="text" id="helpdesk_title" name="helpdesk_title" maxlength="255"
                value="<?php echo $val('helpdesk_title', $cfg->getTitle()); ?>">
            <p class="mh-cz-hint"><?php echo __('Shown in the browser tab and in emails. Example: ACME Support Center.'); ?></p>
            <?php echo $err('helpdesk_title'); ?>
        </div>
        <div class="mh-cz-field">
            <label for="mh_brand_color"><?php echo __('Brand color'); ?></label>
            <div class="mh-brand-field">
                <input type="color" id="mh_brand_picker" aria-label="<?php echo __('Pick a color'); ?>"
                    value="<?php echo Format::htmlchars(MhBrand::normalize($color) ?: MhBrand::DEFAULT_COLOR); ?>">
                <input type="text" name="mh_brand_color" id="mh_brand_color" size="22"
                    value="<?php echo Format::htmlchars($color); ?>" placeholder="<?php echo MhBrand::DEFAULT_COLOR; ?>">
            </div>
            <p class="mh-cz-hint"><?php echo sprintf(__('Hex (%1$s) or RGB (%2$s). Leave empty to use the default color.'),
                '#0072bc', 'rgb(0, 114, 188)'); ?> <?php echo __('Lighter and darker shades are calculated from it.'); ?></p>
            <?php echo $err('mh_brand_color'); ?>
        </div>
    </section>

    <section class="mh-cz-section">
        <h3><?php echo __('Logos'); ?></h3>
        <p class="mh-cz-hint"><?php echo __('Upload your logos and choose where each one is used. The dark mode logo is used when the person has dark mode on; if you do not choose one, the normal logo is used.'); ?></p>
        <div class="mh-cz-logos">
            <table class="mh-cz-logo-table">
                <thead><tr>
                    <th><?php echo __('Logo'); ?></th>
                    <th><?php echo __('Portal'); ?></th>
                    <th><?php echo __('Portal (dark mode)'); ?></th>
                    <th><?php echo __('Agent panel'); ?></th>
                    <th><?php echo __('Agent panel (dark mode)'); ?></th>
                    <th><?php echo __('Delete'); ?></th>
                </tr></thead>
                <tbody>
                <tr>
                    <td><span class="mh-cz-logo-default"><?php echo __('Default logo'); ?></span></td>
<?php foreach ($cols as $k => $label) { ?>
                    <td data-label="<?php echo Format::htmlchars($label); ?>"><input type="radio" name="<?php echo $k; ?>" value="0"
                        aria-label="<?php echo Format::htmlchars($label.' · '.__('Default logo')); ?>" <?php echo !$sel[$k] ? 'checked' : ''; ?>></td>
<?php } ?>
                    <td class="mh-cz-empty-cell"></td>
                </tr>
<?php foreach ($logos as $L) { $id = $L->getId(); $used = in_array($id, $sel); ?>
                <tr>
                    <td>
                        <span class="mh-cz-logo-thumbs">
                            <img src="<?php echo $L->getDownloadUrl(); ?>" alt="" class="mh-cz-on-light">
                            <img src="<?php echo $L->getDownloadUrl(); ?>" alt="" class="mh-cz-on-dark">
                        </span>
                        <small><?php echo Format::htmlchars($L->getName()); ?></small>
                    </td>
<?php foreach ($cols as $k => $label) { ?>
                    <td data-label="<?php echo Format::htmlchars($label); ?>"><input type="radio" name="<?php echo $k; ?>" value="<?php echo $id; ?>"
                        aria-label="<?php echo Format::htmlchars($label.' · '.$L->getName()); ?>" <?php echo $sel[$k] == $id ? 'checked' : ''; ?>></td>
<?php } ?>
                    <td data-label="<?php echo __('Delete'); ?>"><?php if (!$used) { ?><input type="checkbox" name="delete_logo[]" value="<?php echo $id; ?>"
                        aria-label="<?php echo __('Delete'); ?>"><?php } ?></td>
                </tr>
<?php } ?>
                </tbody>
            </table>
        </div>
        <div class="mh-cz-field">
            <label for="logo_file"><?php echo __('Upload a new logo'); ?></label>
            <input type="file" id="logo_file" name="logo[]" accept="image/png,image/jpeg,image/gif,image/webp">
            <p class="mh-cz-hint"><?php echo __('PNG, JPG, GIF or WEBP, at least twice as wide as it is tall. A transparent background looks best in light and dark mode.'); ?></p>
            <?php echo $err('logo'); ?>
        </div>
    </section>

    <section class="mh-cz-section">
        <h3><?php echo __('Browser icon (favicon)'); ?></h3>
        <div class="mh-cz-favicon">
            <img src="<?php echo ROOT_PATH; ?>logo.php?favicon&amp;v=<?php echo $favicon ? $favicon->getId() : 0; ?>" alt="" width="32" height="32">
            <span><?php echo $favicon ? Format::htmlchars($favicon->getName()) : __('Default icon'); ?></span>
<?php if ($favicon) { ?>
            <label class="checkbox inline"><input type="checkbox" name="delete_favicon" value="1"> <?php echo __('Use the default icon'); ?></label>
<?php } ?>
        </div>
        <div class="mh-cz-field">
            <label for="favicon_file"><?php echo __('Upload an icon'); ?></label>
            <input type="file" id="favicon_file" name="favicon[]" accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/gif,image/webp,image/jpeg">
            <p class="mh-cz-hint"><?php echo __('Square image, ideally 64 × 64 pixels, PNG or ICO, up to 512 KB.'); ?></p>
            <?php echo $err('favicon'); ?>
        </div>
    </section>

<?php } elseif ($mh_tab == 'home') {
    $landing = $cfg->getLandingPage();
    $fields = array(
        'home_open_title' => array(__('Title of the "open" card'), __('Open a New Ticket')),
        'home_open_desc' => array(__('Description of the "open" card'), __('Tell us about your problem or request. You will get a ticket number to follow up.')),
        'home_status_title' => array(__('Title of the "check" card'), __('Check Ticket Status')),
        'home_status_desc' => array(__('Description of the "check" card'), __('Check the status of your requests and reply to our team.')),
    );
?>
    <div class="mh-cz-split">
    <div>
    <section class="mh-cz-section">
        <h3><?php echo __('Welcome text'); ?></h3>
        <p class="mh-cz-hint"><?php echo __('Shown at the top of the portal home. Use a heading for the title and a paragraph for the subtitle.'); ?></p>
<?php if ($landing) { ?>
        <textarea name="landing_body" class="richtext" rows="8" data-root-context="landing"><?php
            echo $P ? Format::htmlchars($P['landing_body'] ?? '') : Format::viewableImages($landing->getBody()); ?></textarea>
        <?php echo $err('landing'); ?>
<?php } else { ?>
        <p class="mh-cz-hint"><?php echo __('There is no home page selected. Choose one in Settings › Company › Site Pages.'); ?></p>
<?php } ?>
    </section>
    <section class="mh-cz-section">
        <h3><?php echo __('Home cards'); ?></h3>
        <p class="mh-cz-hint"><?php echo __('Leave a field empty to use the default text, which is shown in the language of each person.'); ?></p>
<?php foreach ($fields as $k => $f) { ?>
        <div class="mh-cz-field">
            <label for="<?php echo $k; ?>"><?php echo $f[0]; ?></label>
<?php if (substr($k, -4) == 'desc') { ?>
            <textarea id="<?php echo $k; ?>" name="<?php echo $k; ?>" rows="2" maxlength="200"
                placeholder="<?php echo Format::htmlchars($f[1]); ?>" data-preview="<?php echo $k; ?>"><?php echo $val($k, MhCustomize::get($k)); ?></textarea>
<?php } else { ?>
            <input type="text" id="<?php echo $k; ?>" name="<?php echo $k; ?>" maxlength="80"
                placeholder="<?php echo Format::htmlchars($f[1]); ?>" data-preview="<?php echo $k; ?>"
                value="<?php echo $val($k, MhCustomize::get($k)); ?>">
<?php } ?>
        </div>
<?php } ?>
    </section>
    <section class="mh-cz-section">
        <h3><?php echo __('Sections'); ?></h3>
<?php foreach (array(
        'home_show_search' => __('Knowledge base search box'),
        'home_show_popular' => __('Popular articles'),
        'home_show_categories' => __('Knowledge base categories'),
    ) as $k => $label) {
    $on = $P ? isset($P[$k]) : MhCustomize::isOn($k); ?>
        <label class="mh-cz-check"><input type="checkbox" name="<?php echo $k; ?>" value="1" data-preview="<?php echo $k; ?>" <?php echo $on ? 'checked' : ''; ?>> <?php echo $label; ?></label>
<?php } ?>
        <p class="mh-cz-hint"><?php echo __('Articles and categories only appear if the knowledge base is enabled and has public content.'); ?></p>
    </section>
    </div>

    <aside class="mh-cz-preview" aria-label="<?php echo __('Preview'); ?>">
        <h3><?php echo __('Preview'); ?></h3>
        <div class="mh-cz-mock">
            <div class="mh-cz-mock-bar"><img src="<?php echo ROOT_PATH; ?>logo.php" alt=""></div>
            <div class="mh-cz-mock-hero">
                <div class="mh-cz-mock-lines"><span></span><span></span></div>
                <div class="mh-cz-mock-search" data-show="home_show_search"><?php echo __('Search our knowledge base'); ?></div>
            </div>
            <div class="mh-cz-mock-cards">
                <div><b data-text="home_open_title"><?php echo Format::htmlchars($fields['home_open_title'][1]); ?></b><small data-text="home_open_desc"></small></div>
                <div><b data-text="home_status_title"><?php echo Format::htmlchars($fields['home_status_title'][1]); ?></b><small data-text="home_status_desc"></small></div>
            </div>
            <div class="mh-cz-mock-kb">
                <div data-show="home_show_popular"><b><?php echo __('Popular articles'); ?></b><span></span><span></span></div>
                <div data-show="home_show_categories"><b><?php echo __('Categories'); ?></b><span></span><span></span></div>
            </div>
        </div>
        <a class="mh-cz-open" href="<?php echo ROOT_PATH; ?>index.php" target="_blank" rel="noopener"><?php echo __('Open the portal'); ?> ↗</a>
    </aside>
    </div>

<?php } elseif ($mh_tab == 'nav') {
    $pages = MhCustomize::availablePages();
    $navSel = $P ? array_map('intval', (array) ($P['nav_pages'] ?? array())) : array_map('intval', MhCustomize::getJson('nav_pages'));
    $footSel = $P ? array_map('intval', (array) ($P['footer_pages'] ?? array())) : array_map('intval', MhCustomize::getJson('footer_pages'));
    $links = $P ? array_map(null, (array) ($P['link_label'] ?? array()), (array) ($P['link_url'] ?? array()))
        : array_map(function ($l) { return array($l['label'], $l['url']); }, MhCustomize::getFooterLinks());
    $links = array_pad($links, max(count($links) + 2, 4), array('', ''));
?>
    <section class="mh-cz-section">
        <h3><?php echo __('Your pages'); ?></h3>
        <p class="mh-cz-hint"><?php echo sprintf(__('Create pages such as "Opening hours" or "Privacy notice" in %s (type "Other"). Then choose here where each one appears.'),
            '<a href="pages.php">' . __('Manage › Pages') . '</a>'); ?></p>
<?php if (!count($pages)) { ?>
        <p class="mh-cz-empty"><?php echo __('There are no active pages of type "Other" yet.'); ?></p>
<?php } else { ?>
        <table class="mh-cz-pages">
            <thead><tr><th><?php echo __('Page'); ?></th><th><?php echo __('Portal menu'); ?></th><th><?php echo __('Footer'); ?></th><th></th></tr></thead>
            <tbody>
<?php foreach ($pages as $Pg) { $id = $Pg->getId(); ?>
            <tr>
                <td><?php echo Format::htmlchars($Pg->getLocalName()); ?></td>
                <td><input type="checkbox" name="nav_pages[]" value="<?php echo $id; ?>" aria-label="<?php echo __('Portal menu'); ?>" <?php echo in_array($id, $navSel) ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="footer_pages[]" value="<?php echo $id; ?>" aria-label="<?php echo __('Footer'); ?>" <?php echo in_array($id, $footSel) ? 'checked' : ''; ?>></td>
                <td><a href="<?php echo MhCustomize::pageUrl($Pg); ?>" target="_blank" rel="noopener"><?php echo __('Open page'); ?> ↗</a></td>
            </tr>
<?php } ?>
            </tbody>
        </table>
<?php } ?>
    </section>
    <section class="mh-cz-section">
        <h3><?php echo __('Footer links'); ?></h3>
        <p class="mh-cz-hint"><?php echo __('Links to your website, social networks, WhatsApp, phone or email. Addresses must start with https://, mailto: or tel:.'); ?></p>
        <div class="mh-cz-links">
            <div class="mh-cz-links-head"><span><?php echo __('Text'); ?></span><span><?php echo __('Address'); ?></span></div>
<?php foreach ($links as $i => $l) { ?>
            <div class="mh-cz-link-row">
                <input type="text" name="link_label[]" maxlength="60" value="<?php echo Format::htmlchars($l[0] ?? ''); ?>"
                    placeholder="<?php echo $i == 0 ? __('WhatsApp') : ''; ?>" aria-label="<?php echo __('Text'); ?>">
                <input type="text" name="link_url[]" maxlength="500" value="<?php echo Format::htmlchars($l[1] ?? ''); ?>"
                    placeholder="<?php echo $i == 0 ? 'https://wa.me/5215512345678' : ''; ?>" aria-label="<?php echo __('Address'); ?>">
            </div>
<?php } ?>
        </div>
        <?php echo $err('links'); ?>
        <p class="mh-cz-hint"><?php echo __('To add more links, save and new empty rows will appear.'); ?></p>
    </section>
    <section class="mh-cz-section">
        <h3><?php echo __('Footer text'); ?></h3>
        <div class="mh-cz-field">
            <textarea name="footer_text" rows="2" maxlength="300"
                placeholder="<?php echo __('Example: Support hours Monday to Friday from 9:00 to 18:00.'); ?>"><?php echo $val('footer_text', MhCustomize::get('footer_text')); ?></textarea>
            <p class="mh-cz-hint"><?php echo __('Shown in the portal footer, above the copyright line.'); ?></p>
        </div>
    </section>

<?php } elseif ($mh_tab == 'login') {
    $staffPage = Page::lookupByType('banner-staff');
    $clientPage = Page::lookupByType('banner-client');
?>
    <section class="mh-cz-section">
        <h3><?php echo __('Agent panel sign in'); ?></h3>
        <div class="mh-cz-field">
            <label for="staff_login_title"><?php echo __('Title'); ?></label>
            <input type="text" id="staff_login_title" name="staff_login_title" maxlength="80"
                placeholder="<?php echo __('Sign in to the agent panel'); ?>"
                value="<?php echo $val('staff_login_title', MhCustomize::get('staff_login_title')); ?>">
            <p class="mh-cz-hint"><?php echo __('Leave it empty to use the default text.'); ?></p>
        </div>
<?php if ($staffPage) { ?>
        <div class="mh-cz-field">
            <label><?php echo __('Text below the title (optional)'); ?></label>
            <textarea name="staff_login_body" class="richtext no-bar" rows="3"><?php
                echo $P ? Format::htmlchars($P['staff_login_body'] ?? '') : Format::viewableImages($staffPage->getBody()); ?></textarea>
            <?php echo $err('banner-staff'); ?>
        </div>
<?php } ?>
    </section>
<?php if ($clientPage) { ?>
    <section class="mh-cz-section">
        <h3><?php echo __('Portal sign in'); ?></h3>
        <div class="mh-cz-field">
            <label for="client_login_title"><?php echo __('Title'); ?></label>
            <input type="text" id="client_login_title" name="client_login_title" maxlength="80"
                value="<?php echo $val('client_login_title', $clientPage->getName()); ?>">
        </div>
        <div class="mh-cz-field">
            <label><?php echo __('Text below the title'); ?></label>
            <textarea name="client_login_body" class="richtext no-bar" rows="3"><?php
                echo $P ? Format::htmlchars($P['client_login_body'] ?? '') : Format::viewableImages($clientPage->getBody()); ?></textarea>
            <?php echo $err('banner-client'); ?>
        </div>
    </section>
<?php } ?>

<?php } elseif ($mh_tab == 'email') {
    $on = $P ? isset($P['email_branding']) : MhCustomize::isOn('email_branding');
?>
    <div class="mh-cz-split">
    <div>
    <section class="mh-cz-section">
        <h3><?php echo __('Branded emails'); ?></h3>
        <label class="mh-cz-check"><input type="checkbox" name="email_branding" value="1" <?php echo $on ? 'checked' : ''; ?>>
            <?php echo __('Add a header with your logo and brand color to every email'); ?></label>
        <p class="mh-cz-hint"><?php echo __('Uses the portal logo (or the company name if there is none) and the brand color. The logo travels inside the email, so it is shown even without internet images.'); ?></p>
        <div class="mh-cz-field">
            <label for="email_footer"><?php echo __('Email footer'); ?></label>
            <textarea id="email_footer" name="email_footer" rows="3" maxlength="400"
                placeholder="<?php echo Format::htmlchars((string) $ost->company ?: $cfg->getTitle()); ?>"><?php echo $val('email_footer', MhCustomize::get('email_footer')); ?></textarea>
            <p class="mh-cz-hint"><?php echo __('Example: company name, address and phone. Leave it empty to show the company name.'); ?></p>
        </div>
        <p class="mh-cz-hint"><?php echo sprintf(__('The text of each email is edited in %s.'),
            '<a href="templates.php">' . __('Emails › Templates') . '</a>'); ?></p>
    </section>
    </div>
    <aside class="mh-cz-preview" aria-label="<?php echo __('Preview'); ?>">
        <h3><?php echo __('Preview (saved settings)'); ?></h3>
<?php
    $sample = '<h3><strong>' . __('Hello Jorge,') . '</strong></h3>'
        . __('We received your request and one of our agents will reply soon.')
        . '<br><br>' . __('Best regards,') . '<br>' . Format::htmlchars((string) $ost->company ?: $cfg->getTitle());
    $preview = MhCustomize::wrapEmail($sample);
    // En la vista previa el logo se toma del portal (en el correo va incrustado)
    $base = (osTicket::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . ROOT_PATH;
    if (($L = $cfg->getClientLogo()) && $L->getKey())
        $preview = str_replace('cid:' . $L->getKey(), $base . 'logo.php?v=' . $L->getId(), $preview);
?>
        <iframe class="mh-cz-email" title="<?php echo __('Preview'); ?>" sandbox=""
            srcdoc="<?php echo Format::htmlchars('<!doctype html><html><body style="margin:0">' . $preview . '</body></html>'); ?>"></iframe>
    </aside>
    </div>
<?php } ?>

</div>
<p class="mh-cz-actions">
    <input class="save" type="submit" name="submit" value="<?php echo __('Save Changes'); ?>">
    <input class="cancel" type="reset" value="<?php echo __('Reset Changes'); ?>">
</p>
</form>

<script>
(function () {
    // Las pestañas son enlaces a otra página: que no las intercepte el manejo
    // de pestañas de osTicket (ul.tabs), que solo cambia de panel en la misma
    document.querySelectorAll('.mh-cz-tabs a').forEach(function (a) {
        a.addEventListener('click', function (e) { e.stopPropagation(); });
    });
    // Color: el selector y el campo de texto van juntos
    var picker = document.getElementById('mh_brand_picker');
    var text = document.getElementById('mh_brand_color');
    if (picker && text) {
        picker.addEventListener('input', function () { text.value = picker.value; });
        text.addEventListener('input', function () {
            if (/^#[0-9a-f]{6}$/i.test(text.value.trim())) picker.value = text.value.trim();
        });
    }
    // Vista previa de la portada
    var form = document.querySelector('form.mh-cz');
    function update() {
        form.querySelectorAll('[data-preview]').forEach(function (el) {
            var k = el.getAttribute('data-preview');
            if (el.type === 'checkbox') {
                document.querySelectorAll('[data-show="' + k + '"]').forEach(function (p) { p.hidden = !el.checked; });
            } else {
                document.querySelectorAll('[data-text="' + k + '"]').forEach(function (p) {
                    p.textContent = el.value.trim() || el.placeholder;
                });
            }
        });
    }
    if (form && form.querySelector('[data-preview]')) {
        form.addEventListener('input', update);
        form.addEventListener('change', update);
        update();
    }
})();
</script>
