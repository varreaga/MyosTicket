/*
 * MiHelpdesk — Editor de texto enriquecido basado en Tiptap.
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2. Reemplaza a Redactor.
 *
 * Esta clase es genérica: convierte un <textarea> en un editor con barra de
 * herramientas y mantiene el <textarea> sincronizado con el HTML, que es lo
 * que se envía al servidor. La integración con osTicket (borradores, firma,
 * variables, etc.) está en osticket.js.
 */
import { Editor, Node, Extension, mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { TableKit } from '@tiptap/extension-table';
import { TextStyleKit } from '@tiptap/extension-text-style';
import Placeholder from '@tiptap/extension-placeholder';
import { icon } from './icons.js';

export const t = (s) => (typeof window.__ === 'function' ? window.__(s) : s);

// ------------------------------------------------------------------ Video
// Inserta un video de YouTube o Vimeo como <iframe>. El servidor decide si
// lo conserva según la lista de dominios permitidos de osTicket.
export function videoEmbedUrl(url) {
  let m;
  url = String(url || '').trim();
  if ((m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{6,})/)))
    return 'https://www.youtube.com/embed/' + m[1];
  if ((m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/)))
    return 'https://player.vimeo.com/video/' + m[1];
  return /^https:\/\//.test(url) ? url : null;
}

const Video = Node.create({
  name: 'video',
  group: 'block',
  atom: true,
  draggable: true,
  addAttributes() {
    return {
      src: { default: null },
      width: { default: '560' },
      height: { default: '315' },
    };
  },
  parseHTML() {
    return [{ tag: 'iframe[src]' }];
  },
  renderHTML({ HTMLAttributes }) {
    return ['iframe', mergeAttributes({ frameborder: '0', allowfullscreen: 'true' }, HTMLAttributes)];
  },
  addCommands() {
    return {
      setVideo: (src) => ({ commands }) => commands.insertContent({ type: this.name, attrs: { src } }),
    };
  },
});

// Dirección del texto (izquierda a derecha / derecha a izquierda) por párrafo
const TextDirection = Extension.create({
  name: 'mhTextDirection',
  addGlobalAttributes() {
    return [{
      types: ['paragraph', 'heading', 'blockquote', 'listItem'],
      attributes: {
        dir: {
          default: null,
          parseHTML: (el) => el.getAttribute('dir'),
          renderHTML: (attrs) => (attrs.dir ? { dir: attrs.dir } : {}),
        },
      },
    }];
  },
  addCommands() {
    return {
      setBlockDirection: (dir) => ({ commands }) =>
        ['paragraph', 'heading', 'blockquote', 'listItem']
          .map((type) => commands.updateAttributes(type, { dir }))
          .some(Boolean),
    };
  },
});

// Las imágenes conservan ancho y alto si los traen (Redactor los guardaba)
const SizedImage = Image.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      width: { default: null },
      height: { default: null },
      style: { default: null },
    };
  },
});

// ------------------------------------------------------------ Utilidades
function h(tag, attrs, children) {
  const n = document.createElement(tag);
  Object.entries(attrs || {}).forEach(([k, v]) => {
    if (v === null || v === undefined || v === false) return;
    if (k === 'class') n.className = v;
    else if (k === 'html') n.innerHTML = v;
    else if (k === 'text') n.textContent = v;
    else if (k.startsWith('on')) n.addEventListener(k.slice(2), v);
    else n.setAttribute(k, v === true ? '' : v);
  });
  (children || []).forEach((c) => c && n.appendChild(typeof c === 'string' ? document.createTextNode(c) : c));
  return n;
}

const COLORS = ['#1f2933', '#5f6b7a', '#c62828', '#b26a00', '#1e7e34', '#0072bc', '#6d28d9', '#c2185b'];
const SIZES = [['12px', 'Small'], [null, 'Normal'], ['18px', 'Large'], ['24px', 'Huge']];

// Conjuntos de botones (mismos nombres que usaba Redactor)
export const FULL_BUTTONS = ['html', 'format', 'fontcolor', 'fontsize', 'bold', 'italic', 'underline',
  'deleted', 'ul', 'ol', 'image', 'video', 'table', 'link', 'line', 'fullscreen'];
export const SMALL_BUTTONS = ['format', 'bold', 'italic', 'underline', 'deleted', 'ul', 'ol', 'link', 'image'];

// ------------------------------------------------------------------ Editor
export class MHEditor {
  /**
   * @param {HTMLTextAreaElement} textarea
   * @param {object} opts
   *   buttons: lista de botones; minHeight; placeholder; imageUpload (URL);
   *   uploadData (campos extra del POST); direction ('rtl'); lang;
   *   extensions (extensiones extra de Tiptap); onChange(editor);
   *   onImageUploaded(json); onError(msg)
   */
  constructor(textarea, opts) {
    this.el = textarea;
    this.opts = Object.assign({ buttons: FULL_BUTTONS, minHeight: null, extensions: [] }, opts || {});
    this.listeners = {};
    this.statuses = {};
    this.savedSelection = null;
    this.sourceMode = false;
    this._build();
    this._createEditor();
    this._buildToolbar();
    textarea.mhEditor = this;
  }

  // ---------------------------------------------------------- Estructura
  _build() {
    const o = this.opts;
    this.box = h('div', {
      class: 'mh-editor' + (o.air ? ' mh-editor--compact' : ''),
      dir: o.direction === 'rtl' ? 'rtl' : null,
    });
    this.toolbar = h('div', { class: 'mh-editor-toolbar', role: 'toolbar', 'aria-label': t('Text formatting') });
    this.content = h('div', { class: 'mh-editor-body' });
    this.source = h('textarea', {
      class: 'mh-editor-source', hidden: true, spellcheck: 'false',
      'aria-label': t('HTML source'),
    });
    this.statusbar = h('div', { class: 'mh-editor-status', 'aria-live': 'polite' });
    this.box.append(this.toolbar, this.content, this.source, this.statusbar);
    if (o.width) this.box.style.maxWidth = o.width;
    this.el.parentNode.insertBefore(this.box, this.el.nextSibling);
    this.el.classList.add('mh-editor-original');
    this.el.style.display = 'none';
  }

  _createEditor() {
    const o = this.opts;
    const self = this;
    this.editor = new Editor({
      element: this.content,
      content: this.el.value || '',
      extensions: [
        StarterKit.configure({
          heading: { levels: [1, 2, 3, 4] },
          link: { openOnClick: false, autolink: true, HTMLAttributes: { rel: 'noopener', target: '_blank' } },
        }),
        SizedImage.configure({ inline: true, allowBase64: false }),
        TableKit.configure({ table: { resizable: false } }),
        TextStyleKit,
        Video,
        TextDirection,
        Placeholder.configure({ placeholder: o.placeholder || this.el.getAttribute('placeholder') || '' }),
        ...o.extensions,
      ],
      editorProps: {
        attributes: {
          class: 'mh-editor-content',
          spellcheck: 'true',
          role: 'textbox',
          'aria-multiline': 'true',
          'aria-label': o.label || this.el.getAttribute('aria-label') || this.el.getAttribute('placeholder') || t('Message'),
          lang: o.lang || '',
          style: o.minHeight ? 'min-height:' + o.minHeight : '',
        },
        handlePaste: (view, event) => self._handleFiles(event.clipboardData && event.clipboardData.files, event),
        handleDrop: (view, event, slice, moved) => !moved && self._handleFiles(event.dataTransfer && event.dataTransfer.files, event),
      },
      onUpdate: () => this._changed(),
      onSelectionUpdate: () => { this._refreshToolbar(); this._bubble(); },
      onTransaction: () => this._refreshToolbar(),
      onFocus: () => this.box.classList.add('is-focused'),
      onBlur: ({ event }) => {
        this.box.classList.remove('is-focused');
        if (!(event && event.relatedTarget && this.bubble && this.bubble.contains(event.relatedTarget))) this._hideBubble();
      },
    });
  }

  // ------------------------------------------------------ API pública
  getHTML() {
    if (this.sourceMode) return this.source.value;
    if (this.editor.isEmpty) return '';
    // Sin párrafos vacíos al final (los agrega el editor para poder escribir
    // después de una lista o una tabla)
    return this.editor.getHTML().replace(/(<p>(<br\s*\/?>)?<\/p>)+$/, '');
  }

  setHTML(html, silent) {
    this.editor.commands.setContent(html || '', { emitUpdate: false });
    if (this.sourceMode) this.source.value = html || '';
    this._sync(silent);
  }

  insertHtml(html) {
    if (this.savedSelection) this.restoreSelection();
    this.editor.chain().focus().insertContent(html || '').run();
    this._changed();
  }

  saveSelection() {
    const { from, to } = this.editor.state.selection;
    this.savedSelection = { from, to };
  }

  restoreSelection() {
    if (!this.savedSelection) return;
    const size = this.editor.state.doc.content.size;
    const from = Math.min(this.savedSelection.from, size);
    const to = Math.min(this.savedSelection.to, size);
    this.editor.commands.setTextSelection({ from, to });
    this.savedSelection = null;
  }

  focus(where) {
    this.editor.commands.focus(where || 'end');
  }

  on(evt, fn) {
    (this.listeners[evt] = this.listeners[evt] || []).push(fn);
    return this;
  }

  emit(evt, ...args) {
    (this.listeners[evt] || []).forEach((fn) => fn.apply(this, args));
  }

  setStatus(key, text, kind) {
    let s = this.statuses[key];
    if (!text) {
      if (s) s.remove();
      delete this.statuses[key];
      return;
    }
    if (!s) {
      s = this.statuses[key] = h('span', { class: 'mh-editor-status-item' });
      this.statusbar.appendChild(s);
    }
    s.className = 'mh-editor-status-item' + (kind ? ' is-' + kind : '');
    s.textContent = text;
  }

  addToolbarButton(name, def, position) {
    const btn = this._button(name, def);
    if (position === 'end') {
      btn.classList.add('mh-editor-btn--end');
      this.toolbar.appendChild(btn);
    } else {
      this.toolbar.insertBefore(btn, this.toolbar.firstChild);
    }
    return btn;
  }

  destroy() {
    this._closeMenus();
    this.editor.destroy();
    this.box.remove();
    this.el.style.display = '';
    this.el.classList.remove('mh-editor-original');
    delete this.el.mhEditor;
    this.emit('destroy');
  }

  // ---------------------------------------------------- Sincronización
  _changed() {
    this._sync();
    this.emit('change', this.getHTML());
    if (this.opts.onChange) this.opts.onChange(this);
  }

  _sync(silent) {
    const html = this.getHTML();
    if (this.el.value === html) return;
    this.el.value = html;
    if (silent) return;
    // osTicket usa el evento "change" para detectar formularios modificados
    this.el.dispatchEvent(new Event('input', { bubbles: true }));
    if (window.jQuery) window.jQuery(this.el).trigger('change');
    else this.el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  // ------------------------------------------------------------ Imágenes
  _handleFiles(files, event) {
    const images = Array.from(files || []).filter((f) => /^image\//.test(f.type));
    if (!images.length || !this.opts.imageUpload) return false;
    event.preventDefault();
    images.forEach((f) => this.uploadImage(f));
    return true;
  }

  pickImage() {
    if (!this.opts.imageUpload) {
      this._askUrl(this._buttonEl('image'), t('Image URL'), '', (url) => {
        if (url) this.editor.chain().focus().setImage({ src: url }).run();
      });
      return;
    }
    const input = h('input', { type: 'file', accept: 'image/*', multiple: true, hidden: true });
    input.addEventListener('change', () => {
      Array.from(input.files).forEach((f) => this.uploadImage(f));
      input.remove();
    });
    document.body.appendChild(input);
    input.click();
  }

  uploadImage(file) {
    const url = typeof this.opts.imageUpload === 'function' ? this.opts.imageUpload() : this.opts.imageUpload;
    if (!url) return;
    const data = new FormData();
    data.append('file[]', file); // osTicket espera una lista de archivos
    Object.entries(this.opts.uploadData ? this.opts.uploadData() : {}).forEach(([k, v]) => data.append(k, v));
    this.setStatus('upload', t('Uploading...'));
    fetch(url, { method: 'POST', body: data, credentials: 'same-origin' })
      .then((r) => r.json().catch(() => ({ error: r.statusText })).then((j) => (r.ok ? j : Promise.reject(j))))
      .then((json) => {
        this.setStatus('upload', null);
        // Respuesta de osTicket: { "nombre.png": { url, id, content_id, draft_id } }
        const info = json && (json.url ? json : Object.values(json)[0]);
        if (!info || !info.url) throw json;
        this.editor.chain().focus().setImage({ src: info.url, alt: file.name }).run();
        this.emit('imageUploaded', info);
      })
      .catch((err) => {
        this.setStatus('upload', null);
        const msg = (err && (err.error && (err.error.message || err.error))) || t('Unable to upload the image');
        this.emit('error', String(msg));
      });
  }

  // ------------------------------------------------------------- Barra
  _buttonDefs() {
    const e = () => this.editor;
    const run = (fn) => () => fn(e().chain().focus()).run();
    return {
      html: { title: t('HTML'), icon: 'html', action: () => this.toggleSource(), active: () => this.sourceMode, keepInSource: true },
      format: {
        title: t('Format'), icon: 'format', menu: () => [
          { label: t('Normal text'), run: run((c) => c.setParagraph()), active: () => e().isActive('paragraph') },
          { label: t('Heading 1'), cls: 'mh-menu-h1', run: run((c) => c.setHeading({ level: 1 })), active: () => e().isActive('heading', { level: 1 }) },
          { label: t('Heading 2'), cls: 'mh-menu-h2', run: run((c) => c.setHeading({ level: 2 })), active: () => e().isActive('heading', { level: 2 }) },
          { label: t('Heading 3'), cls: 'mh-menu-h3', run: run((c) => c.setHeading({ level: 3 })), active: () => e().isActive('heading', { level: 3 }) },
          { label: t('Quote'), run: run((c) => c.toggleBlockquote()), active: () => e().isActive('blockquote') },
          { label: t('Code'), cls: 'mh-menu-code', run: run((c) => c.toggleCodeBlock()), active: () => e().isActive('codeBlock') },
        ],
      },
      fontcolor: {
        title: t('Font Color'), icon: 'fontcolor', menu: () => [
          { swatches: COLORS, run: (color) => e().chain().focus().setColor(color).run() },
          { label: t('Remove color'), run: run((c) => c.unsetColor()) },
          { label: t('Highlight'), swatches: ['#fff3bf', '#d3f9d8', '#d0ebff', '#ffe3e3'], run: (color) => e().chain().focus().setBackgroundColor(color).run() },
          { label: t('Remove highlight'), run: run((c) => c.unsetBackgroundColor()) },
        ],
      },
      fontsize: {
        title: t('Font Size'), icon: 'fontsize', menu: () => SIZES.map(([size, label]) => ({
          label: t(label), style: size ? 'font-size:' + size : '',
          run: size ? run((c) => c.setFontSize(size)) : run((c) => c.unsetFontSize()),
          active: () => (size ? e().isActive('textStyle', { fontSize: size }) : !e().getAttributes('textStyle').fontSize),
        })),
      },
      bold: { title: t('Bold'), icon: 'bold', shortcut: 'Ctrl+B', action: run((c) => c.toggleBold()), active: () => e().isActive('bold') },
      italic: { title: t('Italic'), icon: 'italic', shortcut: 'Ctrl+I', action: run((c) => c.toggleItalic()), active: () => e().isActive('italic') },
      underline: { title: t('Underline'), icon: 'underline', shortcut: 'Ctrl+U', action: run((c) => c.toggleUnderline()), active: () => e().isActive('underline') },
      deleted: { title: t('Strikethrough'), icon: 'deleted', action: run((c) => c.toggleStrike()), active: () => e().isActive('strike') },
      ul: { title: t('Unordered List'), icon: 'ul', action: run((c) => c.toggleBulletList()), active: () => e().isActive('bulletList') },
      ol: { title: t('Ordered List'), icon: 'ol', action: run((c) => c.toggleOrderedList()), active: () => e().isActive('orderedList') },
      image: { title: t('Image'), icon: 'image', action: () => this.pickImage() },
      video: {
        title: t('Video'), icon: 'video', action: () => this._askUrl(this._buttonEl('video'), t('YouTube or Vimeo link'), '', (url) => {
          const src = videoEmbedUrl(url);
          if (src) e().chain().focus().setVideo(src).run();
          else if (url) this.emit('error', t('Invalid video link'));
        }),
      },
      table: {
        title: t('Table'), icon: 'table', menu: () => (e().isActive('table') ? [
          { label: t('Add row above'), run: run((c) => c.addRowBefore()) },
          { label: t('Add row below'), run: run((c) => c.addRowAfter()) },
          { label: t('Add column left'), run: run((c) => c.addColumnBefore()) },
          { label: t('Add column right'), run: run((c) => c.addColumnAfter()) },
          { label: t('Delete row'), run: run((c) => c.deleteRow()) },
          { label: t('Delete column'), run: run((c) => c.deleteColumn()) },
          { label: t('Toggle header row'), run: run((c) => c.toggleHeaderRow()) },
          { label: t('Delete table'), danger: true, run: run((c) => c.deleteTable()) },
        ] : [
          { label: t('Insert table'), run: run((c) => c.insertTable({ rows: 3, cols: 3, withHeaderRow: true })) },
        ]),
        active: () => e().isActive('table'),
      },
      link: {
        title: t('Link'), icon: 'link', active: () => e().isActive('link'),
        action: () => {
          const current = e().getAttributes('link').href || '';
          this._askUrl(this._buttonEl('link'), t('Link URL'), current, (url, label) => {
            if (!url) { e().chain().focus().extendMarkRange('link').unsetLink().run(); return; }
            // %{...}: variable que osTicket reemplaza al enviar (enlaces predefinidos)
            const href = /^(https?:|mailto:|tel:|\/|#|%\{)/.test(url) ? url : 'https://' + url;
            if (e().state.selection.empty && !e().isActive('link')) {
              // Sin texto seleccionado: se inserta el enlace con su nombre o la dirección
              e().chain().focus().insertContent([
                { type: 'text', text: label || url, marks: [{ type: 'link', attrs: { href } }] },
                { type: 'text', text: ' ' },
              ]).run();
            } else {
              e().chain().focus().extendMarkRange('link').setLink({ href }).run();
            }
          }, current ? t('Remove link') : null, this.opts.definedLinks);
        },
      },
      line: { title: t('Line'), icon: 'line', action: run((c) => c.setHorizontalRule()) },
      fullscreen: { title: t('Fullscreen'), icon: 'fullscreen', action: () => this.toggleFullscreen(), active: () => this.box.classList.contains('is-fullscreen'), keepInSource: true },
      ltr: { title: t('Left to right'), icon: 'ltr', action: () => e().chain().focus().setBlockDirection('ltr').run() },
      rtl: { title: t('Right to left'), icon: 'rtl', action: () => e().chain().focus().setBlockDirection('rtl').run() },
    };
  }

  _buildToolbar() {
    const defs = this._buttonDefs();
    this.buttons = {};
    let group = null;
    this.opts.buttons.forEach((name) => {
      if (name === '|') { group = null; return; }
      const def = defs[name];
      if (!def) return;
      if (!group || ['bold', 'ul', 'image', 'fullscreen', 'html', 'format'].includes(name)) {
        group = h('div', { class: 'mh-editor-group', role: 'group' });
        this.toolbar.appendChild(group);
      }
      group.appendChild(this.buttons[name] = this._button(name, def));
    });
    if (!this.opts.buttons.length) this.toolbar.hidden = true;
    this._refreshToolbar();
  }

  _button(name, def) {
    const label = def.title + (def.shortcut ? ' (' + def.shortcut + ')' : '');
    const btn = h('button', {
      type: 'button', class: 'mh-editor-btn mh-editor-btn--' + name, title: label, 'aria-label': def.title,
      'aria-haspopup': def.menu ? 'true' : null, 'aria-expanded': def.menu ? 'false' : null,
      'data-name': name, html: icon(def.icon || name),
    });
    btn._def = def;
    btn.addEventListener('mousedown', (ev) => ev.preventDefault()); // conserva la selección
    btn.addEventListener('click', (ev) => {
      ev.preventDefault();
      if (def.menu) this._openMenu(btn, def.menu());
      else if (def.action) def.action();
      this._refreshToolbar();
    });
    return btn;
  }

  _buttonEl(name) {
    return this.buttons[name] || this.toolbar;
  }

  _refreshToolbar() {
    if (!this.buttons || !this.editor) return;
    Object.values(this.buttons).forEach((btn) => {
      const def = btn._def;
      const on = !!(def.active && !this.sourceMode && def.active());
      btn.classList.toggle('is-active', on || (def.keepInSource && def.active && def.active()));
      if (!def.menu) btn.setAttribute('aria-pressed', on ? 'true' : 'false');
      btn.disabled = this.sourceMode && !def.keepInSource;
    });
  }

  // ----------------------------------------------- Menús desplegables
  _openMenu(anchor, items) {
    if (this.menu && this.menu.anchor === anchor) return this._closeMenus();
    this._closeMenus();
    const menu = h('div', { class: 'mh-editor-menu', role: 'menu' });
    items.forEach((it) => {
      if (it.swatches) {
        const row = h('div', { class: 'mh-editor-swatches', role: 'group', 'aria-label': it.label || t('Colors') });
        if (it.label) menu.appendChild(h('div', { class: 'mh-editor-menu-label', text: it.label }));
        it.swatches.forEach((c) => row.appendChild(h('button', {
          type: 'button', role: 'menuitem', class: 'mh-editor-swatch', style: 'background:' + c,
          title: c, 'aria-label': c,
          onmousedown: (ev) => ev.preventDefault(),
          onclick: () => { it.run(c); this._closeMenus(); },
        })));
        menu.appendChild(row);
        return;
      }
      const active = it.active && it.active();
      const b = h('button', {
        type: 'button', role: 'menuitem', class: 'mh-editor-menu-item' + (it.cls ? ' ' + it.cls : '')
          + (active ? ' is-active' : '') + (it.danger ? ' is-danger' : ''),
        style: it.style || null, text: it.label,
        onmousedown: (ev) => ev.preventDefault(),
        onclick: () => { it.run(); this._closeMenus(); },
      });
      menu.appendChild(b);
    });
    this._placePopover(menu, anchor);
    anchor.setAttribute('aria-expanded', 'true');
    this.menu = { el: menu, anchor };
    const first = menu.querySelector('button');
    if (first) first.focus({ preventScroll: true });
  }

  _askUrl(anchor, label, value, cb, removeLabel, definedLinks) {
    this._closeMenus();
    const input = h('input', { type: 'url', class: 'mh-editor-input', value: value || '', 'aria-label': label, placeholder: 'https://' });
    let chosen = null;  // nombre del enlace predefinido elegido
    const done = (val) => { this._closeMenus(); cb(val, chosen && chosen.url === val ? chosen.name : null); };
    // Enlaces predefinidos (panel de agentes): enlace a la solicitud, páginas
    // de acceso... Usan variables %{...} que se reemplazan al enviar.
    let picker = null;
    if (definedLinks) {
      picker = h('select', { class: 'mh-editor-input mh-editor-links', 'aria-label': t('Predefined links') },
        [h('option', { value: '', text: t('Predefined links') + '…' })]);
      picker.addEventListener('change', () => {
        const l = (MHEditor._links || [])[picker.value];
        if (!l) return;
        chosen = l;
        input.value = l.url;
        input.focus();
      });
      MHEditor.loadDefinedLinks(definedLinks).then((links) => {
        links.forEach((l, i) => picker.appendChild(h('option', { value: String(i), text: l.name })));
        if (!links.length) picker.remove();
      });
    }
    // novalidate: el navegador rechazaba "ejemplo.com" o "%{...}" por no ser
    // una dirección completa y el botón Aceptar no hacía nada
    const form = h('form', { class: 'mh-editor-menu mh-editor-ask', role: 'dialog', 'aria-label': label, novalidate: true }, [
      h('label', { class: 'mh-editor-menu-label', text: label }),
      picker,
      input,
      h('div', { class: 'mh-editor-ask-actions' }, [
        removeLabel ? h('button', { type: 'button', class: 'mh-editor-menu-item is-danger', text: removeLabel, onclick: () => done('') }) : null,
        h('button', { type: 'submit', class: 'mh-editor-ok', text: t('OK') }),
      ]),
    ]);
    form.addEventListener('submit', (ev) => { ev.preventDefault(); done(input.value.trim()); });
    this._placePopover(form, anchor);
    this.menu = { el: form, anchor };
    input.focus();
    input.select();
  }

  // Lista de enlaces predefinidos (una sola vez por página)
  static loadDefinedLinks(url) {
    if (!MHEditor._linksReq) {
      MHEditor._linksReq = fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then((r) => (r.ok ? r.json() : []))
        .catch(() => [])
        .then((list) => {
          MHEditor._links = (Array.isArray(list) ? list : [])
            .filter((l) => l && l.url)
            .map((l) => ({ name: t(l.name), url: l.url }));
          return MHEditor._links;
        });
    }
    return MHEditor._linksReq;
  }

  _placePopover(pop, anchor) {
    this.box.appendChild(pop);
    const a = anchor.getBoundingClientRect();
    const b = this.box.getBoundingClientRect();
    let left = a.left - b.left;
    const max = b.width - pop.offsetWidth - 4;
    if (left > max) left = Math.max(4, max);
    pop.style.left = left + 'px';
    pop.style.top = (a.bottom - b.top + 4) + 'px';
    this._outside = (ev) => {
      if (!pop.contains(ev.target) && !anchor.contains(ev.target)) this._closeMenus();
    };
    this._escape = (ev) => {
      if (ev.key === 'Escape') { this._closeMenus(); anchor.focus(); }
    };
    setTimeout(() => {
      document.addEventListener('mousedown', this._outside);
      document.addEventListener('keydown', this._escape);
    });
  }

  _closeMenus() {
    if (!this.menu) return;
    this.menu.el.remove();
    this.menu.anchor.setAttribute && this.menu.anchor.getAttribute('aria-expanded') && this.menu.anchor.setAttribute('aria-expanded', 'false');
    document.removeEventListener('mousedown', this._outside);
    document.removeEventListener('keydown', this._escape);
    this.menu = null;
  }

  // ------------------------- Barra flotante al seleccionar texto
  _bubble() {
    const { state, view } = this.editor;
    const sel = state.selection;
    if (this.sourceMode || sel.empty || !this.editor.isFocused || sel.node
        || !state.doc.textBetween(sel.from, sel.to).trim()) return this._hideBubble();
    if (!this.bubble) {
      const e = () => this.editor;
      const items = [
        ['bold', t('Bold'), () => e().chain().focus().toggleBold().run(), () => e().isActive('bold')],
        ['italic', t('Italic'), () => e().chain().focus().toggleItalic().run(), () => e().isActive('italic')],
        ['underline', t('Underline'), () => e().chain().focus().toggleUnderline().run(), () => e().isActive('underline')],
        ['deleted', t('Strikethrough'), () => e().chain().focus().toggleStrike().run(), () => e().isActive('strike')],
        ['link', t('Link'), () => this._buttonDefs().link.action(), () => e().isActive('link')],
        ['html', t('Code'), () => e().chain().focus().toggleCode().run(), () => e().isActive('code')],
      ];
      this.bubble = h('div', { class: 'mh-editor-bubble', role: 'toolbar', 'aria-label': t('Format selection') });
      this.bubbleItems = items.map(([ic, label, run, active]) => {
        const b = h('button', { type: 'button', class: 'mh-editor-bubble-btn', title: label, 'aria-label': label, html: icon(ic, 16) });
        b.addEventListener('mousedown', (ev) => ev.preventDefault());
        b.addEventListener('click', () => { run(); this._bubble(); });
        this.bubble.appendChild(b);
        return { b, active };
      });
      this.box.appendChild(this.bubble);
    }
    this.bubbleItems.forEach(({ b, active }) => {
      const on = active();
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    const start = view.coordsAtPos(sel.from);
    const end = view.coordsAtPos(sel.to);
    const box = this.box.getBoundingClientRect();
    this.bubble.hidden = false;
    const w = this.bubble.offsetWidth;
    let left = (start.left + end.left) / 2 - box.left - w / 2;
    left = Math.max(4, Math.min(left, box.width - w - 4));
    let top = start.top - box.top - this.bubble.offsetHeight - 8;
    if (top < this.toolbar.offsetHeight) top = end.bottom - box.top + 8;
    this.bubble.style.left = left + 'px';
    this.bubble.style.top = top + 'px';
  }

  _hideBubble() {
    if (this.bubble) this.bubble.hidden = true;
  }

  // --------------------------------------- Vista HTML y pantalla completa
  toggleSource() {
    if (!this.sourceMode) {
      this.source.value = this.getHTML();
      this.source.style.minHeight = Math.max(this.content.offsetHeight, 160) + 'px';
      this.content.hidden = true;
      this.source.hidden = false;
      this.sourceMode = true;
      this.source.focus();
      this.source.oninput = () => this._changed();
    } else {
      this.sourceMode = false;
      this.editor.commands.setContent(this.source.value, { emitUpdate: false });
      this.source.hidden = true;
      this.content.hidden = false;
      this._changed();
      this.focus();
    }
    this.box.classList.toggle('is-source', this.sourceMode);
    this._refreshToolbar();
  }

  toggleFullscreen() {
    const on = !this.box.classList.contains('is-fullscreen');
    this.box.classList.toggle('is-fullscreen', on);
    document.documentElement.classList.toggle('mh-editor-fullscreen-open', on);
    this._refreshToolbar();
    this.focus();
  }
}
