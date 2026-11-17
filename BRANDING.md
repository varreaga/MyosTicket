Marca del producto (branding)
=============================

El nombre actual, **MiHelpdesk**, es **provisional**. Este documento
explica cómo cambiarlo y qué reglas seguir para que el fork siga siendo
legal.

Cambiar el nombre
-----------------
Desde la raíz del repositorio:

    pip install pillow pyyaml
    python3 tools/branding/rebrand.py "NombreNuevo"
    git diff --stat

El script:

1. Cambia `APP_NAME` en `bootstrap.php`. De esa constante dependen todos
   los títulos, pies de página y textos del panel, el portal y el instalador.
2. Sustituye el nombre en los datos de instalación (`include/i18n/en_US`),
   en la documentación y en los avisos de modificación.
3. Regenera los logos provisionales de texto con el nombre nuevo.

Si solo quieres cambiar el nombre y poner tus propios logos a mano, usa
`--sin-logos`.

> Los datos de instalación (plantillas de correo, organización de ejemplo,
> tickets de bienvenida) se copian a la base de datos **al instalar**. En una
> instalación que ya existe, el cambio de nombre en esos textos no se aplica
> solo: hay que editarlos desde el panel de administración.

Poner tu propio logo
--------------------
Reemplaza estos archivos manteniendo el **mismo nombre y tamaño**:

| Archivo | Tamaño (px) | Dónde aparece |
|---|---|---|
| `assets/default/images/logo.png` | 817 × 170 | Portal de clientes |
| `scp/images/ost-logo.png` | 395 × 132 | Panel de agentes |
| `scp/images/osticket-grey.png` | 110 × 26 | Pie de la pantalla de inicio de sesión de agentes |
| `setup/images/logo.png` | 560 × 144 | Instalador |
| `include/fpdf/print-logo.png` | 817 × 170 (escala de grises) | PDF al imprimir tickets |
| `assets/default/images/poweredby.png` | 218 × 40 | Imagen "powered by" |
| `images/favicon.png`, `images/oscar-favicon-16x16.png`, `images/oscar-favicon-32x32.png` | 964², 16², 32² | Ícono del navegador |

La imagen del pie de los correos está incrustada en base64 en
`include/i18n/en_US/file.yaml`. `rebrand.py` la regenera; si usas un logo
propio, cambia el script para que la tome de tu archivo.

En una instalación ya en marcha también puedes subir los logos desde
el panel de administración (Configuración → Company, en la sección de
logos; ruta `scp/settings.php?t=pages`), sin tocar el código.

Reglas para no tener problemas legales
--------------------------------------
* **No uses "osTicket" como nombre ni como logo** de tu producto. Es una
  marca de Enhancesoft. Sí puedes decir que tu producto está "basado en
  osTicket", porque eso describe con verdad su origen.
* **No quites** los avisos de copyright de los archivos fuente ni
  `LICENSE.txt`.
* **Registra cada cambio** en `NOTICE.md` (fecha, autor, qué cambió) y
  agrega la línea de aviso al inicio de cada archivo que modifiques.
* **No renombres** clases ni funciones internas como `osTicket::`. No es
  marca visible y complica traer las actualizaciones de seguridad de
  osTicket.
* Elige un nombre que **no se confunda** con osTicket. Antes de decidirlo,
  busca si ya está registrado como marca en la oficina de marcas de tu
  país.

Traer actualizaciones de osTicket
---------------------------------
Agrega el repositorio original como `upstream` y une sus versiones:

    git remote add upstream https://github.com/osTicket/osTicket
    git fetch upstream --tags
    git merge v1.18.5   # por ejemplo

Los conflictos aparecerán sobre todo en los archivos listados en
`NOTICE.md`. Al resolverlos, conserva tus cambios de marca.
