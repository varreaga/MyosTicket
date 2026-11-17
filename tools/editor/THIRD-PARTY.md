Componentes de terceros del editor
==================================

El archivo compilado `js/mh-editor.js` (y `css/mh-editor.css`) se genera con
`npm run build` a partir del código de esta carpeta. Incluye estas
bibliotecas de código abierto, todas con **licencia MIT** (compatible con la
GPLv2 de MiHelpdesk):

* **Tiptap** (`@tiptap/*`) — https://tiptap.dev — © Tiptap GmbH y colaboradores
* **ProseMirror** (`prosemirror-*`) — https://prosemirror.net — © Marijn Haverbeke y colaboradores
* **linkifyjs** — https://linkify.js.org — © SoapBox Innovations Inc.
* Otras dependencias menores listadas en `package-lock.json`.

Los avisos de licencia completos se conservan al final de `js/mh-editor.js`.
Para revisar la licencia de cada dependencia:

```bash
cd tools/editor && npm install
npx license-checker --summary   # o revisar node_modules/*/LICENSE
```

El código propio de MiHelpdesk en `src/` es GPL-2.0, como el resto del
proyecto.
