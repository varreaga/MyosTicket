/*
 * MiHelpdesk — Accesibilidad
 * Parte del tema visual de MiHelpdesk, fork de osTicket bajo GPLv2.
 *
 * osTicket arma muchos formularios con tablas: la etiqueta está en una
 * celda y el campo en otra, sin <label for>. Un lector de pantalla anuncia
 * entonces "cuadro de edición" sin decir de qué. Este archivo da un nombre
 * accesible a esos campos y a los botones que solo tienen un icono, sin
 * tocar las plantillas de osTicket (mejora progresiva: si no carga, todo
 * funciona igual).
 *
 * Reglas, en orden:
 *  1. Casillas de selección de una lista: «Seleccionar: <texto de la fila>».
 *  2. Casilla de «seleccionar todo» de una tabla: «Seleccionar todo».
 *  3. Botón u opción con texto justo al lado (radio «Activo»): ese texto.
 *  4. Campo en una fila de tabla: el texto de la primera celda de la fila.
 *  5. Marcador de posición (placeholder) o título (title).
 *  6. Enlaces y botones de solo icono: su tooltip (title).
 *  Además: atributos lang con guion bajo (es_MX) pasan a es-MX.
 */
(function () {
  'use strict';

  function t(s) {
    return (typeof window.__ === 'function') ? window.__(s) : s;
  }

  function clean(s) {
    return (s || '').replace(/\s+/g, ' ').replace(/[:*]+\s*$/, '').trim();
  }

  function hasName(el) {
    if (el.getAttribute('aria-label') || el.getAttribute('aria-labelledby'))
      return true;
    if (el.id) {
      try {
        if (document.querySelector('label[for="' + CSS.escape(el.id) + '"]'))
          return true;
      } catch (e) {}
    }
    if (el.closest('label'))
      return true;
    return false;
  }

  function rowText(el) {
    var tr = el.closest('tr');
    if (!tr)
      return '';
    // La celda de la etiqueta: la primera que no contenga al campo
    var cells = tr.querySelectorAll(':scope > th, :scope > td');
    for (var i = 0; i < cells.length; i++) {
      if (cells[i].contains(el))
        continue;
      var txt = clean(cells[i].textContent);
      if (txt)
        return txt.slice(0, 80);
    }
    return '';
  }

  function nextText(el) {
    var n = el.nextSibling;
    while (n && n.nodeType === 3 && !n.textContent.trim())
      n = n.nextSibling;
    if (n && n.nodeType === 3)
      return clean(n.textContent);
    if (n && n.nodeType === 1 && /^(SPAN|B|STRONG|EM)$/.test(n.tagName))
      return clean(n.textContent);
    return '';
  }

  // Texto que precede al campo: un texto suelto («Entre:») o una etiqueta en
  // <b>, <strong>, <div>… Se saltan los iconos de ayuda y otros campos.
  function prevText(el) {
    var node = el;
    for (var up = 0; up < 3 && node; up++, node = node.parentElement) {
      for (var p = node.previousSibling; p; p = p.previousSibling) {
        if (p.nodeType === 3) {
          var tx = clean(p.textContent.replace(/\u00a0/g, ' '));
          if (tx) return tx.length <= 60 ? tx : '';
          continue;
        }
        if (p.nodeType !== 1 || /^(SCRIPT|STYLE|BR|HR|I|IMG|INPUT|SELECT|TEXTAREA|BUTTON)$/.test(p.tagName))
          continue;
        var txt = clean(p.textContent);
        if (txt) return txt.length <= 60 ? txt : '';
      }
    }
    return '';
  }

  // Botón de enviar del mismo formulario («Buscar»), para cajas de búsqueda
  function submitText(el) {
    var f = el.form;
    var b = f && f.querySelector('[type=submit], button:not([type=button])');
    return b ? clean(b.value || b.textContent || b.getAttribute('aria-label')) : '';
  }

  // Nombre según el icono, para enlaces que solo tienen un icono
  var ICONS = [
    [/icon-user|icon-hand-right/, 'Assign'], [/icon-remove|icon-trash/, 'Remove'],
    [/icon-edit|icon-pencil/, 'Edit'], [/icon-print/, 'Print'], [/icon-share|icon-exchange/, 'Transfer'],
    [/icon-flag/, 'Status'], [/icon-cog/, 'Settings'], [/icon-plus/, 'Add'], [/icon-search/, 'Search'],
    [/icon-refresh/, 'Refresh'], [/icon-link/, 'Link'], [/icon-copy/, 'Copy'],
    [/icon-ellipsis/, 'More']
  ];
  function iconName(el) {
    var i = el.querySelector('i[class*="icon-"]');
    if (!i)
      return '';
    for (var k = 0; k < ICONS.length; k++)
      if (ICONS[k][0].test(i.className))
        return t(ICONS[k][1]);
    return '';
  }

  function nameField(el) {
    if (el.type === 'hidden' || hasName(el))
      return;
    var name = '';
    if (el.type === 'checkbox' && /\b(ckb|mass)\b/.test(el.className)) {
      var tr = el.closest('tr');
      var inHead = el.closest('thead') || (tr && tr.querySelector('th'));
      if (/\bmass\b/.test(el.className) || inHead || !el.name) {
        name = t('Select all');
      } else if (tr) {
        var link = tr.querySelector('a');
        var txt = clean(link ? link.textContent : '') || rowText(el);
        name = t('Select') + (txt ? ': ' + txt : '');
      }
    }
    if (!name && (el.type === 'radio' || el.type === 'checkbox'))
      name = nextText(el);
    if (!name)
      name = rowText(el);
    if (!name)
      name = clean(el.getAttribute('placeholder') || el.getAttribute('title'));
    // Lista con una primera opción tipo «— Todos los departamentos —»
    if (!name && el.tagName === 'SELECT' && el.options.length
        && /^[\s—–-]+.+[—–-]+\s*$/.test(el.options[0].text))
      name = clean(el.options[0].text.replace(/[—–-]/g, ''));
    if (!name)
      name = prevText(el);
    if (!name && /^(text|search)$/.test(el.type))
      name = submitText(el);
    if (name)
      el.setAttribute('aria-label', name);
  }

  function nameControl(el) {
    if (clean(el.textContent) || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby'))
      return;
    if (el.querySelector('img[alt]:not([alt=""])'))
      return;
    var name = clean(el.getAttribute('title') || el.getAttribute('data-original-title')
      || el.getAttribute('data-title'));
    if (!name && el.classList.contains('translatable'))
      name = t('Translate');
    if (!name)
      name = iconName(el);
    if (!name && el.getAttribute('hreflang'))
      name = el.getAttribute('hreflang');
    if (!name && el.tagName === 'A') {
      var img = el.querySelector('img');
      name = img ? clean(img.getAttribute('title') || '') : '';
    }
    if (name)
      el.setAttribute('aria-label', name);
    else if (el.tagName === 'A' && !el.textContent.trim() && !el.querySelector('img, i, svg')) {
      // Enlace vacío (por ejemplo, sin agente): fuera del orden del teclado
      el.setAttribute('aria-hidden', 'true');
      el.setAttribute('tabindex', '-1');
    }
  }

  function fixLang(root) {
    root.querySelectorAll('[lang*="_"]').forEach(function (el) {
      el.setAttribute('lang', el.getAttribute('lang').replace(/_/g, '-'));
    });
    var html = document.documentElement;
    if (/_/.test(html.getAttribute('lang') || ''))
      html.setAttribute('lang', html.getAttribute('lang').replace(/_/g, '-'));
  }

  function run(root) {
    root = root || document;
    root.querySelectorAll('input, select, textarea').forEach(nameField);
    root.querySelectorAll('a, button').forEach(nameControl);
    // Selector con búsqueda (select2): toma el nombre del <select> original
    root.querySelectorAll('.select2-selection__rendered[role="textbox"]').forEach(function (el) {
      if (el.getAttribute('aria-label'))
        return;
      var sel = el.closest('.select2-container');
      sel = sel && sel.previousElementSibling;
      if (sel && sel.tagName === 'SELECT') {
        nameField(sel);
        if (sel.getAttribute('aria-label'))
          el.setAttribute('aria-label', sel.getAttribute('aria-label'));
      }
    });
    // Imágenes sin texto alternativo: decorativas
    root.querySelectorAll('img:not([alt])').forEach(function (img) {
      img.setAttribute('alt', '');
    });
    fixLang(root);
    // Zonas con desplazamiento lateral (tablas, gráficas): alcanzables con
    // el teclado para poder moverlas con las flechas
    document.querySelectorAll('.mh-table-scroll, table.list, .mh-chart-scroll, .mh-dash-table, .mh-cz-logos')
      .forEach(function (el) {
        var sx = getComputedStyle(el).overflowX;
        if ((sx === 'auto' || sx === 'scroll') && el.scrollWidth > el.clientWidth + 1
            && !el.hasAttribute('tabindex')) {
          el.setAttribute('tabindex', '0');
          if (!el.getAttribute('aria-label') && el.tagName !== 'TABLE') {
            el.setAttribute('role', 'region');
            el.setAttribute('aria-label', t('Scrollable content'));
          }
        }
      });
  }

  var pending = false;
  function later() {
    if (pending)
      return;
    pending = true;
    setTimeout(function () { pending = false; run(document); }, 150);
  }

  // Enlace «Saltar al contenido», primero en el orden del teclado
  function skipLink() {
    var main = document.getElementById('content') || document.querySelector('main');
    if (!main || document.querySelector('.mh-skip'))
      return;
    if (!main.hasAttribute('tabindex'))
      main.setAttribute('tabindex', '-1');
    var a = document.createElement('a');
    a.className = 'mh-skip';
    a.href = '#' + (main.id || (main.id = 'mh-main'));
    a.textContent = t('Skip to content');
    a.addEventListener('click', function (e) { e.preventDefault(); main.focus(); main.scrollIntoView(); });
    document.body.insertBefore(a, document.body.firstChild);
  }

  function start() {
    skipLink();
    run(document);
    // Contenido que llega después (pjax, ventanas emergentes, select2)
    if (window.MutationObserver)
      new MutationObserver(later).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', start);
  else
    start();
})();
