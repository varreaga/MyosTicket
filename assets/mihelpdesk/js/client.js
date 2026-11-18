/*
 * MiHelpdesk — Tema visual: comportamiento del portal de clientes.
 * Parte del tema visual de MiHelpdesk, fork de osTicket bajo GPLv2.
 *
 * Todo aquí es "mejora progresiva": si este archivo no carga, el portal
 * sigue funcionando con los controles originales de osTicket.
 */
(function () {
  'use strict';

  function el(tag, attrs, children) {
    var n = document.createElement(tag);
    Object.keys(attrs || {}).forEach(function (k) {
      if (k === 'text') n.textContent = attrs[k];
      else if (k === 'class') n.className = attrs[k];
      else n.setAttribute(k, attrs[k]);
    });
    (children || []).forEach(function (c) { if (c) n.appendChild(c); });
    return n;
  }

  function fmt(str, value) {
    return String(str).replace('%s', value).replace('%d', value);
  }

  // ------------------------------------------------------------------
  // Menú en celular: el botón muestra u oculta el menú principal.
  function initMenu() {
    var btn = document.querySelector('.mh-menu-toggle');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var open = document.body.classList.toggle('mh-nav-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // ------------------------------------------------------------------
  // Abrir ticket: selector de temas en dos niveles.
  //
  // Los temas principales se muestran en una columna; al elegir uno, sus
  // subtemas aparecen en la otra. La elección se escribe en el <select>
  // original (#topicId) y se dispara su evento "change", que es el que
  // carga los campos propios de cada tema.
  function initTopicPicker() {
    var host = document.querySelector('.mh-topic-picker');
    var dataNode = document.getElementById('mh-topics-data');
    if (!host || !dataNode) return;
    var select = document.getElementById(host.getAttribute('data-select'));
    if (!select) return;

    var data;
    try { data = JSON.parse(dataNode.textContent); } catch (e) { return; }
    var labels = data.labels || {};
    var byId = {}, roots = [], children = {};
    data.topics.forEach(function (t) { byId[t.id] = t; });
    data.topics.forEach(function (t) {
      // Un tema cuyo padre no es público se trata como principal
      if (t.pid && byId[t.pid]) (children[t.pid] = children[t.pid] || []).push(t);
      else roots.push(t);
    });

    // Lleva la sección de temas al principio del formulario, para que el
    // cliente elija primero el tema (orden real del documento, no visual).
    var topicBody = host.closest('tbody');
    var table = topicBody && topicBody.parentNode;
    if (table && table.tBodies && table.tBodies[0] !== topicBody) {
      table.insertBefore(topicBody, table.tBodies[0]);
    }

    // El asterisco de "obligatorio" y el mensaje de error del select se
    // mueven debajo del selector; si solo es el asterisco, se oculta.
    var err = select.parentNode.querySelector('font.error');
    if (err) {
      err.classList.add('mh-topic-error');
      if (err.textContent.replace(/\s|\u00a0/g, '') === '*') err.classList.add('is-marker');
    }

    select.classList.add('mh-sr');
    select.setAttribute('tabindex', '-1');
    select.setAttribute('aria-hidden', 'true');
    host.classList.add('is-ready');

    var current = byId[parseInt(select.value, 10)] || null;
    var activeRoot = current ? (byId[current.pid] && children[current.pid] ? byId[current.pid] : current) : null;

    var rootsCol = el('div', { 'class': 'mh-topic-roots', role: 'group', 'aria-label': labels.main || '' });
    var subCol = el('div', { 'class': 'mh-topic-subs' });
    var subTitleId = 'mh-topic-subs-title';
    var subTitle = el('div', { 'class': 'mh-topic-label', id: subTitleId });
    var subList = el('div', { 'class': 'mh-topic-sublist', role: 'radiogroup', 'aria-labelledby': subTitleId });
    subCol.appendChild(subTitle);
    subCol.appendChild(subList);
    host.appendChild(el('div', { 'class': 'mh-topic-label', text: labels.main || '' }));
    var cols = el('div', { 'class': 'mh-topic-columns' }, [rootsCol, subCol]);
    host.appendChild(cols);
    if (err) host.appendChild(err);

    function choose(topic) {
      current = topic;
      if (String(select.value) !== String(topic.id)) {
        select.value = String(topic.id);
        select.dispatchEvent(new Event('change', { bubbles: true }));
      }
      render();
    }

    function render() {
      rootsCol.textContent = '';
      roots.forEach(function (t) {
        var kids = children[t.id] || [];
        var pressed = activeRoot && activeRoot.id === t.id;
        var meta = kids.length
          ? fmt(kids.length === 1 ? (labels.count1 || '%d') : (labels.count || '%d'), kids.length) : '';
        var btn = el('button', { type: 'button', 'class': 'mh-topic-root', 'aria-pressed': pressed ? 'true' : 'false' }, [
          el('span', { 'class': 'mh-topic-root-name', text: t.name }),
          meta ? el('span', { 'class': 'mh-topic-root-meta', text: meta }) : null,
          kids.length ? el('span', { 'class': 'mh-topic-chevron', 'aria-hidden': 'true', text: '›' }) : null
        ]);
        btn.addEventListener('click', function () {
          activeRoot = t;
          if (!kids.length) choose(t);       // sin subtemas: se elige directo
          else render();
          var first = subList.querySelector('[aria-checked="true"], button');
          if (kids.length && first && window.matchMedia('(max-width: 760px)').matches)
            subCol.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        rootsCol.appendChild(btn);
      });

      var kids = activeRoot ? (children[activeRoot.id] || []) : [];
      subCol.hidden = !kids.length;
      cols.classList.toggle('has-subs', !!kids.length);
      subList.textContent = '';
      if (!kids.length) return;
      subTitle.textContent = fmt(labels.sub || '%s', activeRoot.name);
      // El tema principal también se puede elegir ("Otro")
      kids.concat([{ id: activeRoot.id, name: labels.general || activeRoot.name, isGeneral: true }])
        .forEach(function (t) {
          var checked = current && current.id === t.id;
          var btn = el('button', {
            type: 'button', role: 'radio', 'class': 'mh-topic-sub' + (t.isGeneral ? ' is-general' : ''),
            'aria-checked': checked ? 'true' : 'false'
          }, [
            el('span', { 'class': 'mh-topic-dot', 'aria-hidden': 'true' }),
            el('span', { text: t.name })
          ]);
          btn.addEventListener('click', function () { choose(byId[t.id]); });
          subList.appendChild(btn);
        });
    }

    render();
  }

  function init() {
    initMenu();
    initTopicPicker();
  }

  if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', init);
  else
    init();
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
})();
