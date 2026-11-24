/*
 * MiHelpdesk — Tema visual: Panel de agentes
 * Parte del tema visual de MiHelpdesk, fork de osTicket bajo GPLv2.
 *
 * Mejora progresiva: si este archivo no carga, el panel sigue funcionando.
 *  - Botón de menú (celular): abre la barra lateral como cajón.
 *  - Menú del usuario en la barra superior.
 */
(function () {
  'use strict';
  var body = document.body;

  // ---------------------------------------------------- Cajón del menú
  var toggle = document.querySelector('.mh-nav-toggle');
  var backdrop = null;

  function setNav(open) {
    body.classList.toggle('mh-nav-open', open);
    if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      backdrop = document.createElement('div');
      backdrop.className = 'mh-nav-backdrop';
      backdrop.addEventListener('click', function () { setNav(false); });
      body.appendChild(backdrop);
      var first = document.querySelector('#nav a');
      if (first) first.focus();
    } else if (backdrop) {
      backdrop.remove();
      backdrop = null;
    }
  }
  if (toggle) toggle.addEventListener('click', function () {
    setNav(!body.classList.contains('mh-nav-open'));
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && body.classList.contains('mh-nav-open')) {
      setNav(false);
      if (toggle) toggle.focus();
    }
  });
  // Al navegar (PJAX o enlace), se cierra el cajón
  document.addEventListener('click', function (e) {
    if (body.classList.contains('mh-nav-open') && e.target.closest('#nav a')) setNav(false);
  });
  if (window.jQuery) jQuery(document).on('pjax:start', function () { setNav(false); });

  // ---------------------------------------------------- Menú del usuario
  var userBtn = document.querySelector('.mh-user-btn');
  var userMenu = document.getElementById('mh-user-menu');
  function setUser(open) {
    if (!userBtn || !userMenu) return;
    userMenu.hidden = !open;
    userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  if (userBtn && userMenu) {
    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      setUser(userMenu.hidden);
      if (!userMenu.hidden) userMenu.querySelector('a').focus();
    });
    document.addEventListener('click', function (e) {
      if (!userMenu.hidden && !e.target.closest('.mh-user')) setUser(false);
    });
    userMenu.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { setUser(false); userBtn.focus(); }
    });
  }
  // ------------------------------------- Lista de tickets (colas)
  // Marca columnas conocidas, convierte prioridad y estado en etiquetas de
  // color y pone el nombre de la columna en cada celda (para celular).
  var COLS = { 1: 'number', 3: 'subject', 10: 'date', 9: 'date', 6: 'date', 8: 'assignee' };
  var CHIPS = {
    low: /^(low|baja?)$/i, normal: /^normal$/i, high: /^(high|alta?)$/i,
    urgent: /^(emergency|urgent|emergencia|urgente)$/i,
    open: /^(open|abiert[oa])$/i, resolved: /^(resolved|resuelt[oa])$/i,
    closed: /^(closed|archived|cerrad[oa]|archivad[oa])$/i, overdue: /^(overdue|vencid[oa])$/i,
  };
  function chipKind(text) {
    for (var k in CHIPS) if (CHIPS[k].test(text)) return k;
    return null;
  }
  function initials(name) {
    return name.split(/\s+/).filter(Boolean).slice(0, 2).map(function (w) { return w[0]; }).join('').toUpperCase();
  }
  function enhanceQueues() {
    document.querySelectorAll('table.queue:not(.mh-done)').forEach(function (table) {
      table.classList.add('mh-done');
      var heads = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
        return { id: th.getAttribute('data-id'), label: th.textContent.trim() };
      });
      table.querySelectorAll('tbody tr').forEach(function (tr) {
        var tds = tr.children;
        if (tr.querySelector('td[style*="font-weight:bold"], td > div[style*="font-weight:bold"]')) tr.classList.add('mh-unread');
        for (var i = 0; i < tds.length; i++) {
          var td = tds[i], head = heads[i];
          if (!head || !head.id) continue;
          if (head.label) td.setAttribute('data-label', head.label);
          var col = COLS[head.id];
          if (col) td.classList.add('mh-col-' + col);
          // Celdas con color (prioridad u otras anotaciones del administrador)
          var colored = td.querySelector('div[style*="background-color"]') || (td.style.backgroundColor ? td : null);
          var text = td.textContent.trim();
          var kind = text && chipKind(text);
          if (text && (kind || colored)) {
            var chip = document.createElement('span');
            chip.className = 'mh-chip mh-chip--' + (kind || 'custom');
            if (!kind && colored) chip.style.background = colored.style.backgroundColor;
            chip.textContent = text;
            td.textContent = '';
            td.appendChild(chip);
          } else if (col === 'assignee' && text) {
            var av = document.createElement('span');
            av.className = 'mh-avatar';
            av.setAttribute('aria-hidden', 'true');
            av.textContent = initials(text);
            td.insertBefore(av, td.firstChild);
          }
        }
      });
    });
  }
  enhanceQueues();
  if (window.jQuery) jQuery(document).on('pjax:end', enhanceQueues);

  // ------------------------------------- Conversación del ticket
  // Iniciales en lugar de la imagen de avatar de cada mensaje.
  function enhanceThread() {
    document.querySelectorAll('.thread-entry:not(.mh-done)').forEach(function (entry) {
      entry.classList.add('mh-done');
      var header = entry.querySelector('.header');
      var name = header && header.querySelector(':scope > b');
      if (!name) return;
      var av = document.createElement('span');
      av.className = 'mh-entry-avatar';
      av.setAttribute('aria-hidden', 'true');
      av.textContent = initials(name.textContent.trim());
      header.insertBefore(av, name);
    });
  }
  enhanceThread();
  if (window.jQuery) jQuery(document).on('pjax:end', enhanceThread);

  // ------------------------- Sugerencias en el buscador de la barra superior
  // Usa la misma búsqueda rápida de tickets que el buscador original.
  if (window.jQuery && jQuery.fn.typeahead) {
    var last;
    jQuery('#mh-top-q').typeahead({
      source: function (typeahead, query) {
        if (last) last.abort();
        last = jQuery.ajax({
          url: 'ajax.php/tickets/lookup?q=' + encodeURIComponent(query),
          dataType: 'json',
          success: function (data) { typeahead.process(data); },
        });
      },
      onselect: function (obj) {
        var form = this.$element.closest('form');
        form.find('input[name=search-type]').val('typeahead');
        this.$element.val(obj.value);
        if (obj.id) form.append(jQuery('<input type="hidden" name="number">').val(obj.id));
        form.submit();
      },
      property: 'matches',
    });
  }
  // ------------------------------------------------ Calendario de fechas
  // Un solo mes a la vista (el original pide dos). Se ajusta la opción al
  // crear cada calendario; el estilo está en widgets.css.
  if (window.jQuery && jQuery.fn.datepicker && !jQuery.fn.datepicker.mhPatched) {
    var origDatepicker = jQuery.fn.datepicker;
    var patched = function (options) {
      var args = Array.prototype.slice.call(arguments);
      if (options && typeof options === 'object')
        args[0] = jQuery.extend({}, options, { numberOfMonths: 1 });
      return origDatepicker.apply(this, args);
    };
    for (var key in origDatepicker)
      if (Object.prototype.hasOwnProperty.call(origDatepicker, key)) patched[key] = origDatepicker[key];
    patched.mhPatched = true;
    jQuery.fn.datepicker = patched;
  }

  // Barra de aviso flotante de osTicket ($.messageBar): su botón por
  // omisión dice "OK" porque el texto se fija antes de que carguen las
  // traducciones. Se vuelve a traducir cuando la página ya está lista.
  if (window.jQuery) {
    jQuery(function () {
      if (jQuery.messageBar && jQuery.messageBar.defaults && typeof window.__ === 'function')
        jQuery.messageBar.defaults.buttonText = window.__('OK');
    });
  }

  // Tablas de listas (categorías, departamentos, directorio…): en una
  // tableta o celular se desplazan a lo ancho en lugar de cortarse.
  function wrapTables(root) {
    (root || document).querySelectorAll('#content table.list:not(.queue)').forEach(function (t) {
      if (t.parentElement && t.parentElement.classList.contains('mh-table-scroll'))
        return;
      var w = document.createElement('div');
      w.className = 'mh-table-scroll';
      t.parentNode.insertBefore(w, t);
      w.appendChild(t);
    });
  }
  wrapTables();
  if (window.jQuery)
    jQuery(document).on('pjax:end ajaxComplete', function () { wrapTables(); });
})();
