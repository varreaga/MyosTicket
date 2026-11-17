/*
 * MiHelpdesk — Compila el editor:
 *   src/main.js  -> ../../js/mh-editor.js
 *   src/*.css    -> ../../css/mh-editor.css
 *
 * Uso:  npm install && npm run build      (npm run watch para desarrollo)
 *
 * El archivo compilado se guarda en el repositorio para que osTicket se
 * pueda instalar sin Node.js. Su código fuente es esta carpeta (GPL).
 */
import * as esbuild from 'esbuild';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const here = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(here, '../..');
const watch = process.argv.includes('--watch');

const banner = '/*! MiHelpdesk editor (Tiptap) — GPL-2.0. Código fuente: tools/editor/. '
  + 'Incluye Tiptap y ProseMirror (MIT). Ver tools/editor/THIRD-PARTY.md */';

const options = {
  entryPoints: { 'mh-editor': path.join(here, 'src/main.js') },
  bundle: true,
  format: 'iife',
  target: ['es2019'],
  minify: !watch,
  sourcemap: false,
  legalComments: 'eof',
  banner: { js: banner, css: banner },
  outdir: path.join(root, 'js'),
  // El CSS sale junto al JS; se mueve a css/ abajo
  logLevel: 'info',
};

if (watch) {
  const ctx = await esbuild.context(options);
  await ctx.watch();
} else {
  await esbuild.build(options);
  const fs = await import('node:fs');
  fs.renameSync(path.join(root, 'js/mh-editor.css'), path.join(root, 'css/mh-editor.css'));
  console.log('CSS -> css/mh-editor.css');
}
