<?php
/*********************************************************************
    class.brand.php

    MiHelpdesk — Color de marca.

    A partir de un solo color (el que elige el administrador en Admin Panel ›
    Settings › Company › Brand color) calcula sus tonos para el tema: color
    al pasar el ratón, fondos suaves, color de enlaces, borde de foco y las
    variantes para el modo oscuro. Los enlaces se oscurecen o aclaran hasta
    tener contraste suficiente (WCAG AA, 4.5:1) sobre el fondo, y el texto
    de los botones es blanco o casi negro según cuál se lea mejor.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/

class MhBrand {

    const DEFAULT_COLOR = '#0072bc';
    const CONFIG_KEY = 'mh_brand_color';

    // Superficies de referencia (tokens.css)
    const LIGHT_SURFACE = '#ffffff';
    const LIGHT_BG = '#f5f7fa';
    const DARK_SURFACE = '#182028';
    const DARK_TEXT = '#111827';

    /**
     * Convierte "#abc", "#aabbcc", "aabbcc" o "rgb(1, 2, 3)" en
     * array(r, g, b). Devuelve null si el texto no es un color válido.
     */
    static function parse($text) {
        $t = strtolower(trim((string) $text));
        if (preg_match('/^#?([0-9a-f]{3})$/', $t, $m)) {
            $h = $m[1];
            return array(hexdec($h[0].$h[0]), hexdec($h[1].$h[1]), hexdec($h[2].$h[2]));
        }
        if (preg_match('/^#?([0-9a-f]{6})$/', $t, $m))
            return array_map('hexdec', str_split($m[1], 2));
        if (preg_match('/^rgba?\(\s*(\d{1,3})\s*[, ]\s*(\d{1,3})\s*[, ]\s*(\d{1,3})\s*(?:[,\/]\s*[\d.]+%?\s*)?\)$/', $t, $m)) {
            $rgb = array((int) $m[1], (int) $m[2], (int) $m[3]);
            foreach ($rgb as $v)
                if ($v > 255) return null;
            return $rgb;
        }
        return null;
    }

    /** Normaliza un color a "#rrggbb", o null si no es válido. */
    static function normalize($text) {
        return ($rgb = self::parse($text)) ? self::hex($rgb) : null;
    }

    static function hex(array $rgb) {
        return sprintf('#%02x%02x%02x', round($rgb[0]), round($rgb[1]), round($rgb[2]));
    }

    /** Mezcla dos colores: $t = 0 da $a, $t = 1 da $b. */
    static function mix(array $a, array $b, $t) {
        return array(
            $a[0] + ($b[0] - $a[0]) * $t,
            $a[1] + ($b[1] - $a[1]) * $t,
            $a[2] + ($b[2] - $a[2]) * $t,
        );
    }

    static function luminance(array $rgb) {
        $c = array_map(function ($v) {
            $v /= 255;
            return $v <= 0.03928 ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        }, $rgb);
        return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
    }

    static function contrast(array $a, array $b) {
        $la = self::luminance($a);
        $lb = self::luminance($b);
        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /**
     * Acerca $color a $toward (negro o blanco) poco a poco hasta lograr el
     * contraste pedido contra $against.
     */
    static function untilContrast(array $color, array $toward, array $against, $ratio) {
        for ($i = 0; $i < 20 && self::contrast($color, $against) < $ratio; $i++)
            $color = self::mix($color, $toward, 0.12);
        return $color;
    }

    static function rgba(array $rgb, $alpha) {
        return sprintf('rgba(%d, %d, %d, %s)', round($rgb[0]), round($rgb[1]), round($rgb[2]), $alpha);
    }

    /**
     * Variables CSS del tema para un color de marca.
     * Devuelve array('light' => [...], 'dark' => [...]).
     */
    static function palette($color) {
        $brand = self::parse($color) ?: self::parse(self::DEFAULT_COLOR);
        $white = array(255, 255, 255);
        $black = array(0, 0, 0);
        $lightSurface = self::parse(self::LIGHT_SURFACE);
        $lightBg = self::parse(self::LIGHT_BG);
        $darkSurface = self::parse(self::DARK_SURFACE);
        $darkText = self::parse(self::DARK_TEXT);

        // Texto sobre el color de marca (botones): el que más contraste tenga.
        // Si ni el blanco ni el casi negro llegan a 4.5:1 (tonos medios, como
        // un naranja), el color se oscurece apenas lo necesario para que el
        // texto blanco de los botones cumpla WCAG AA.
        if (max(self::contrast($brand, $white), self::contrast($brand, $darkText)) < 4.5) {
            for ($i = 0; $i < 40 && self::contrast($brand, $white) < 4.5; $i++)
                $brand = self::mix($brand, $black, 0.03);
        }
        $onBrand = self::contrast($brand, $white) >= self::contrast($brand, $darkText)
            ? $white : $darkText;
        // Al pasar el ratón: un poco más oscuro (o más claro si ya es muy oscuro)
        $hover = self::luminance($brand) < 0.02
            ? self::mix($brand, $white, 0.15) : self::mix($brand, $black, 0.15);

        // Enlaces y textos de marca: contraste AA sobre el fondo de la página
        $link = self::untilContrast($brand, $black, $lightBg, 4.8);
        $linkHover = self::mix($link, $black, 0.25);

        $light = array(
            '--mh-brand' => self::hex($brand),
            '--mh-brand-hover' => self::hex($hover),
            '--mh-brand-soft' => self::hex(self::mix($lightSurface, $brand, 0.10)),
            '--mh-brand-soft-hover' => self::hex(self::mix($lightSurface, $brand, 0.18)),
            '--mh-on-brand' => self::hex($onBrand),
            '--mh-link' => self::hex($link),
            '--mh-link-hover' => self::hex($linkHover),
            '--mh-focus' => self::rgba($brand, '.35'),
            '--mh-info-border' => self::hex(self::mix($lightSurface, $brand, 0.30)),
        );

        // Modo oscuro: enlaces más claros para leerse sobre superficies oscuras
        $linkDark = self::untilContrast($brand, $white, $darkSurface, 5.5);
        $dark = array(
            '--mh-brand' => self::hex($brand),
            '--mh-brand-hover' => self::hex(self::mix($brand, $white, 0.12)),
            '--mh-brand-soft' => self::hex(self::mix($darkSurface, $brand, 0.22)),
            '--mh-brand-soft-hover' => self::hex(self::mix($darkSurface, $brand, 0.32)),
            '--mh-on-brand' => self::hex($onBrand),
            '--mh-link' => self::hex($linkDark),
            '--mh-link-hover' => self::hex(self::mix($linkDark, $white, 0.3)),
            '--mh-focus' => self::rgba($linkDark, '.4'),
            '--mh-info-border' => self::hex(self::mix($darkSurface, $brand, 0.45)),
        );

        return array('light' => $light, 'dark' => $dark);
    }

    /** Color configurado por el administrador ('' = el del tema). */
    static function getConfigured() {
        global $cfg;
        if (!$cfg || !method_exists($cfg, 'get'))
            return '';
        return self::normalize($cfg->get(self::CONFIG_KEY)) ?: '';
    }

    /**
     * Bloque <style> con los tonos del color configurado. Vacío si no hay
     * color propio (se usan los de tokens.css).
     */
    static function getStyleTag() {
        if (!($color = self::getConfigured()))
            return '';
        $p = self::palette($color);
        $vars = function ($list) {
            $out = '';
            foreach ($list as $k => $v)
                $out .= $k.':'.$v.';';
            return $out;
        };
        return "\n    <style id=\"mh-brand\">:root{".$vars($p['light'])."}"
            .'html[data-mh-mode="dark"]{'.$vars($p['dark'])."}</style>\n";
    }
}
