/*
 * MiHelpdesk — Menús que aparecen al escribir:
 *   "/"   insertar: respuestas predefinidas, tabla, imagen, listas, etc.
 *   "%{"  variables de osTicket (%{ticket.number}, %{recipient.name}...)
 *   "@"   mencionar a un agente (solo en notas internas del panel)
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2.
 */
import { Extension } from '@tiptap/core';
import Suggestion from '@tiptap/suggestion';
import { PluginKey } from '@tiptap/pm/state';
import { t } from './editor.js';
import { icon } from './icons.js';

// --------------------------------------------- Lista emergente genérica
function popupRenderer(emptyText) {
  let el, items = [], index = 0, props = null;

  const select = (i) => {
    const item = items[i];
    if (item && props) props.command(item);
  };
  const draw = () => {
    el.textContent = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'mh-suggest-empty';
      empty.textContent = emptyText;
      el.appendChild(empty);
      return;
    }
    let lastGroup = null;
    items.forEach((it, i) => {
      if (it.group && it.group !== lastGroup) {
        const g = document.createElement('div');
        g.className = 'mh-suggest-group';
        g.textContent = it.group;
        el.appendChild(g);
        lastGroup = it.group;
      }
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'mh-suggest-item' + (i === index ? ' is-selected' : '');
      b.setAttribute('role', 'option');
      b.setAttribute('aria-selected', i === index ? 'true' : 'false');
      b.innerHTML = (it.icon ? icon(it.icon, 16) : '')
        + '<span class="mh-suggest-label"></span><span class="mh-suggest-desc"></span>';
      b.querySelector('.mh-suggest-label').textContent = it.label;
      b.querySelector('.mh-suggest-desc').textContent = it.desc || '';
      b.addEventListener('mousedown', (ev) => { ev.preventDefault(); select(i); });
      el.appendChild(b);
    });
    const sel = el.querySelector('.is-selected');
    if (sel) sel.scrollIntoView({ block: 'nearest' });
  };
  const place = () => {
    const rect = props && props.clientRect && props.clientRect();
    if (!rect) return;
    const top = rect.bottom + window.scrollY + 6;
    let left = rect.left + window.scrollX;
    left = Math.min(left, window.scrollX + document.documentElement.clientWidth - el.offsetWidth - 8);
    el.style.top = top + 'px';
    el.style.left = Math.max(8, left) + 'px';
  };

  return {
    onStart: (p) => {
      props = p; items = p.items; index = 0;
      el = document.createElement('div');
      el.className = 'mh-suggest';
      el.setAttribute('role', 'listbox');
      document.body.appendChild(el);
      draw(); place();
    },
    onUpdate: (p) => { props = p; items = p.items; index = Math.min(index, Math.max(items.length - 1, 0)); draw(); place(); },
    onKeyDown: ({ event }) => {
      if (event.key === 'Escape') { el && el.remove(); el = null; return true; }
      if (!items.length) return false;
      if (event.key === 'ArrowDown') { index = (index + 1) % items.length; draw(); return true; }
      if (event.key === 'ArrowUp') { index = (index - 1 + items.length) % items.length; draw(); return true; }
      if (event.key === 'Enter' || event.key === 'Tab') { select(index); return true; }
      return false;
    },
    onExit: () => { if (el) el.remove(); el = null; props = null; },
  };
}

function suggestionExtension(name, config) {
  return Extension.create({
    name,
    addProseMirrorPlugins() {
      return [Suggestion(Object.assign({ editor: this.editor, pluginKey: new PluginKey(name) }, config))];
    },
  });
}

// ---------------------------------------------------- Variables %{...}
function variablesExtension(rootContext) {
  let cache = null;
  const load = () => {
    if (!cache) cache = fetch('ajax.php/content/context?root=' + encodeURIComponent(rootContext),
      { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then((r) => r.json()).catch(() => ({}));
    return cache;
  };
  return suggestionExtension('mhVariables', {
    char: '%{',
    allowSpaces: false,
    startOfLine: false,
    items: ({ query }) => load().then((vars) => {
      const keys = Object.keys(vars || {});
      const q = query.toLowerCase();
      const depth = (query.match(/\./g) || []).length;
      return keys
        .filter((k) => k.toLowerCase().indexOf(q) === 0 && (k.match(/\./g) || []).length === depth)
        .sort()
        .slice(0, 50)
        .map((k) => ({
          label: k,
          desc: vars[k],
          more: keys.some((o) => o.indexOf(k + '.') === 0),
        }));
    }),
    command: ({ editor, range, props }) => {
      // Variable con sub-variables: se completa hasta el punto y sigue el menú
      const text = props.more ? '%{' + props.label + '.' : '%{' + props.label + '}';
      editor.chain().focus().insertContentAt(range, text).run();
    },
    render: () => popupRenderer(t('No variables found')),
  });
}

// ------------------------------------------------------- Menú "/"
function slashItems(getEditor) {
  return ({ query }) => {
    const ed = getEditor();
    const items = [];
    // Respuestas predefinidas: las del <select id="cannedResp"> del formulario
    const form = ed && ed.el.closest('form');
    const canned = form && form.querySelector('select#cannedResp, select[name=cannedResp]');
    if (canned) {
      Array.from(canned.options).forEach((o) => {
        if (!o.value || o.value === '0' || o.disabled) return;
        items.push({ group: t('Canned Responses'), label: o.textContent.trim(), icon: 'quote', canned: o.value });
      });
    }
    const add = (label, ic, run, desc) => items.push({ group: t('Insert'), label, icon: ic, run, desc });
    add(t('Heading'), 'format', (c) => c.setHeading({ level: 2 }));
    add(t('Unordered List'), 'ul', (c) => c.toggleBulletList());
    add(t('Ordered List'), 'ol', (c) => c.toggleOrderedList());
    add(t('Quote'), 'quote', (c) => c.toggleBlockquote());
    add(t('Table'), 'table', (c) => c.insertTable({ rows: 3, cols: 3, withHeaderRow: true }));
    add(t('Image'), 'image', null, '').image = true;
    add(t('Line'), 'line', (c) => c.setHorizontalRule());
    if (ed && ed.el.dataset.rootContext) add(t('Variable'), 'variable', null, '%{…}').variable = true;
    const q = query.toLowerCase();
    return items.filter((it) => !q || it.label.toLowerCase().indexOf(q) !== -1).slice(0, 30);
  };
}

function slashExtension(getEditor) {
  return suggestionExtension('mhSlash', {
    char: '/',
    allowSpaces: true,
    startOfLine: false,
    allow: ({ state, range }) => {
      // Solo al inicio de línea o después de un espacio (no en URLs como a/b)
      const before = state.doc.textBetween(Math.max(0, range.from - 1), range.from, '\n', '\n');
      return range.from <= 1 || before === '' || /\s/.test(before);
    },
    items: slashItems(getEditor),
    command: ({ editor, range, props }) => {
      const ed = getEditor();
      editor.chain().focus().deleteRange(range).run();
      if (props.canned) {
        const form = ed.el.closest('form');
        const sel = form && form.querySelector('select#cannedResp, select[name=cannedResp]');
        if (sel) {
          ed.saveSelection();
          sel.value = props.canned;
          if (window.jQuery) window.jQuery(sel).trigger('change');
          else sel.dispatchEvent(new Event('change', { bubbles: true }));
        }
      } else if (props.image) {
        ed.pickImage();
      } else if (props.variable) {
        editor.chain().focus().insertContent('%{').run();
      } else if (props.run) {
        props.run(editor.chain().focus()).run();
      }
    },
    render: () => popupRenderer(t('No matches')),
  });
}

// --------------------------------------------------- Menciones "@"
function mentionsExtension(getEditor) {
  let cache = null;
  const load = (q) => fetch('ajax.php/staff/mention?q=' + encodeURIComponent(q),
    { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then((r) => (r.ok ? r.json() : [])).catch(() => []);
  return suggestionExtension('mhMentions', {
    char: '@',
    allowSpaces: false,
    allow: ({ state, range }) => {
      const before = state.doc.textBetween(Math.max(0, range.from - 1), range.from, '\n', '\n');
      return range.from <= 1 || before === '' || /\s/.test(before);
    },
    items: ({ query }) => load(query).then((list) => (list || []).slice(0, 8).map((s) => ({
      label: s.name, desc: s.dept || '', id: s.id,
    }))),
    command: ({ editor, range, props }) => {
      editor.chain().focus().insertContentAt(range, [
        { type: 'text', text: '@' + props.label, marks: [{ type: 'bold' }] },
        { type: 'text', text: ' ' },
      ]).run();
      const ed = getEditor();
      ed.emit('mention', props);
    },
    render: () => popupRenderer(t('No agents found')),
  });
}

export function suggestionExtensions({ rootContext, isStaff, getEditor }) {
  const list = [slashExtension(getEditor)];
  if (rootContext) list.push(variablesExtension(rootContext));
  if (isStaff) list.push(mentionsExtension(getEditor));
  return list;
}
