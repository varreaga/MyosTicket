/*
 * MiHelpdesk — Íconos de la barra del editor (SVG de trazo, 24x24).
 * Dibujados para MiHelpdesk. GPL-2.0, como el resto del proyecto.
 */
const P = {
  html: '<path d="m8 8-4 4 4 4"/><path d="m16 8 4 4-4 4"/>',
  format: '<path d="M13 4v16"/><path d="M17 4v16"/><path d="M19 4H9.5a4.5 4.5 0 0 0 0 9H13"/>',
  fontcolor: '<path d="m5 16 7-12 7 12"/><path d="M8 11h8"/><path d="M4 20h16" stroke-width="3"/>',
  fontsize: '<path d="M3 18 8 6l5 12"/><path d="M4.5 14h7"/><path d="m15 18 3-7 3 7"/><path d="M16 16h4"/>',
  bold: '<path d="M7 5h6a3.5 3.5 0 0 1 0 7H7z"/><path d="M7 12h7a3.5 3.5 0 0 1 0 7H7z"/>',
  italic: '<path d="M14 5h-4"/><path d="M14 19h-4"/><path d="M14 5l-4 14"/>',
  underline: '<path d="M7 4v7a5 5 0 0 0 10 0V4"/><path d="M5 20h14"/>',
  deleted: '<path d="M16 6a4 4 0 0 0-4-2c-2.5 0-4 1.4-4 3 0 1.3.8 2.3 2.5 3"/><path d="M4 12h16"/><path d="M8 18a4 4 0 0 0 4 2c2.5 0 4-1.4 4-3 0-1-.4-1.8-1.2-2.4"/>',
  ul: '<path d="M9 6h11"/><path d="M9 12h11"/><path d="M9 18h11"/><circle cx="4.5" cy="6" r="1"/><circle cx="4.5" cy="12" r="1"/><circle cx="4.5" cy="18" r="1"/>',
  ol: '<path d="M10 6h10"/><path d="M10 12h10"/><path d="M10 18h10"/><path d="M4 4h1v4"/><path d="M4 8h2"/><path d="M4 14h2l-2 3h2"/>',
  image: '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
  video: '<rect x="2" y="5" width="15" height="14" rx="2"/><path d="m17 10 5-3v10l-5-3"/>',
  table: '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M3 15h18"/><path d="M9 4v16"/><path d="M15 4v16"/>',
  link: '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
  line: '<path d="M3 12h18"/>',
  fullscreen: '<path d="M8 3H5a2 2 0 0 0-2 2v3"/><path d="M21 8V5a2 2 0 0 0-2-2h-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/>',
  save: '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/>',
  trash: '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/>',
  undo: '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-4"/>',
  redo: '<path d="m15 14 5-5-5-5"/><path d="M20 9H9a5 5 0 0 0 0 10h4"/>',
  quote: '<path d="M7 7h4v4H7z"/><path d="M7 11c0 3-1 5-3 6"/><path d="M15 7h4v4h-4z"/><path d="M15 11c0 3-1 5-3 6"/>',
  ltr: '<path d="M9 4v10"/><path d="M13 4v10"/><path d="M15 4H8.5a3.5 3.5 0 0 0 0 7H9"/><path d="M4 19h16"/><path d="m17 16 3 3-3 3"/>',
  rtl: '<path d="M11 4v10"/><path d="M15 4v10"/><path d="M17 4h-6.5a3.5 3.5 0 0 0 0 7H11"/><path d="M4 19h16"/><path d="m7 16-3 3 3 3"/>',
  variable: '<path d="M8 4c-2 0-3 1-3 3v3l-2 2 2 2v3c0 2 1 3 3 3"/><path d="M16 4c2 0 3 1 3 3v3l2 2-2 2v3c0 2-1 3-3 3"/>',
};

export function icon(name, size) {
  const s = size || 18;
  return '<svg width="' + s + '" height="' + s + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
    + 'stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
    + (P[name] || '') + '</svg>';
}
