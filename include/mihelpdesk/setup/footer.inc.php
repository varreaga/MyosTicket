<?php
/* MiHelpdesk — Instalador: pie. Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md. */
if (!defined('SETUPINC')) die('Kwaheri!');
?>
    </main>
    <footer class="mh-setup-foot"><?php echo sprintf(__('Based on %s, licensed under the GNU GPL v2.'),
        '<a href="'.APP_UPSTREAM_URL.'" target="_blank" rel="noopener">'.APP_UPSTREAM_NAME.'</a>'); ?></footer>
</div>
<script src="../js/jstz.min.js"></script>
<script>
(function () {
    // Zona horaria del navegador para el formulario de instalación
    var tz = document.getElementById('timezone');
    if (tz && window.jstz) { try { tz.value = jstz.determine().name(); } catch (e) {} }

    // Pantalla de espera mientras se instala
    var form = document.getElementById('install');
    if (form) form.addEventListener('submit', function () {
        var b = form.querySelector('[type=submit]');
        if (b) b.disabled = true;
        var busy = document.getElementById('mh-busy');
        if (busy) busy.classList.add('is-on');
    });

    // Botones «Copiar» de los comandos
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
})();
</script>
</body>
</html>
