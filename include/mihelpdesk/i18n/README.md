<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="../../../assets/mihelpdesk/marca/mihelpdesk-logo-oscuro.png">
    <img src="../../../assets/mihelpdesk/marca/mihelpdesk-logo.png" alt="MiHelpdesk" width="260">
  </picture>
</p>

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

## "Solicitud" en lugar de "ticket"

MiHelpdesk usa "solicitud" en todo el sistema. El paquete oficial mezcla
términos, así que `es_MX.paquete.php` reemplaza sus traducciones que decían
"ticket" (estas sí tienen prioridad sobre el paquete) y
`datos/es_MX/help/tips/` hace lo mismo con las ayudas emergentes.

## Datos en español (correos, portal, catálogos)

Además de las frases de la interfaz, el paquete oficial trae los **datos**
que el instalador guarda en la base de datos: plantillas de correo, páginas
del portal, colas, estados, prioridades, formularios, etc. MiHelpdesk los
ajusta sin modificar el paquete:

* `datos/es_MX/…`: archivos completos redactados para MiHelpdesk que
  reemplazan a los del paquete (las 19 plantillas de correo, las 13 páginas,
  las solicitudes de bienvenida, la organización y las respuestas de ejemplo).
* `datos/es_MX.php`: correcciones puntuales a textos del paquete (por ejemplo
  "Procurador" → "Asignado a").
* En todos los datos, "osTicket" se muestra con el nombre del producto.

Esto aplica solo al instalar. Para una instalación que ya existe (sobre todo
si se instaló en inglés) usa:

```bash
php tools/i18n/cargar-idioma.php es_MX            # vista previa
php tools/i18n/cargar-idioma.php es_MX --aplicar  # aplica los cambios
```

Cambia solo los textos que siguen igual a los de la instalación en inglés;
lo que ya editó el administrador se deja como está. También crea el juego de
plantillas de correo en español y lo deja como predeterminado.

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
