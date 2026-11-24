<?php
/*********************************************************************
    MiHelpdesk — Impresión (PDF): funciones y estilos comunes

    Lo usan las plantillas de include/mihelpdesk/print/, que
    include/class.pdf.php elige cuando el tema está activo.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/

// Códigos QR (mpdf/qrcode, LGPL-2.1+), incluido en include/mpdf/vendor/mpdf/qrcode
spl_autoload_register(function ($class) {
    if (strpos($class, 'Mpdf\\QrCode\\') !== 0)
        return;
    $file = INCLUDE_DIR . 'mpdf/vendor/mpdf/qrcode/src/'
        . str_replace('\\', '/', substr($class, strlen('Mpdf\\QrCode\\'))) . '.php';
    if (is_file($file))
        require $file;
});

class MhPrint {

    /** Colores: el de la marca de la empresa y tonos derivados. */
    static function colors() {
        $brand = '#0072bc';
        if (class_exists('MhBrand') || (is_file(INCLUDE_DIR.'mihelpdesk/class.brand.php')
                && (require_once INCLUDE_DIR.'mihelpdesk/class.brand.php')))
            $brand = MhBrand::getConfigured() ?: MhBrand::DEFAULT_COLOR;
        $rgb = MhBrand::parse($brand);
        $white = array(255, 255, 255);
        return array(
            'brand' => MhBrand::hex($rgb),
            // Texto de marca legible sobre blanco
            'brandText' => MhBrand::hex(MhBrand::untilContrast($rgb, array(0, 0, 0), $white, 4.8)),
            'soft' => MhBrand::hex(MhBrand::mix($white, $rgb, 0.08)),
            'line' => MhBrand::hex(MhBrand::mix($white, $rgb, 0.35)),
        );
    }

    /** Logo de la empresa (cid: lo resuelve class.pdf.php) o el de fábrica. */
    static function logo() {
        global $cfg;
        if ($cfg && ($logo = $cfg->getClientLogo()))
            return '<img src="cid:'.$logo->getKey().'" class="logo"/>';
        return '<img src="'.ROOT_DIR.'assets/default/images/logo.png" class="logo"/>';
    }

    /** Duración legible: «2 d 4 h», «3 h 15 min», «45 min». */
    static function duration($seconds) {
        if ($seconds === null || $seconds < 0)
            return '—';
        $m = (int) round($seconds / 60);
        if ($m < 1)
            return __('less than a minute');
        $d = intdiv($m, 1440); $h = intdiv($m % 1440, 60); $min = $m % 60;
        if ($d)
            return $h ? sprintf(__('%1$d d %2$d h'), $d, $h) : sprintf(__('%d d'), $d);
        if ($h)
            return $min ? sprintf(__('%1$d h %2$d min'), $h, $min) : sprintf(__('%d h'), $h);
        return sprintf(__('%d min'), $min);
    }

    static function ts($date) {
        return $date ? Misc::db2gmtime($date) : null;
    }

    /** Fecha y hora con el formato del sistema, o un guion. */
    static function date($date) {
        return $date ? Format::datetime($date) : '—';
    }

    /** Primer registro de un tipo en el hilo (R = respuesta de agente). */
    static function firstEntry($thread, $type) {
        if (!$thread)
            return null;
        // Consulta aparte: getEntries() guarda su resultado y filtrarlo
        // dejaría la conversación con un solo mensaje
        return ThreadEntry::objects()->filter(array('thread_id' => $thread->getId(), 'type' => $type))
            ->order_by('created')->first();
    }

    /** Cuántos registros de cada tipo tiene el hilo: array('M' => n, 'R' => n, 'N' => n). */
    static function counts($thread) {
        $out = array('M' => 0, 'R' => 0, 'N' => 0);
        if ($thread)
            foreach (ThreadEntry::objects()->filter(array('thread_id' => $thread->getId()))
                    ->values_flat('type') as $r)
                if (isset($out[$r[0]]))
                    $out[$r[0]]++;
        return $out;
    }

    /** Código QR como etiqueta de mPDF. */
    static function qr($url, $size = 22) {
        if (!$url || strlen($url) > 190 || !class_exists('Mpdf\QrCode\QrCode'))
            return '';
        return sprintf('<barcode code="%s" type="QR" error="M" size="%s" disableborder="1" />',
            Format::htmlchars($url), round($size / 25, 2));
    }

    /** Etiqueta de color (estado, prioridad): una celda de la fila pills(). */
    static function pill($text, $bg, $fg) {
        return sprintf('<td class="pill" style="background-color:%s;color:%s">%s</td><td class="pillgap"></td>',
            $bg, $fg, Format::htmlchars($text));
    }

    /** Fila de etiquetas (mPDF no da relleno a los elementos en línea). */
    static function pills() {
        return '<table class="pills"><tr>'.implode('', array_filter(func_get_args())).'</tr></table>';
    }

    static function statePill($state, $label) {
        switch ($state) {
        case 'closed':  return self::pill($label, '#e6f4ea', '#1e6b30');
        case 'archived':
        case 'deleted': return self::pill($label, '#eef0f3', '#4b5563');
        default:        $c = self::colors(); return self::pill($label, $c['soft'], $c['brandText']);
        }
    }

    /** Hoja de estilos común (mPDF entiende un subconjunto de CSS). */
    static function css() {
        $c = self::colors();
        return <<<CSS
@page {
    header: html_mh; footer: html_mh;
    margin: 14mm 15mm 18mm 15mm; margin-header: 7mm; margin-footer: 7mm;
    margin-top: 27mm;
}
body { font-family: dejavusans; font-size: 9pt; color: #1f2937; line-height: 1.45; }
.logo { max-height: 13mm; max-width: 60mm; width: auto; height: auto; }
.muted { color: #6b7280; }
.small { font-size: 7.5pt; }
.brand { color: {$c['brandText']}; }
.hdr { width: 100%; border-bottom: 0.6mm solid {$c['brand']}; padding-bottom: 2mm; }
.hdr td { vertical-align: bottom; }
.ftr { width: 100%; border-top: 0.2mm solid #d1d5db; padding-top: 1.5mm; font-size: 7.5pt; color: #6b7280; }
.kicker { font-size: 7.5pt; font-weight: bold; letter-spacing: 0.6pt; color: {$c['brandText']}; text-transform: uppercase; }
h1 { font-size: 20pt; margin: 0; color: #111827; }
.subject { font-size: 13pt; font-weight: bold; color: #111827; margin: 1mm 0 2mm 0; }
table.pills { border-collapse: separate; border-spacing: 0 0; margin-top: 1mm; }
td.pill { font-size: 7.5pt; font-weight: bold; padding: 1mm 3mm; border-radius: 2.5mm; }
td.pillgap { width: 1.5mm; }
.qrbox { text-align: center; font-size: 6.5pt; color: #6b7280; }
table.kpi { width: 100%; border-collapse: separate; border-spacing: 2mm 0; margin: 4mm -2mm 0 -2mm; }
table.kpi td { background-color: {$c['soft']}; border: 0.2mm solid {$c['line']}; border-radius: 2mm; padding: 2.5mm 3mm; width: 25%; vertical-align: top; }
.kpi-label { font-size: 7pt; color: #4b5563; text-transform: uppercase; letter-spacing: 0.3pt; }
.kpi-value { font-size: 12pt; font-weight: bold; color: #111827; }
.kpi-note { font-size: 7pt; color: #6b7280; }
table.cards { width: 100%; border-collapse: separate; border-spacing: 2mm 0; margin: 4mm -2mm 0 -2mm; }
td.card { width: 50%; vertical-align: top; border: 0.2mm solid #e5e7eb; border-radius: 2mm; padding: 3mm; }
.card-title { font-size: 7.5pt; font-weight: bold; color: {$c['brandText']}; text-transform: uppercase; letter-spacing: 0.4pt; margin-bottom: 1.5mm; }
table.kv { width: 100%; border-collapse: collapse; }
table.kv th { text-align: left; font-weight: normal; color: #6b7280; width: 36%; padding: 0.8mm 2mm 0.8mm 0; vertical-align: top; font-size: 8pt; }
table.kv td { padding: 0.8mm 0; vertical-align: top; color: #111827; }
.section { font-size: 11pt; font-weight: bold; color: #111827; margin: 6mm 0 2mm 0; padding-bottom: 1mm; border-bottom: 0.3mm solid #e5e7eb; }
.banner { background-color: #fff7e6; border: 0.2mm solid #f3c77a; color: #7a4a00; padding: 2mm 3mm; border-radius: 2mm; font-size: 8pt; margin-top: 3mm; }
.entry { margin: 0 0 3mm 0; border: 0.2mm solid #e5e7eb; border-left: 1.2mm solid #9ca3af; border-radius: 1.5mm; }
.entry.m { border-left-color: {$c['brand']}; }
.entry.r { border-left-color: #1e7e34; }
.entry.n { border-left-color: #d97706; background-color: #fffbf2; }
table.entry-head { width: 100%; border-collapse: collapse; background-color: #f9fafb; }
.entry.n table.entry-head { background-color: #fff3d9; }
table.entry-head td { padding: 1.6mm 3mm; font-size: 8pt; }
.who { font-weight: bold; color: #111827; }
.role { font-size: 7pt; color: #6b7280; }
.entry-body { padding: 2mm 3mm 2.5mm 3mm; }
.entry-files { padding: 1.5mm 3mm; border-top: 0.2mm dashed #e5e7eb; font-size: 7.5pt; color: #4b5563; }
.event { font-size: 7.5pt; color: #6b7280; padding: 0.6mm 0 0.6mm 4mm; }
table.sign { width: 100%; border-collapse: separate; border-spacing: 6mm 0; margin: 2mm -6mm 0 -6mm; }
table.sign td { width: 50%; padding-top: 14mm; border-bottom: 0.3mm solid #374151; }
table.signlbl { width: 100%; border-collapse: separate; border-spacing: 6mm 0; margin: 0 -6mm; }
table.signlbl td { width: 50%; font-size: 7.5pt; color: #4b5563; padding-top: 1mm; }
.note-box { border: 0.2mm solid {$c['line']}; background-color: {$c['soft']}; border-radius: 2mm; padding: 3mm; margin-top: 5mm; font-size: 8pt; }
CSS
            . "\n" . self::threadCss();
    }

    /** Estilos del contenido de los mensajes (tablas, citas, imágenes). */
    static function threadCss() {
        return '.entry-body img { max-width: 100%; height: auto; }
.entry-body p { margin: 0 0 1.5mm 0; }
.entry-body blockquote { margin: 1mm 0 1mm 2mm; padding-left: 2mm; border-left: 0.6mm solid #d1d5db; color: #4b5563; }
.entry-body table { border-collapse: collapse; }
.entry-body td, .entry-body th { border: 0.2mm solid #d1d5db; padding: 1mm 2mm; }
.entry-body pre { font-family: dejavusansmono; font-size: 7.5pt; background-color: #f3f4f6; padding: 2mm; }';
    }

    /** Lista de adjuntos de un mensaje. */
    static function files($entry) {
        if (!$entry->has_attachments || !($files = $entry->attachments))
            return '';
        $out = array();
        foreach ($files as $A)
            $out[] = Format::htmlchars($A->file->name).' <span class="muted">('.Format::file_size($A->file->size).')</span>';
        return $out ? '<div class="entry-files"><b>'.__('Attachments').':</b> '.implode(' · ', $out).'</div>' : '';
    }

    /** Respuestas de los formularios (campos propios). */
    static function formAnswers($forms, $clientOnly = false, $skip = array('subject', 'priority')) {
        $html = '';
        foreach ($forms as $form) {
            $q = array('field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
                'field__name__in' => $skip);
            if ($clientOnly)
                $q[] = Q::not(array('field__flags__hasbit' => DynamicFormField::FLAG_CLIENT_VIEW));
            $rows = '';
            foreach ($form->getAnswers()->exclude(Q::any($q)) as $a) {
                if (!($v = $a->display()))
                    continue;
                $rows .= '<tr><th>'.Format::htmlchars($a->getField()->get('label')).'</th><td>'.$v.'</td></tr>';
            }
            if ($rows)
                $html .= '<div class="card-title" style="margin-top:4mm">'.Format::htmlchars($form->getTitle())
                    .'</div><table class="kv">'.$rows.'</table>';
        }
        return $html;
    }
}
