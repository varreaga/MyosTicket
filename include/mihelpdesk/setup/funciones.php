<?php
/*********************************************************************
    MiHelpdesk — Funciones comunes del instalador y del actualizador

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/

/** Bloque con un comando y botón para copiarlo */
function mh_setup_cmd($label, $cmd) {
    return sprintf('<div class="mh-cmd"><span class="mh-cmd-label">%s</span><div class="mh-cmd-row"><code>%s</code>'
        .'<button type="button" class="mh-copy" data-copied="%s">%s</button></div></div>',
        Format::htmlchars($label), Format::htmlchars($cmd),
        Format::htmlchars(__('Copied')), Format::htmlchars(__('Copy')));
}

/** Script de los botones «Copiar» (se imprime una vez por página) */
function mh_setup_copy_script() {
    static $listo = false;
    if ($listo) return '';
    $listo = true;
    return <<<'JS'
<script>
document.querySelectorAll('.mh-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var code = btn.parentNode.querySelector('code').textContent, label = btn.textContent;
        var ok = function () { btn.textContent = btn.getAttribute('data-copied');
            setTimeout(function () { btn.textContent = label; }, 1500); };
        if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(code).then(ok);
        else {
            var t = document.createElement('textarea'); t.value = code; document.body.appendChild(t);
            t.select(); try { document.execCommand('copy'); ok(); } catch (e) {} t.remove();
        }
    });
});
</script>
JS;
}

/** Lista de requisitos: cada elemento es array(nombre, descripción, ok, estado) */
function mh_setup_checks(array $items, $fail = 'is-fail') {
    $out = '<ul class="mh-checks">';
    foreach ($items as $i) {
        list($name, $desc, $ok, $state) = $i;
        $out .= sprintf('<li class="%s"><span class="mh-check-icon" aria-hidden="true"></span>'
            .'<span class="mh-check-name">%s%s</span><span class="mh-check-state">%s</span></li>',
            $ok ? 'is-ok' : $fail, Format::htmlchars($name),
            $desc ? '<span class="mh-check-desc">'.Format::htmlchars($desc).'</span>' : '',
            Format::htmlchars($state));
    }
    return $out.'</ul>';
}
