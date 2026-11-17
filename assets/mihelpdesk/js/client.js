/*
 * MiHelpdesk — Tema visual: comportamiento del portal de clientes.
 * Parte del tema visual de MiHelpdesk, fork de osTicket bajo GPLv2.
 */
(function () {
  'use strict';

  // Menú en celular: el botón muestra u oculta el menú principal.
  function initMenu() {
    var btn = document.querySelector('.mh-menu-toggle');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var open = document.body.classList.toggle('mh-nav-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', initMenu);
  else
    initMenu();
})();
