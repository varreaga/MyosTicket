/*
 * MiHelpdesk — Tema visual: funciones comunes (todas las áreas)
 * Parte del tema visual de MiHelpdesk, fork de osTicket bajo GPLv2.
 *
 * Apariencia: claro, oscuro o automático (según el sistema). La elección se
 * guarda en el navegador. El script de <head> (app_theme_head()) ya aplicó
 * la apariencia antes de pintar; aquí se manejan los botones y los cambios
 * del sistema cuando está en automático.
 */
(function () {
  'use strict';
  var root = document.documentElement;
  var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

  function current() {
    try { return localStorage.getItem('mh-appearance') || 'light'; } catch (e) { return 'light'; }
  }

  function apply(choice) {
    var dark = choice === 'dark' || (choice === 'auto' && media && media.matches);
    root.setAttribute('data-mh-appearance', choice);
    root.setAttribute('data-mh-mode', dark ? 'dark' : 'light');
    document.querySelectorAll('[data-mh-appearance-set]').forEach(function (b) {
      b.setAttribute('aria-pressed', b.getAttribute('data-mh-appearance-set') === choice ? 'true' : 'false');
    });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('[data-mh-appearance-set]');
    if (!btn) return;
    var choice = btn.getAttribute('data-mh-appearance-set');
    try { localStorage.setItem('mh-appearance', choice); } catch (err) { /* sin almacenamiento */ }
    apply(choice);
  });

  if (media) {
    var onChange = function () { if (current() === 'auto') apply('auto'); };
    if (media.addEventListener) media.addEventListener('change', onChange);
    else if (media.addListener) media.addListener(onChange);
  }

  // Otras pestañas del mismo navegador
  window.addEventListener('storage', function (e) {
    if (e.key === 'mh-appearance') apply(current());
  });

  // Menú emergente de apariencia (portal)
  var toggle = document.querySelector('.mh-appearance-toggle');
  var menu = document.getElementById('mh-appearance-menu');
  if (toggle && menu) {
    var setOpen = function (open) {
      menu.hidden = !open;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      setOpen(menu.hidden);
      if (!menu.hidden) {
        var pressed = menu.querySelector('[aria-pressed="true"]') || menu.querySelector('button');
        if (pressed) pressed.focus();
      }
    });
    document.addEventListener('click', function (e) {
      if (!menu.hidden && !e.target.closest('.mh-appearance-pop')) setOpen(false);
    });
    menu.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { setOpen(false); toggle.focus(); }
    });
  }

  apply(current());
})();
