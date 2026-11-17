/*
 * MiHelpdesk — Integración del editor con osTicket.
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2.
 *
 * Reemplaza a js/redactor-osticket.js:
 *  - Convierte automáticamente cada <textarea class="richtext"> en editor,
 *    con las mismas opciones por clase (draft, no-bar, small, fullscreen...).
 *  - Borradores automáticos (ajax.php/draft/...), guardar y borrar borrador.
 *  - Imágenes dentro del texto subidas al borrador.
 *  - Vista previa de la firma, bloqueo del ticket al escribir.
 *  - Capa de compatibilidad: $(el).redactor(...) y $(el).data('redactor')
 *    siguen funcionando para el código existente de osTicket.
 */
import { MHEditor, FULL_BUTTONS, SMALL_BUTTONS, t } from './editor.js';
import { suggestionExtensions } from './suggest.js';

const $ = window.jQuery;

function csrf() {
  const m = document.querySelector('meta[name=csrf_token]');
  return m ? m.getAttribute('content') : '';
}

function sysAlert(title, msg) {
  if ($ && $.sysAlert) $.sysAlert(title, msg);
  else window.alert(title + '\n' + (msg || ''));
}

function getConfigSafe() {
  // getConfig() viene de osticket.js / scp.js y devuelve una promesa jQuery
  if (typeof window.getConfig === 'function') {
    try { return window.getConfig(); } catch (e) { /* sin configuración */ }
  }
  return { then: (fn) => fn({}) };
}

// ------------------------------------------------------------ Borradores
class DraftSupport {
  constructor(ed, el, opts) {
    this.ed = ed;
    this.el = el;
    this.opts = opts;
    this.timer = null;
    this.ns = el.dataset.draftNamespace;
    if (!this.ns) return;
    this.autoCreateUrl = 'ajax.php/draft/' + this.ns + (el.dataset.draftObjectId ? '.' + el.dataset.draftObjectId : '');
    this.draftId = el.dataset.draftId || null;
    this.original = opts.draftOriginal || '';
    if (this.draftId) this._setup(this.draftId);
    ed.on('change', () => this.schedule());
    ed.on('imageUploaded', (info) => { if (info.draft_id && !this.draftId) this._created(info.draft_id); });
  }

  get enabled() { return !!this.ns; }

  imageUploadUrl() {
    if (this.draftId) return 'ajax.php/draft/' + this.draftId + '/attach';
    if (this.el.classList.contains('draft')) return this.autoCreateUrl + '/attach';
    return null;
  }

  _setup(id) {
    this.draftId = String(id);
    this.el.setAttribute('data-draft-id', this.draftId);
    const hidden = this.el.form && this.el.form.querySelector('input[name=draft_id]');
    if (hidden) hidden.value = this.draftId;
    this.ed.setStatus('draft', t('all changes saved'), 'ok');
    if (this.opts.draftDelete && !this.deleteBtn)
      this.deleteBtn = this.ed.addToolbarButton('deletedraft',
        { title: t('Delete Draft'), icon: 'trash', action: () => this.deleteDraft() }, 'end');
    if ((this.opts.draftSave || this.opts.draftDelete) && !this.saveBtn)
      this.saveBtn = this.ed.addToolbarButton('savedraft',
        { title: t('Save Draft'), icon: 'save', action: () => this.saveDraft() }, 'end');
    if (this.deleteBtn) this.deleteBtn.hidden = false;
    if (this.saveBtn) this.saveBtn.hidden = false;
  }

  _created(id) {
    this._setup(id);
    this.ed.emit('draft.saved');
  }

  schedule() {
    if (!this.enabled || this.disabled) return;
    this.ed.setStatus('draft', t('unsaved'));
    clearTimeout(this.timer);
    this.timer = setTimeout(() => this.send(), 10000);
  }

  _post(url, html) {
    const name = this.el.getAttribute('name') || 'response';
    const data = new URLSearchParams();
    data.append('name', name);
    data.append(name, html);
    data.append('__CSRFToken__', csrf());
    return fetch(url, {
      method: 'POST', body: data, credentials: 'same-origin',
      headers: { 'X-CSRFToken': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
    });
  }

  send() {
    clearTimeout(this.timer);
    const html = this.ed.getHTML();
    if (!html) return Promise.resolve();
    const url = this.draftId ? 'ajax.php/draft/' + this.draftId : this.autoCreateUrl;
    this.ed.setStatus('draft', t('saving...'));
    return this._post(url, html)
      .then((r) => r.text().then((txt) => {
        if (!r.ok) throw { code: r.status, error: txt };
        let json = {};
        try { json = txt ? JSON.parse(txt) : {}; } catch (e) { /* respuesta vacía */ }
        if (json.code === 422 || (json.error && json.error.code === 422)) return; // mensaje vacío
        if (!this.draftId && json.draft_id) this._created(json.draft_id);
        this.ed.setStatus('draft', t('all changes saved'), 'ok');
        this.ed.emit('draft.saved');
      }))
      .catch((err) => {
        this.disabled = true;
        this.ed.setStatus('draft', t('save error'), 'error');
        this.ed.emit('draft.failed');
        sysAlert(t('Unable to save draft.'),
          t('Refresh the current page to restore and continue your draft.'));
      });
  }

  saveDraft() {
    if (!this.draftId) return this.send();
    return this.send();
  }

  deleteDraft() {
    if (!this.draftId) return Promise.resolve();
    clearTimeout(this.timer);
    return fetch('ajax.php/draft/' + this.draftId, {
      method: 'DELETE', credentials: 'same-origin',
      headers: { 'X-CSRFToken': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
    }).then(() => {
      this.draftId = null;
      this.el.setAttribute('data-draft-id', '');
      this.ed.setStatus('draft', null);
      this.ed.setHTML(this.original || '');
      if (this.deleteBtn) this.deleteBtn.hidden = true;
      if (this.saveBtn) this.saveBtn.hidden = true;
      this.ed.emit('draft.deleted');
    });
  }
}

// --------------------------------------------------------------- Firma
function setupSignature(ed, el) {
  const field = el.dataset.signatureField;
  if (!field) return;
  const form = el.form || el.closest('form');
  const box = document.createElement('div');
  box.className = 'mh-editor-signature';
  box.setAttribute('aria-label', t('Signature'));
  const inner = document.createElement('div');
  inner.className = 'inner';
  box.appendChild(inner);
  ed.box.insertBefore(box, ed.statusbar);
  if (el.dataset.signature) inner.innerHTML = el.dataset.signature;
  else box.hidden = true;

  const update = (e) => {
    const selected = (form.querySelector('input[name="' + field + '"]:checked') || {}).value;
    const type = e && e.target ? e.target.value : selected;
    const deptField = el.dataset.deptField;
    const dept = deptField ? (form.querySelector('[name="' + deptField + '"]') || {}).value : null;
    let url = 'ajax.php/content/signature/';
    if (selected === 'dept' && el.dataset.deptId) url += 'dept/' + el.dataset.deptId;
    else if (selected === 'dept' && deptField) {
      if (!dept) { inner.innerHTML = ''; box.hidden = true; return; }
      url += 'dept/' + dept;
    } else if (selected === 'theirs' && el.dataset.posterId) url += 'agent/' + el.dataset.posterId;
    else if (type === 'none' || !selected) { inner.innerHTML = ''; box.hidden = true; return; }
    else url += selected;
    fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then((r) => r.text())
      .then((html) => { inner.innerHTML = html; box.hidden = !html.trim(); });
  };
  form && form.querySelectorAll('input[name="' + field + '"]').forEach((r) => r.addEventListener('change', update));
}

// ------------------------------------------------------- Bloqueo del ticket
function setupAutolock(ed, el) {
  const form = el.form || el.closest('form');
  if (!form || !form.querySelector('[name=lockCode]') || !$) return;
  const lock = $(el).closest('[data-lock-object-id]');
  if (!lock.length) return;
  ed.on('change', () => { try { lock.exclusive('acquire'); } catch (e) { /* sin bloqueo */ } });
}

// ------------------------------------------- Creación según clases y datos
export function createEditor(el, extra) {
  if (el.mhEditor) return el.mhEditor;
  const cl = el.classList;
  const small = cl.contains('no-bar');
  const sizes = { small: '75px', medium: '150px', large: '225px' };
  let minHeight = sizes.medium;
  Object.keys(sizes).forEach((k) => { if (cl.contains(k)) minHeight = sizes[k]; });
  const inDialog = !!el.closest('.dialog');
  const opts = Object.assign({
    air: small,
    buttons: small ? SMALL_BUTTONS : FULL_BUTTONS.filter((b) => b !== 'fullscreen' || cl.contains('fullscreen') || !inDialog),
    minHeight,
    width: el.dataset.width || (cl.contains('fullscreen') ? '950px' : null),
    lang: (el.closest('[lang]') || {}).lang || '',
    draftDelete: cl.contains('draft-delete'),
    draftSave: cl.contains('draft-save'),
    draftOriginal: el.dataset.draftOriginal || '',
  }, extra || {});

  const ctx = { draft: null };
  const staff = !$ || !$.clientPortal;
  // Respuestas y notas del panel: variables del ticket (las mismas que
  // osTicket reemplaza al enviar)
  const rootContext = el.dataset.rootContext
    || (staff && ['response', 'note'].includes(el.getAttribute('name')) ? 'cannedresponse' : null);
  if (rootContext && !el.dataset.rootContext) el.dataset.rootContext = rootContext;
  opts.extensions = suggestionExtensions({
    rootContext,
    isStaff: staff && el.getAttribute('name') === 'note',
    getEditor: () => ctx.ed,
  });
  opts.uploadData = () => ({ __CSRFToken__: csrf() });
  opts.imageUpload = () => (ctx.draft && ctx.draft.imageUploadUrl()) || null;

  // Igual que Redactor: un campo oculto draft_id en el formulario
  if (cl.contains('draft') && el.form && !el.form.querySelector('input[name=draft_id]')) {
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'draft_id';
    el.form.appendChild(hidden);
  }

  const ed = ctx.ed = new MHEditor(el, opts);
  ed.el.closest('form') && ed.box.classList.add('no-pjax');
  ctx.draft = new DraftSupport(ed, el, opts);
  if (!$ || !$.clientPortal) setupSignature(ed, el);
  setupAutolock(ed, el);
  ed.on('error', (msg) => sysAlert(t('Unable to upload the image'), msg));
  if (!opts.imageUpload()) ed.toolbar.querySelector('.mh-editor-btn--image')
    && !ctx.draft.enabled && (ed.opts.imageUploadAllowed = false);

  // Botón "Restablecer" del formulario
  const reset = el.form && el.form.querySelector('input[type=reset]');
  if (reset) reset.addEventListener('click', () => {
    if (ctx.draft.draftId) ctx.draft.deleteDraft();
    else ed.setHTML('');
  });

  // Compatibilidad con el código que usaba la instancia de Redactor
  const compat = makeCompat(ed, ctx.draft, opts);
  if ($) $(el).data('redactor', compat);
  ed.compat = compat;
  if (opts.focusEnd) ed.focus('end');
  return ed;
}

// --------------------------------------------- Capa de compatibilidad
function makeCompat(ed, draft, opts) {
  const draftApi = {
    get opts() { return { draftId: draft.draftId, draftDelete: opts.draftDelete }; },
    saveDraft: () => draft.saveDraft(),
    deleteDraft: () => draft.deleteDraft(),
  };
  const compat = {
    editor: ed,
    get opts() { return { draftId: draft.draftId, draftDelete: opts.draftDelete, draftSave: opts.draftSave }; },
    plugin: { draft: draftApi },
    source: { getCode: () => ed.getHTML(), setCode: (html) => ed.setHTML(html) },
    insertion: { insertHtml: (html) => ed.insertHtml(html) },
    autosave: { disable: () => { draft.disabled = true; }, enable: () => { draft.disabled = false; } },
    stop: () => ed.destroy(),
    api: (cmd, ...args) => compatCommand(ed, compat, cmd, args),
  };
  return compat;
}

function compatCommand(ed, compat, cmd, args) {
  switch (cmd) {
    case 'selection.save': return ed.saveSelection();
    case 'selection.restore': return ed.restoreSelection();
    case 'source.getCode': return ed.getHTML();
    case 'source.setCode': return ed.setHTML(args[0]);
    case 'insertion.insertHtml': return ed.insertHtml(args[0]);
    case 'stop': return ed.destroy();
    case 'plugin.draft': return compat.plugin.draft;
    case 'plugin.draft.saveDraft': return compat.plugin.draft.saveDraft();
    case 'plugin.draft.deleteDraft':
    case 'deleteDraft': return compat.plugin.draft.deleteDraft();
    case 'focus.end': return ed.focus('end');
    default:
      if (window.console) console.warn('MiHelpdesk editor: comando no soportado', cmd);
      return undefined;
  }
}

// ------------------------------------------------ Inicialización automática
function redact(el, extra) {
  if (el.mhEditor || el.dataset.mhPending) return;
  el.dataset.mhPending = '1';
  getConfigSafe().then((c) => {
    delete el.dataset.mhPending;
    if (el.mhEditor || !document.contains(el)) return;
    const opts = Object.assign({}, extra);
    if (c && c.has_rtl) opts.buttons = (el.classList.contains('no-bar') ? SMALL_BUTTONS : FULL_BUTTONS).concat(['ltr', 'rtl']);
    if (el.querySelector && (el.value || '').indexOf('dir="rtl"') !== -1) opts.direction = 'rtl';
    createEditor(el, opts);
  });
}

function findRichtextBoxes() {
  document.querySelectorAll('textarea.richtext').forEach((el) => {
    if (el.mhEditor) return;
    if (el.classList.contains('ifhtml'))
      getConfigSafe().then((c) => { if (c && c.html_thread) redact(el); });
    else redact(el);
  });
}

function cleanup() {
  document.querySelectorAll('textarea.richtext').forEach((el) => el.mhEditor && el.mhEditor.destroy());
}

export function install() {
  // Variables globales que esperan los paquetes de idioma de osTicket
  // (incluyen traducciones de Redactor): se dejan vacías para no romperlos.
  // $R('#response.richtext') devuelve el editor (lo usa scp.js para
  // insertar respuestas predefinidas).
  if (!window.Redactor || !window.Redactor.mhCompat) {
    const R = function (sel) {
      const el = typeof sel === 'string' ? document.querySelector(sel)
        : (sel && sel.jquery ? sel[0] : sel);
      return el && el.mhEditor ? el.mhEditor.compat : undefined;
    };
    R.mhCompat = true;
    R.lang = {};
    R.add = function () {};
    window.Redactor = window.$R = R;
  }

  if ($) {
    $.fn.redactor = function (arg, ...args) {
      let result;
      this.each(function () {
        if (this.tagName !== 'TEXTAREA') return;
        if (arg === undefined || typeof arg === 'object') {
          const ed = this.mhEditor || createEditor(this, arg || {});
          if (arg && arg.focusEnd) ed.focus('end');
          result = this;
          return;
        }
        const ed = this.mhEditor;
        if (!ed) return;
        result = compatCommand(ed, ed.compat, arg, args);
      });
      return result === undefined ? this : result;
    };
    $.fn.redact = function (el, options) { redact(el || this[0], options); return this; };
    $(findRichtextBoxes);
    $(document).ajaxStop(findRichtextBoxes);
    $(document).on('pjax:start', cleanup);
  } else {
    document.addEventListener('DOMContentLoaded', findRichtextBoxes);
  }
}
