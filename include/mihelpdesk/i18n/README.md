# Traducciones propias de MiHelpdesk

Los textos que agregó MiHelpdesk (portal, panel, tablero y editor) no existen
en los paquetes de idioma oficiales de osTicket. Sus traducciones viven aquí,
un archivo por idioma, y se suman a las del paquete oficial sin modificarlo:

* `es.php`: español (sirve para `es_MX`, `es_ES`, `es_AR`, etc.).
* Para una variante de país, crea `es_ES.php`; tiene prioridad sobre `es.php`.

Cada archivo devuelve un arreglo `'texto en inglés' => 'traducción'`. Si el
paquete oficial ya traduce un texto, se respeta la traducción del paquete.

Las mismas traducciones llegan al JavaScript (editor y tema) por
`ajax.php/i18n/<idioma>/js`.

## Revisar textos sin traducir

```bash
python3 tools/i18n/textos-nuevos.py          # revisa es.php
```

## Requisito: el paquete de idioma oficial

Para que un idioma aparezca en el sistema hace falta su paquete oficial,
que traduce el resto de osTicket. MiHelpdesk ya incluye el de español
(`include/i18n/es_MX.phar`); otros se descargan de
https://osticket.com/download (Language Packs) y se copian en
`include/i18n/`. Después se activan en **Admin Panel › Settings › System ›
Language**.
