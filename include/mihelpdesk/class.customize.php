<?php
/*
 * MiHelpdesk — Personalización sin programar.
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2. Archivo nuevo, no existe
 * en osTicket.
 *
 * Guarda en la configuración (tabla config, espacio "core", claves mh_*) lo
 * que el administrador cambia en Administración › Configuración ›
 * Personalización: logos para modo oscuro, favicon, textos de la portada del
 * portal, menú y pie del portal, título del inicio de sesión y encabezado de
 * marca en los correos. Los logos, el color de marca y los textos de las
 * páginas (bienvenida, inicio de sesión) usan lo que osTicket ya tenía.
 */
class MhCustomize {

    const PREFIX = 'mh_';

    /** Valores por omisión. Un texto vacío significa "el de fábrica". */
    static $defaults = array(
        'logo_dark_client_id' => 0,
        'logo_dark_staff_id' => 0,
        'favicon_id' => 0,
        'home_open_title' => '',
        'home_open_desc' => '',
        'home_status_title' => '',
        'home_status_desc' => '',
        'home_show_search' => 1,
        'home_show_popular' => 1,
        'home_show_categories' => 1,
        'nav_pages' => '[]',
        'footer_pages' => '[]',
        'footer_links' => '[]',
        'footer_text' => '',
        'staff_login_title' => '',
        'email_branding' => 1,
        'email_footer' => '',
    );

    static function get($key) {
        global $cfg;
        $v = $cfg ? $cfg->get(self::PREFIX . $key, null) : null;
        return ($v === null || $v === false) ? self::$defaults[$key] : $v;
    }

    static function isOn($key) {
        return (bool) (int) self::get($key);
    }

    /** Texto personalizado o, si está vacío, el de fábrica ya traducido. */
    static function text($key, $default) {
        $v = trim((string) self::get($key));
        return $v !== '' ? $v : $default;
    }

    static function getJson($key) {
        $v = json_decode((string) self::get($key), true);
        return is_array($v) ? $v : array();
    }

    // ------------------------------------------------------------ Logos

    static function getFile($key) {
        $id = (int) self::get($key);
        return $id ? AttachmentFile::lookup($id) : null;
    }

    /** Logo para modo oscuro: 'client' (portal) o 'staff' (panel). */
    static function getDarkLogo($site) {
        return self::getFile('logo_dark_' . ($site == 'staff' ? 'staff' : 'client') . '_id');
    }

    static function getFavicon() {
        return self::getFile('favicon_id');
    }

    /**
     * Etiquetas <link> del favicon. Con uno propio se sirve desde logo.php;
     * sin él, los de fábrica.
     */
    static function faviconTags($root) {
        if (self::getFavicon()) {
            $v = strtotime($GLOBALS['cfg']->lastModified(self::PREFIX . 'favicon_id')) ?: 1;
            return sprintf('<link rel="icon" href="%slogo.php?favicon&amp;v=%d" />', $root, $v);
        }
        return sprintf('<link rel="icon" type="image/png" href="%1$simages/oscar-favicon-32x32.png" sizes="32x32" />'
            . "\n    " . '<link rel="icon" type="image/png" href="%1$simages/oscar-favicon-16x16.png" sizes="16x16" />', $root);
    }

    // ------------------------------------------------------------ Portal

    /** Páginas propias (tipo "otra", activas) para el menú o el pie. */
    static function getPages($key) {
        $ids = array_filter(array_map('intval', self::getJson($key)));
        if (!$ids)
            return array();
        $pages = array();
        foreach (Page::objects()->filter(array('id__in' => $ids, 'isactive' => 1, 'type' => 'other')) as $P)
            $pages[$P->getId()] = $P;
        // En el orden en que se eligieron
        $out = array();
        foreach ($ids as $id)
            if (isset($pages[$id]))
                $out[] = $pages[$id];
        return $out;
    }

    static function pageUrl($page) {
        return ROOT_PATH . 'pages/index.php/' . Format::slugify($page->getName());
    }

    /** Enlaces libres del pie: lista de array('label' => , 'url' => ). */
    static function getFooterLinks() {
        $out = array();
        foreach (self::getJson('footer_links') as $l)
            if (!empty($l['label']) && !empty($l['url']))
                $out[] = $l;
        return $out;
    }

    /** Páginas que se pueden enlazar: tipo "otra" y activas. */
    static function availablePages() {
        return Page::objects()->filter(array('type' => 'other', 'isactive' => 1))->order_by('name');
    }

    static function validUrl($url) {
        return (bool) preg_match('#^(https?://|mailto:|tel:|/)#i', $url)
            && !preg_match('/[\s<>"]/', $url);
    }

    // ------------------------------------------------------------ Guardar

    static function set($key, $value) {
        global $cfg;
        return $cfg->set(self::PREFIX . $key, $value);
    }

    /**
     * Guarda una pestaña de la pantalla de personalización.
     */
    static function save($tab, $vars, &$errors) {
        global $cfg;
        switch ($tab) {
        case 'brand':
            return self::saveBrand($vars, $errors);
        case 'home':
            foreach (array('home_open_title', 'home_open_desc', 'home_status_title',
                    'home_status_desc') as $k)
                self::set($k, Format::striptags(trim((string) ($vars[$k] ?? ''))));
            foreach (array('home_show_search', 'home_show_popular', 'home_show_categories') as $k)
                self::set($k, isset($vars[$k]) ? 1 : 0);
            return self::savePageBody('landing', $vars['landing_body'] ?? null, $errors);
        case 'nav':
            $valid = array();
            foreach (self::availablePages() as $P)
                $valid[$P->getId()] = true;
            foreach (array('nav_pages', 'footer_pages') as $k) {
                $ids = array_values(array_filter(array_map('intval', (array) ($vars[$k] ?? array())),
                    function ($id) use ($valid) { return isset($valid[$id]); }));
                self::set($k, json_encode($ids));
            }
            $links = array();
            foreach ((array) ($vars['link_label'] ?? array()) as $i => $label) {
                $label = Format::striptags(trim((string) $label));
                $url = trim((string) ($vars['link_url'][$i] ?? ''));
                if ($label === '' && $url === '')
                    continue;
                if ($label === '' || !self::validUrl($url)) {
                    $errors['links'] = __('Each link needs a name and an address that starts with https://, mailto: or tel:.');
                    continue;
                }
                $links[] = array('label' => mb_substr($label, 0, 60), 'url' => mb_substr($url, 0, 500));
            }
            if (!empty($errors['links']))
                return false;
            self::set('footer_links', json_encode(array_slice($links, 0, 10)));
            self::set('footer_text', Format::striptags(trim((string) ($vars['footer_text'] ?? ''))));
            return true;
        case 'login':
            self::set('staff_login_title', Format::striptags(trim((string) ($vars['staff_login_title'] ?? ''))));
            $ok = self::savePageBody('banner-staff', $vars['staff_login_body'] ?? null, $errors, true);
            if (($P = Page::lookupByType('banner-client')) && isset($vars['client_login_title'])) {
                $t = Format::striptags(trim((string) $vars['client_login_title']));
                if ($t !== '') {
                    $P->name = $t;
                    $P->save();
                }
            }
            return self::savePageBody('banner-client', $vars['client_login_body'] ?? null, $errors, true) && $ok;
        case 'email':
            self::set('email_branding', isset($vars['email_branding']) ? 1 : 0);
            self::set('email_footer', Format::striptags(trim((string) ($vars['email_footer'] ?? ''))));
            return true;
        }
        return false;
    }

    static function saveBrand($vars, &$errors) {
        global $cfg;
        // Nombre del centro de soporte (pestaña del navegador y correos)
        if (isset($vars['helpdesk_title'])) {
            $t = Format::striptags(trim((string) $vars['helpdesk_title']));
            if ($t === '')
                $errors['helpdesk_title'] = __('Helpdesk name is required');
            else
                $cfg->set('helpdesk_title', $t);
        }
        // Color de marca
        require_once INCLUDE_DIR . 'mihelpdesk/class.brand.php';
        $color = trim((string) ($vars['mh_brand_color'] ?? ''));
        if ($color !== '' && !($color = MhBrand::normalize($color)))
            $errors['mh_brand_color'] = __('Enter a color in hex (#0072bc) or RGB (rgb(0, 114, 188)) format.');
        // Logo nuevo
        if (!empty($_FILES['logo']['name'][0])) {
            list($logo) = AttachmentFile::format($_FILES['logo']);
            $error = false;
            if ($logo && $logo['error'])
                $errors['logo'] = $logo['error'];
            elseif ($logo && !AttachmentFile::uploadLogo($logo, $error))
                $errors['logo'] = sprintf(__('Unable to upload logo image: %s'), $error);
        }
        // Favicon nuevo
        if (!empty($_FILES['favicon']['name'][0])) {
            list($icon) = AttachmentFile::format($_FILES['favicon']);
            if ($icon && $icon['error'])
                $errors['favicon'] = $icon['error'];
            elseif ($icon && ($e = self::checkFavicon($icon)))
                $errors['favicon'] = $e;
            elseif ($icon && ($f = AttachmentFile::upload($icon, 'I', false))) {
                $old = self::getFavicon();
                self::set('favicon_id', is_object($f) ? $f->getId() : (int) $f);
                if ($old && $old->getId() != self::get('favicon_id'))
                    $old->delete();
            }
            elseif ($icon)
                $errors['favicon'] = __('Unable to upload the icon.');
        }
        if ($errors)
            return false;

        if ($color !== null)
            $cfg->set(MhBrand::CONFIG_KEY, $color);

        $sel = function ($k) use ($vars) {
            return (isset($vars[$k]) && is_numeric($vars[$k]) && $vars[$k]) ? (int) $vars[$k] : 0;
        };
        $cfg->updateAll(array(
            'client_logo_id' => $sel('logo_client') ?: false,
            'staff_logo_id' => $sel('logo_staff') ?: false,
        ));
        self::set('logo_dark_client_id', $sel('logo_dark_client'));
        self::set('logo_dark_staff_id', $sel('logo_dark_staff'));

        $inUse = array_filter(array($sel('logo_client'), $sel('logo_staff'),
            $sel('logo_dark_client'), $sel('logo_dark_staff')));
        foreach ((array) ($vars['delete_logo'] ?? array()) as $id)
            if (!in_array((int) $id, $inUse) && ($f = AttachmentFile::lookup((int) $id))
                    && $f->ft == 'L')
                $f->delete();

        if (isset($vars['delete_favicon']) && ($f = self::getFavicon())) {
            self::set('favicon_id', 0);
            $f->delete();
        }
        return true;
    }

    static function checkFavicon($file) {
        if ($file['size'] > 512 * 1024)
            return __('The icon must be smaller than 512 KB.');
        // Solo imágenes rasterizadas: un SVG puede llevar código
        $info = @getimagesize($file['tmp_name']);
        if (!$info || !in_array($info[2], array(IMAGETYPE_PNG, IMAGETYPE_GIF,
                IMAGETYPE_ICO, IMAGETYPE_WEBP, IMAGETYPE_JPEG)))
            return __('Upload a PNG, ICO, GIF, WEBP or JPG image.');
        return null;
    }

    /** Cambia el contenido de una página del sistema (bienvenida, avisos). */
    static function savePageBody($type, $body, &$errors, $allowEmpty=false) {
        if ($body === null || !($P = Page::lookupByType($type)))
            return true;
        $vars = array(
            'id' => $P->getId(),
            'type' => $P->getType(),
            'name' => $P->getName(),
            'isactive' => $P->isActive() ? 1 : 0,
            'notes' => $P->notes,
            'body' => $body,
        );
        $e = array();
        if (!$P->update($vars, $e, $allowEmpty)) {
            $errors[$type] = $e['body'] ?? ($e['err'] ?? __('Unable to save the page.'));
            return false;
        }
        Draft::deleteForNamespace('page.' . $P->getId() . '%');
        return true;
    }

    // ------------------------------------------------------------ Correos

    /**
     * Envuelve el HTML de un correo con el encabezado de marca: franja con el
     * color de la empresa, logo (o nombre) y pie. Estilos en línea y tablas,
     * que es lo que entienden los programas de correo.
     */
    static function wrapEmail($html) {
        global $cfg, $ost;
        if (!$cfg || !self::isOn('email_branding') || !app_theme())
            return $html;
        require_once INCLUDE_DIR . 'mihelpdesk/class.brand.php';
        $brand = MhBrand::getConfigured() ?: MhBrand::DEFAULT_COLOR;
        $company = $ost && $ost->company ? (string) $ost->company : '';
        $name = Format::htmlchars($company ?: $cfg->getTitle());
        $logo = $cfg->getClientLogo();
        $font = 'font-family:Arial,Helvetica,sans-serif;';
        $head = ($logo && $logo->getKey())
            ? sprintf('<img src="cid:%s" alt="%s" style="max-height:48px;max-width:240px;border:0;display:block;%s">',
                $logo->getKey(), $name, $font)
            : sprintf('<span style="%sfont-size:18px;font-weight:bold;color:#1f2937">%s</span>', $font, $name);
        // Los enlaces de las plantillas usan un azul fijo; con la marca se ven
        // en su color (oscurecido si hace falta para que se lea sobre blanco)
        $link = MhBrand::palette($brand)['light']['--mh-link'] ?? $brand;
        $html = str_ireplace(array('rgb(84, 141, 212)', '#548dd4'), $link, $html);
        // Enlaces sin estilo (por ejemplo, el número de solicitud en los
        // avisos a agentes): el programa de correo los pintaba de azul
        $html = preg_replace('/<a\b(?![^>]*\bstyle=)([^>]*)>/i',
            '<a$1 style="color:' . $link . ';">', $html);
        $foot = trim((string) self::get('email_footer'));
        $foot = $foot !== '' ? nl2br(Format::htmlchars($foot)) : $name;
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f5f8;padding:24px 0;">'
            . '<tr><td align="center" style="padding:0 12px;">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border:1px solid #e2e7ee;border-radius:8px;">'
            . '<tr><td style="height:6px;line-height:6px;font-size:0;background:' . $brand . ';border-radius:8px 8px 0 0;">&nbsp;</td></tr>'
            . '<tr><td style="padding:20px 28px 4px;">' . $head . '</td></tr>'
            . '<tr><td style="padding:12px 28px 28px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:#1f2937;">'
            . $html
            . '</td></tr></table>'
            . '<p style="margin:12px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;color:#8a94a3;">'
            . $foot . '</p>'
            . '</td></tr></table>';
    }
}
