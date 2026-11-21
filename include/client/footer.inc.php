<?php /* Modificado el 2026-11-16 y el 2026-11-21 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca; enlaces y texto del pie de Personalización. Ver NOTICE.md. */ ?>
        </div>
    </div>
    <div id="footer">
<?php
// MiHelpdesk: enlaces, páginas y texto del pie elegidos en Personalización
if (app_theme()) {
    require_once INCLUDE_DIR . 'mihelpdesk/class.customize.php';
    $mh_links = array();
    foreach (MhCustomize::getPages('footer_pages') as $P)
        $mh_links[] = sprintf('<a href="%s">%s</a>', MhCustomize::pageUrl($P),
            Format::htmlchars($P->getLocalName()));
    foreach (MhCustomize::getFooterLinks() as $l) {
        $ext = preg_match('#^https?://#i', $l['url']);
        $mh_links[] = sprintf('<a href="%s"%s>%s</a>', Format::htmlchars($l['url']),
            $ext ? ' target="_blank" rel="noopener"' : '', Format::htmlchars($l['label']));
    }
    if ($mh_links)
        echo '<nav class="mh-footer-links" aria-label="'.__('Footer links').'">'
            .implode('', $mh_links).'</nav>';
    if (($mh_ft = trim((string) MhCustomize::get('footer_text'))) !== '')
        echo '<p class="mh-footer-text">'.nl2br(Format::htmlchars($mh_ft)).'</p>';
} ?>
        <p><?php echo __('Copyright &copy;'); ?> <?php echo date('Y'); ?> <?php
        echo Format::htmlchars((string) $ost->company ?: APP_NAME); ?> - <?php echo __('All rights reserved.'); ?></p>
        <p class="based-on"><?php echo sprintf(__('%1$s is free software based on %2$s, licensed under the GNU GPL v2.'),
            Format::htmlchars(APP_NAME),
            '<a href="'.APP_UPSTREAM_URL.'" target="_blank" rel="noopener">'.APP_UPSTREAM_NAME.'</a>'); ?></p>
    </div>
<div id="overlay"></div>
<div id="loading">
    <h4><?php echo __('Please Wait!');?></h4>
    <p><?php echo __('Please wait... it will take a second!');?></p>
</div>
<?php
if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>ajax.php/i18n/<?php
        echo $lang; ?>/js"></script>
<?php } ?>
<script type="text/javascript">
    getConfig().resolve(<?php
        include INCLUDE_DIR . 'ajax.config.php';
        $api = new ConfigAjaxAPI();
        print $api->client(false);
    ?>);
</script>
</body>
</html>
