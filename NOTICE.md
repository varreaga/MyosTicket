<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="assets/mihelpdesk/marca/mihelpdesk-logo-oscuro.png">
    <img src="assets/mihelpdesk/marca/mihelpdesk-logo.png" alt="MiHelpdesk" width="260">
  </picture>
</p>

Aviso de modificaciones (NOTICE)
================================

MiHelpdesk es una obra derivada de **osTicket**
(https://github.com/osTicket/osTicket), Copyright (c) osTicket,
Enhancesoft, LLC y sus colaboradores, distribuido bajo la Licencia Pública General de GNU,
versión 2 (GPLv2). MiHelpdesk se distribuye bajo esa misma licencia; ver
[LICENSE.txt](LICENSE.txt).

"osTicket" es una marca de Enhancesoft, LLC. MiHelpdesk no está afiliado,
patrocinado ni respaldado por Enhancesoft, LLC.

Este archivo cumple con la sección 2(a) de la GPLv2, que exige que los
archivos modificados indiquen de forma visible que se cambiaron y la fecha
del cambio. Además, cada archivo de texto modificado lleva al inicio una
línea de aviso.

Base
----
* Proyecto original: osTicket v1.18.4
* Commit de origen en este fork: `8d38b06` (17 de junio de 2026)

Historial de cambios
--------------------

### 2026-11-26 — Editor: menciones, enlaces predefinidos y ventana de enlace (varreaga)

* `include/mihelpdesk/class.mentions.php` (nuevo): al publicar una nota
  interna con «@Nombre Apellido» o «@usuario», se avisa por correo al
  agente mencionado (si tiene acceso, está disponible y no es quien
  escribe), aunque no le toquen los avisos normales.
* `include/class.ticket.php`, `include/class.task.php`: `onActivity()`
  devuelve a quiénes avisó y `postNote()` llama a los avisos de menciones,
  sin repetir el correo a quien ya lo recibió.
* `tools/editor/` (y `js/mh-editor.js`, `css/mh-editor.css` compilados):
  - La ventana de enlace tiene la lista de **enlaces predefinidos** del
    panel (enlace a la solicitud para agente o cliente, páginas de acceso),
    como en el editor anterior; se reemplazan al enviar.
  - Corregido: el botón Aceptar de las ventanas de enlace, imagen y video
    no hacía nada si la dirección no empezaba con `https://`.
  - Sin texto seleccionado, el enlace se inserta con su nombre o dirección.
* `include/mihelpdesk/i18n/es.php`: textos de los avisos y de los enlaces
  predefinidos.
* `README.md`: cómo activar la compresión en Apache y nginx.

### 2026-11-25 — Logo de fábrica sin copia vieja (varreaga)

* `bootstrap.php` (`app_logo_img()`): si la empresa no subió logo, el logo
  de fábrica (claro y oscuro) se enlaza directo con su fecha como versión.
  Antes pasaba por `logo.php`, cuya redirección el navegador guardaba un
  día, y después de actualizar seguía mostrando el logo anterior.
* `logo.php`, `scp/logo.php`: la redirección al logo de fábrica lleva
  `?v=<fecha del archivo>`.
* `include/mihelpdesk/i18n/es_MX.paquete.php`: «Automático» con acento
  (el paquete decía «Automatico»).
### 2026-11-25 — Traducciones de JavaScript sin copia vieja (varreaga)

* `include/ajax.i18n.php`: el archivo de traducciones para JavaScript se
  guarda en caché según su contenido (ETag). osTicket usaba la fecha de la
  configuración, así que después de actualizar el navegador seguía con la
  copia vieja y mostraba textos nuevos en inglés (p. ej. «Skip to content»).
* `bootstrap.php`: `app_i18n_version()`, versión según la fecha de los
  archivos de idioma.
* `include/client/footer.inc.php`, `include/staff/footer.inc.php`: la
  dirección de ese archivo lleva `?v=<versión>`, para que el navegador lo
  pida de nuevo al cambiar las traducciones.

### 2026-11-25 — Ventana «Un momento, por favor» (varreaga)

* `assets/mihelpdesk/css/widgets.css`: la ventana de espera de osTicket
  (`#loading`, al enviar formularios del portal y al cargar en el panel)
  pasa a ser una tarjeta centrada con indicador giratorio del color de
  marca, en modo claro y oscuro; el fondo oscurecido usa el color del tema.
* `assets/mihelpdesk/js/a11y.js`: la ventana se anuncia a los lectores de
  pantalla (`role="status"`).
* `include/mihelpdesk/i18n/es_MX.paquete.php`: «Un momento, por favor»,
  «Estamos procesando la información.» y «Cargando…» en lugar de las
  traducciones del paquete sin signo de apertura.

### 2026-11-25 — Documentación con el logo (varreaga)

* `README.md`: logo al inicio (versión clara y oscura según el tema de
  GitHub), sección «Qué incluye» con capturas, y explicación del logo y de
  cómo cada empresa lo reemplaza por el suyo.
* `SECURITY.md`: reescrito en español; los reportes de seguridad de
  MiHelpdesk se hacen en privado desde la pestaña Security del repositorio.
* `ISSUE_TEMPLATE.md`: traducido y apuntando a este repositorio.
* `WHATSNEW.md`: resumen de las novedades de MiHelpdesk al inicio; el
  historial de versiones de osTicket se conserva sin cambios.
* Logo al inicio de `BRANDING.md`, `MIGRAR-DESDE-OSTICKET.md`, `NOTICE.md`,
  `SOPORTE.md` y de los README de `assets/mihelpdesk/`,
  `include/mihelpdesk/i18n/` y `tools/editor/`.
* `docs/capturas/` (nuevo): capturas del panel y del portal usadas en el
  README.

### 2026-11-24 — Accesibilidad y tabletas (varreaga)

* `assets/mihelpdesk/js/a11y.js` (nuevo): da nombre accesible a los campos
  de los formularios de osTicket que no tienen `<label>`, a los enlaces y
  botones de solo icono, agrega «Saltar al contenido», corrige atributos
  `lang` (es_MX → es-MX) y hace alcanzables con el teclado las zonas con
  desplazamiento lateral. Se carga desde `app_theme_scripts()` en
  `bootstrap.php`.
* `include/staff/login.header.php`: el idioma de la página ya no está fijo
  en inglés, y la recarga cada 2 horas (para renovar el token) se hace solo
  si los campos están vacíos, en lugar de con `<meta refresh>`.
* `include/client/header.inc.php`: atributo `lang` con formato válido.
* `include/mihelpdesk/class.brand.php`: si el color de marca no permite
  texto legible en los botones (contraste menor a 4.5:1), se oscurece lo
  necesario.
* Estilos del tema: indicador de foco para todo lo que se enfoca, enlaces
  subrayados dentro de párrafos, barra lateral como cajón hasta 1100 px,
  tablas con desplazamiento lateral en tabletas y campos traducibles que
  caben en celular.
* Traducciones del paquete corregidas: «Equipo de actualización» →
  «Actualizar equipo» y otros títulos de edición.
### 2026-11-24 — PDF de solicitudes y tareas con diseño propio (varreaga)

* `include/class.pdf.php`: con el tema activo, los PDF usan las plantillas
  nuevas de `include/mihelpdesk/print/` (agente, cliente y tarea). Sin
  tema se usan las originales.
* `scp/tasks.php`: imprimir una tarea indicando el tamaño de papel ya no
  da error (se pasaba texto donde `Task::pdfExport()` espera un arreglo).
* Se agrega la biblioteca `mpdf/qrcode` (LGPL-2.1 o posterior) en
  `include/mpdf/vendor/mpdf/qrcode/`, solo con los datos de las versiones
  1 a 10 del código QR. Ver `LEEME-MIHELPDESK.md` en esa carpeta.

### 2026-11-24 — Logo de MiHelpdesk (varreaga)

* Logo propio del producto, el búho copyleft (`assets/mihelpdesk/marca/`).
  Se reemplazaron las imágenes de fábrica que traía osTicket con el mismo
  nombre y tamaño: `assets/default/images/logo.png` y `poweredby.png`,
  `scp/images/ost-logo.png` y `osticket-grey.png`, `setup/images/logo.png`,
  `include/fpdf/print-logo.png`, `images/favicon.png`,
  `images/oscar-favicon-16x16.png` y `-32x32.png`, y la imagen del pie de
  los correos en `include/i18n/en_US/file.yaml`. Nuevas: `logo-dark.png` y
  `ost-logo-dark.png` para el modo oscuro.
* `bootstrap.php`: `app_logo_img()` usa el logo oscuro de fábrica cuando la
  empresa no subió logo propio.
* `tools/branding/rebrand.py`: ya no reemplaza los logos salvo con
  `--logos-de-texto`.
* El texto del logo usa la tipografía Inter (SIL Open Font License 1.1),
  convertida a trazos.

### 2026-11-24 — Migración desde osTicket (varreaga)

* `scp/upgrade.php`: con el tema activo, las pantallas del actualizador
  salen de `include/mihelpdesk/upgrader/` (archivos nuevos) con el estilo
  del instalador. Sin tema se usan las originales.
* `scp/js/upgrader.js`: los mensajes del actualizador se traducen (la
  comprobación original de la función de traducción siempre fallaba).
* `include/class.i18n.php`: `DataTemplate` puede leer solo el paquete de
  idioma oficial (`$propios = false`) y devolver sus datos tal cual
  (`getOriginalData()`).
* `tools/i18n/cargar-idioma.php`: reconoce los textos que dejó un osTicket
  instalado en español (páginas y plantillas de correo del paquete oficial)
  y actualiza las plantillas de un juego de correo ya existente.
* Guía nueva: `MIGRAR-DESDE-OSTICKET.md`.

### 2026-11-24 — Instalador con el estilo del tema (varreaga)

* `setup/install.php`: con el tema activo, las pantallas del instalador
  salen de `include/mihelpdesk/setup/` (archivos nuevos) en lugar de
  `setup/inc/`. Con `define('APP_THEME', '')` vuelve el instalador
  original sin cambios.
* Archivos nuevos: `include/mihelpdesk/setup/*.inc.php` y
  `assets/mihelpdesk/css/setup.css`. Textos en español en
  `include/mihelpdesk/i18n/es.php`.

### 2026-11-23 — Compatibilidad con PHP 8.5 (varreaga)

* `include/JSON.php`, `include/class.orm.php`, `include/class.misc.php`,
  `include/class.staff.php`, `include/class.forms.php`, `scp/faq.php`,
  `include/staff/system.inc.php`, `include/cli/modules/i18n.php` y
  `include/cli/modules/package.php`: se cambió la sintaxis que PHP 8.5
  marca como obsoleta (conversiones `(integer)` y `(double)`, `case` con
  punto y coma, el operador de comillas invertidas y `"${var}"`). El
  comportamiento no cambia.
* Traducciones: se corrigió la pantalla final del instalador.

### 2026-11-21 — Avisos con el estilo del tema (varreaga)

* Archivos propios del tema (`assets/mihelpdesk/`): los avisos de
  resultado (verde, amarillo, rojo, informativo), incluido el que sale al
  volver a la cola tras responder (`.success-banner`), y la barra flotante
  de osTicket (`$.messageBar`, usada al vencer el apartado de una
  solicitud) ya no usan los iconos .png ni el muñequito "Oscar".
* Traducciones: el aviso de apartado por vencer se explica en lenguaje
  claro ("¿Sigues trabajando en esta solicitud?") y se corrigieron
  "Publicar nota interna" y "¡Marcada como atrasada!".

### 2026-11-21 — Detalles de los correos (varreaga)

* `setup/inc/class.installer.php`: los correos que crea el instalador
  llevan nombre en el idioma de la instalación ("Soporte", "Alertas de
  MiHelpdesk") y la respuesta automática al cliente queda encendida.
* Encabezado de marca de los correos: los enlaces de las plantillas (también
  los que no tienen estilo, como el número de solicitud) toman el color de
  la empresa y el nombre de la empresa usa la misma letra que
  el resto del correo.
* `tools/i18n/cargar-idioma.php`: también traduce la firma de los
  departamentos y los nombres de los correos.

### 2026-11-21 — Créditos más cortos (varreaga)

* La línea de créditos del portal, del inicio de sesión, de las páginas de
  contraseña y del instalador dice ahora "Basado en osTicket bajo licencia
  GNU GPL v2." (`include/client/footer.inc.php`,
  `include/staff/login.tpl.php`, `pwreset.php`, `pwreset.sent.php`,
  `pwreset.login.php` y `setup/inc/footer.inc.php`).

### 2026-11-21 — Estado del sistema sin la alerta de imap (varreaga)

* `include/staff/system.inc.php`: ya no revisa la extensión `imap` de PHP.
  osTicket descarga el correo con laminas-mail desde la versión 1.17, PHP
  8.4 dejó de incluir esa extensión y Debian 13 no la trae, así que solo
  mostraba una alerta sin motivo. Por lo mismo se quita de la lista de
  requisitos del instalador (`setup/inc/install-prereq.inc.php`).
* `include/staff/system.inc.php`: la revisión de `cgi.fix_pathinfo` solo se
  muestra cuando PHP corre como CGI o FPM; con PHP dentro de Apache esa
  opción no existe y la alerta salía sin motivo.

### 2026-11-21 — Personalización sin programar (varreaga)

Pantalla nueva **Administración › Configuración › Personalización**
(`scp/customize.php`, `include/staff/customize.inc.php`,
`include/mihelpdesk/class.customize.php`), con el tema activo:

* Marca: nombre del centro de soporte, color, logos del portal y del panel
  (también para modo oscuro) y favicon.
* Portada del portal: texto de bienvenida, textos de las tarjetas y qué
  secciones se muestran, con vista previa.
* Menú y pie del portal: páginas propias, enlaces (WhatsApp, teléfono,
  redes) y texto.
* Inicio de sesión de agentes y clientes: título y texto.
* Correos: encabezado con el logo (incrustado) y el color de la empresa, y
  pie propio.

Archivos de osTicket modificados para esto: `bootstrap.php` (ayudas
`app_favicon_tags()` y `app_logo_img()`), `logo.php` y `scp/logo.php`
(`?favicon` y `?dark`), `include/class.nav.php` (menú),
`include/class.config.php` (la pantalla Empresa ya no borra los logos cuando
no los envía), `include/class.mailer.php` (encabezado de marca),
`include/staff/settings-pages.inc.php` (con el tema, logos, fondo y color
se mueven a Personalización), `include/staff/header.inc.php`,
`include/staff/login.header.php`, `include/staff/login.tpl.php`,
`include/client/header.inc.php` e `include/client/footer.inc.php`.

### 2026-11-19 — Revisión visual completa (varreaga)

* `include/staff/footer.inc.php`: los textos de JavaScript se cargan en el
  idioma efectivo del agente. Si el agente tenía "predeterminado del
  sistema", se quedaban en inglés (por ejemplo, "all changes saved" en el
  editor).
* Textos que osTicket mostraba sin traducir: `include/staff/helptopics.inc.php`
  (estado de los temas), `user-view.inc.php` y `org-view.inc.php`
  ("Reload"), `faq.inc.php`, `page.inc.php`, `category.inc.php` y
  `dynamic-form.inc.php` ("This content is translatable"),
  `slaplans.inc.php` ("(Default)"), `settings-system.inc.php` ("Apply To"),
  `templates/schedule-entries.tmpl.php` (zona horaria),
  `template.inc.php` (grupos de plantillas) e `include/class.schedule.php`
  (días de la semana).
* `tools/editor/src/editor.css` y `css/mh-editor.css`: el área de texto del
  editor ya no dibuja un segundo recuadro oscuro al tener el foco.
* Tema (`assets/mihelpdesk/`): restos del estilo original (grises fijos,
  iconos .png de los avisos, flecha de los selectores, botones grises del
  portal), barra de acciones no fija en pantallas chicas, "Mis solicitudes"
  como tarjetas en el celular e iniciales cuando el avatar no carga.
* `tools/i18n/cargar-idioma.php`: también traduce los formularios de
  empresa, organización y tarea, las propiedades de los estados y su
  descripción.

### 2026-11-19 — "Solicitud" en todo el sistema (varreaga)

El paquete oficial en español mezcla "ticket", "tiquete", "boleto" y
"solicitud". MiHelpdesk usa "solicitud" en todos lados.

* Nuevo: `include/mihelpdesk/i18n/es_MX.paquete.php`, 241 traducciones del
  paquete reescritas con "solicitud" y el género corregido ("la solicitud
  cerrada"). A diferencia de `es.php`, estas sí tienen prioridad sobre el
  paquete, que no se modifica.
* Nuevo: `include/mihelpdesk/i18n/datos/es_MX/help/tips/` (29 archivos),
  copias de las ayudas emergentes del paquete con el mismo cambio.
* `include/class.translation.php` y `include/ajax.i18n.php`: cargan esos
  ajustes en PHP y en JavaScript.
* `include/class.i18n.php`: `DataTemplate::getData(false)` devuelve los datos
  del paquete sin correcciones y `DataTemplate::anteriores()` los textos que
  MiHelpdesk usó antes, para que `tools/i18n/cargar-idioma.php` pueda volver
  a correrse sobre una instalación ya pasada a español.
* Colas y estados en femenino ("Abiertas", "Cerrada"); etiquetas del tablero
  ("Abiertas", "Sin asignar", "Vencidas") con textos propios
  (`include/staff/templates/mh-dashboard.tmpl.php`, `scp/dashboard.php`).

### 2026-11-19 — Datos en español y "Estado del sistema" (varreaga)

* `include/class.i18n.php` (`DataTemplate`): para idiomas distintos de
  inglés, primero busca los datos en `include/mihelpdesk/i18n/datos/<idioma>/`
  (plantillas de correo, páginas del portal, solicitudes de bienvenida,
  organización y respuestas de ejemplo redactadas para MiHelpdesk), aplica
  las correcciones de `datos/<idioma>.php` a los datos del paquete oficial y
  cambia "osTicket" por el nombre del producto. `file.yaml` (imágenes) se toma
  siempre de `en_US`. El paquete oficial no se modifica.
* `include/class.translation.php`: las traducciones del paquete oficial
  muestran el nombre del producto en lugar de "osTicket".
* `include/class.nav.php`: la primera pestaña de Admin Panel se llama
  "System status" ("Estado del sistema"); abre los registros e información
  del sistema, no un tablero.
* `include/staff/login.tpl.php`: el mensaje por omisión del inicio de sesión
  se reconoce sin importar mayúsculas (el paquete dice "Autenticación
  Requerida").
* Nuevo: `tools/i18n/cargar-idioma.php`, que pasa a otro idioma los textos
  que una instalación en inglés guardó en la base de datos (solo los que
  nadie ha editado) y crea el juego de plantillas de correo en ese idioma.

### 2026-11-19 — Español incluido de fábrica (varreaga)

* Nuevo: `include/i18n/es_MX.phar`, el paquete de idioma **oficial** de
  osTicket para español (México), sin modificar (frases 1.18, generado el
  2025-02-04 a partir de osTicket v1.18.2; SHA-256
  `af5cd19153221bf85f8f4e51ca54db25d28efbcaef56dd2dd7090e6115ec18ef`).
  Las traducciones de osTicket son obra de sus traductores en Crowdin y se
  distribuyen bajo la misma licencia GPLv2.
* `.gitignore`: excepción para incluir ese paquete en el repositorio.
* `setup/inc/install.inc.php`: el instalador sugiere español (México) como
  idioma principal cuando el paquete está presente.

### 2026-11-19 — Color de marca configurable (varreaga)

El administrador elige el color principal en **Admin Panel › Settings ›
Company › Brand color** (hexadecimal o RGB). MiHelpdesk calcula sus tonos
(al pasar el ratón, fondos suaves, enlaces con contraste AA, texto de los
botones y variantes del modo oscuro) y reemplaza con ellos los azules de
`tokens.css`.

* Nuevo: `include/mihelpdesk/class.brand.php` (cálculo de los tonos).
* Modificados: `include/staff/settings-pages.inc.php` (pestaña "Brand
  color"), `include/class.config.php` (guarda el color; además, una clave
  de configuración nueva se puede leer en la misma petición) y
  `bootstrap.php` (`app_theme_styles()` agrega los tonos).

### 2026-11-19 — Créditos del inicio de sesión y botón de actualizar (varreaga)

* `include/staff/login.tpl.php` y `include/staff/pwreset*.php`: el pie decía
  "Powered by" con la imagen de MiHelpdesk pero enlazaba a osticket.com.
  Ahora dice "MiHelpdesk es software libre basado en osTicket, con
  licencia GNU GPL v2", con cada nombre enlazado a su propio sitio.
* `bootstrap.php`: nueva constante `APP_URL` (sitio del proyecto; por ahora
  el repositorio en GitHub).
* `include/staff/system.inc.php`: el botón "Upgrade" de Admin Panel ›
  Information lleva a las versiones de MiHelpdesk en lugar de la descarga de
  osTicket.

### 2026-11-19 — Inicio de sesión del panel (varreaga)

Con el tema activo, el inicio de sesión de agentes y la página para
restablecer la contraseña usan una tarjeta centrada con título, campos con
etiqueta y botón a todo lo ancho, en modo claro u oscuro. El error de acceso
se muestra como aviso. Ya no se usa la foto de fondo del original.

* Modificados: `include/staff/login.tpl.php` e `include/staff/pwreset.php`
  (título y etiquetas de los campos, solo con el tema activo) y
  `assets/mihelpdesk/css/login.css`.

### 2026-11-19 — Modo oscuro (varreaga)

Apariencia clara, oscura o automática (según el sistema), en el portal, el
panel y la página de inicio de sesión. La elección se guarda en el
navegador.

* Nuevo: `assets/mihelpdesk/js/common.js` (botones de apariencia).
* Modificados:
  * `bootstrap.php`: `app_theme_head()` aplica la apariencia antes de
    pintar la página; `app_theme_appearance_options()` dibuja los botones;
    `app_theme_scripts()` carga `common.js`.
  * `include/client/header.inc.php`, `include/staff/header.inc.php` e
    `include/staff/login.header.php`: llaman a `app_theme_head()`; el
    portal tiene un botón de apariencia y el panel lo muestra en el menú del
    usuario.
  * Tema: colores del modo oscuro en `tokens.css`; colores fijos cambiados
    por variables en `staff.css`, `client.css`, `widgets.css` y en el
    editor (`tools/editor/src/editor.css`).

### 2026-11-19 — Traducciones propias de MiHelpdesk (varreaga)

Los textos que agregó MiHelpdesk no existen en los paquetes de idioma de
osTicket. Ahora se traducen con archivos propios que se suman al paquete
oficial sin modificarlo; si el paquete ya traduce un texto, se respeta.

* Nuevos: `include/mihelpdesk/i18n/es.php` (130 textos al español),
  `include/mihelpdesk/i18n/README.md` y `tools/i18n/textos-nuevos.py`
  (lista los textos nuevos sin traducir).
* Modificados: `include/class.translation.php` (carga las traducciones
  propias) e `include/ajax.i18n.php` (las entrega también al JavaScript).

### 2026-11-18 — Tareas y calendario (varreaga)

* Nuevo: `assets/mihelpdesk/css/widgets.css`, estilos comunes del tema
  (calendario para elegir fecha y hora), que se carga en todas las áreas.
* Modificado: `bootstrap.php` (`app_theme_styles()` carga `widgets.css`).
* El calendario muestra un solo mes y el ícono va dentro del campo; las
  ventanas emergentes (por ejemplo, Nueva tarea) ya no quedan tapadas por la
  barra de acciones; la vista de tarea sigue el diseño de la de ticket.

### 2026-11-18 — Tablero nuevo del panel de agentes (varreaga)

Con el tema activo, el tablero del panel se reemplaza por uno nuevo. El
tablero original de osTicket sigue disponible al desactivar el tema.

* Nuevos: `include/class.mh-dashboard.php` (cálculos),
  `include/staff/templates/mh-dashboard.tmpl.php` (pantalla) y
  `tools/dev/seed-history.php` (historial de demostración).
* Modificado: `scp/dashboard.php` (usa el tablero nuevo con el tema activo;
  enlaces de las tarjetas y exportación a CSV).
* Indicadores: tickets abiertos, sin asignar, vencidos y asignados al
  agente; primera respuesta, tiempo hasta resolver y resueltos a tiempo,
  con su cambio frente al periodo anterior; tickets por día; desempeño por
  agente, departamento o tema.
* Los tiempos se calculan en minutos y se muestran en horas y minutos. El
  tablero original los calculaba en horas completas, sin unidad visible.

### 2026-11-18 — Fase 4: rediseño del panel de agentes (varreaga)

El panel de agentes y el de administración usan el tema visual. Los estilos
van en `assets/mihelpdesk/css/staff.css` y `assets/mihelpdesk/js/staff.js`
(nuevo); no se modificó ningún CSS de osTicket.

* Modificados:
  * `include/staff/header.inc.php`: barra superior del tema (buscador,
    botón de nuevo ticket y menú del usuario) y etiqueta `viewport` para
    celular, solo con el tema activo.
  * `include/staff/footer.inc.php`: carga el JavaScript del tema.
  * `include/staff/ticket-view.inc.php`: contenedores para la vista del
    ticket en dos columnas, solo con el tema activo.
* Barra lateral con íconos (cajón en celular), pestañas de sección como
  botones, lista de tickets en tarjeta con prioridad y estado de color,
  conversación con tarjetas por tipo de mensaje, detalles del ticket en
  columna lateral, formularios de configuración en tarjetas y diálogos
  adaptados a celular.

### 2026-11-17 — Fase 3: editor Tiptap en lugar de Redactor (varreaga)

Se reemplazó el editor Redactor 3 (Imperavi, licencia comercial que no
permite publicarlo en un proyecto público) por un editor propio construido
con Tiptap (licencia MIT). Ver `tools/editor/THIRD-PARTY.md`.

* Nuevo: código fuente en `tools/editor/` y archivos generados
  `js/mh-editor.js` y `css/mh-editor.css`.
* Eliminados: `js/redactor.min.js`, `js/redactor-plugins.js`,
  `js/redactor-osticket.js` y `css/redactor.css`.
* Modificados:
  * `include/client/header.inc.php`, `include/staff/header.inc.php` e
    `include/staff/footer.inc.php`: cargan el nuevo editor.
  * `include/ajax.staff.php` y `scp/ajax.php`: búsqueda de agentes para
    las menciones con "@".
  * `setup/test/tests/class.test.php`: lista de exclusión.
* Se conservan los borradores automáticos, la subida de imágenes, las
  respuestas predefinidas, las variables, las firmas, el bloqueo de ticket,
  las traducciones y la vista HTML. Se agregan el menú con "/", la
  autocompletación de variables con "%{", las menciones con "@", la barra
  flotante al seleccionar texto y los atajos de Markdown.
* No se portó la anotación de imágenes (`imageannotate`). Las menciones
  solo insertan el nombre; todavía no envían avisos.

### 2026-11-17 — Portal: todas las páginas con el tema (varreaga)

Se completó el rediseño en las páginas del portal que aún mezclaban el
diseño anterior. Solo cambian archivos del tema; no se modificó ningún
archivo de osTicket.

* Registro, perfil y edición de ticket: tarjeta, etiquetas y secciones
  con el estilo del tema; sin anchos fijos en celular.
* Abrir ticket con sesión iniciada: bloque del cliente ordenado.
* Base de conocimiento: listas de preguntas en categorías y búsqueda.
* Páginas del sitio (tipo "otra") con barra lateral adaptable.
* Tipografía del tema en el texto de mensajes, artículos y editor.
* `tools/dev/seed-demo.php` crea además una página de ejemplo
  ("Horarios de atención").

### 2026-11-16 — Fase 2: rediseño del portal de clientes (varreaga)

Rediseño del portal de clientes con la capa de tema `assets/mihelpdesk/`.
Con el tema desactivado (`define('APP_THEME', '')`) el portal se ve y
funciona como el original.

* Diseño adaptable a celular, con menú desplegable.
* Nueva página de inicio: buscador, accesos rápidos, artículos populares y
  categorías (`include/client/templates/mh-home.tmpl.php`, archivo nuevo).
* Abrir ticket: selector de temas en dos niveles (tema principal y
  subtemas) construido sobre el `<select>` original.
* Vista de ticket como conversación; estado con color.
* Base de conocimiento, acceso, registro y lista de tickets con el estilo
  del tema; editor y zona de adjuntos integrados.
* Nueva función `app_theme_scripts()` en `bootstrap.php` y archivo
  `assets/mihelpdesk/js/client.js`.
* Herramientas de desarrollo: `tools/dev/seed-demo.php` (datos de
  demostración) y `tools/dev/access-link.php` (enlace de acceso de cliente).

Archivos modificados en este cambio:

* `bootstrap.php`
* `index.php`
* `include/client/header.inc.php`
* `include/client/open.inc.php`
* `include/client/view.inc.php`

### 2026-11-16 — Base del tema visual (varreaga)

Se agregó una capa de tema propia que se carga después de las hojas de
estilo originales, sin modificarlas. Por ahora no cambia el aspecto: solo
define las variables de diseño.

* Nuevas funciones `app_theme()`, `app_theme_styles()` y
  `app_theme_body_class()` en `bootstrap.php`. El tema se puede desactivar
  con `define('APP_THEME', '')` en `include/ost-config.php`.
* `include/client/header.inc.php`, `include/staff/header.inc.php` e
  `include/staff/login.header.php` cargan el CSS del tema y agregan clases
  al `<body>`.
* Nueva carpeta `assets/mihelpdesk/` (tokens.css, client.css, staff.css,
  login.css y README.md).
* `include/cli/modules/deploy.php`: la carpeta `tools/` ya no se copia al
  servidor web al desplegar.

Archivos modificados en este cambio:

* `bootstrap.php`
* `include/client/header.inc.php`
* `include/staff/header.inc.php`
* `include/staff/login.header.php`
* `include/cli/modules/deploy.php`

### 2026-11-16 — Cambio de marca (varreaga)

Se reemplazó la marca visible de osTicket por el nombre del producto, sin
alterar el funcionamiento del sistema.

* Se agregaron las constantes `APP_NAME`, `APP_UPSTREAM_NAME` y
  `APP_UPSTREAM_URL` y la función `brand()` en `bootstrap.php`.
* Títulos de página, texto alternativo de logos, pie de página y asunto del
  correo de prueba usan ahora `APP_NAME`.
* El pie del portal de clientes indica que el producto es software libre
  basado en osTicket bajo GPLv2, con enlace al proyecto original.
* Se reemplazaron los logos, el ícono del navegador y la imagen
  "powered by" de los correos por versiones provisionales de texto.
* Se quitaron del instalador, el actualizador y los tickets de bienvenida
  los anuncios de servicios comerciales de Enhancesoft y SupportSystem, y
  se reemplazaron por un aviso neutral de soporte profesional.
* La organización de ejemplo que crea el instalador ya no contiene los
  datos de contacto de Enhancesoft.
* Se agregaron README.md (reescrito), NOTICE.md, BRANDING.md, SOPORTE.md y
  la herramienta `tools/branding/rebrand.py`.

**Qué NO se modificó:** los avisos de copyright de los archivos fuente,
LICENSE.txt, los nombres internos del código (por ejemplo la clase
`osTicket`), los enlaces a la documentación y foros oficiales de osTicket,
y las licencias de las bibliotecas de terceros.

Archivos modificados en este cambio:


**Código PHP (textos visibles y constantes de marca)**

* `bootstrap.php`
* `include/ajax.content.php`
* `include/class.osticket.php`
* `include/class.setup.php`
* `include/class.upgrader.php`
* `include/client/footer.inc.php`
* `include/client/header.inc.php`
* `include/staff/footer.inc.php`
* `include/staff/header.inc.php`
* `include/staff/login.header.php`
* `include/staff/login.tpl.php`
* `include/staff/pwreset.login.php`
* `include/staff/settings-system.inc.php`
* `include/staff/pwreset.php`
* `include/staff/pwreset.sent.php`
* `include/staff/system.inc.php`
* `include/upgrader/done.inc.php`
* `include/upgrader/prereq.inc.php`
* `include/upgrader/rename.inc.php`
* `include/upgrader/upgrade.inc.php`
* `scp/admin.inc.php`
* `scp/emailtest.php`
* `scp/staff.inc.php`
* `setup/inc/class.installer.php`
* `setup/inc/file-missing.inc.php`
* `setup/inc/file-perm.inc.php`
* `setup/inc/file-unclean.inc.php`
* `setup/inc/footer.inc.php`
* `setup/inc/header.inc.php`
* `setup/inc/install-done.inc.php`
* `setup/inc/install-prereq.inc.php`
* `setup/inc/install.inc.php`
* `setup/install.php`
* `setup/tips.php`

**Datos y plantillas de instalación (YAML)**

* `include/i18n/en_US/email_template_group.yaml`
* `include/i18n/en_US/file.yaml`
* `include/i18n/en_US/organization.yaml`
* `include/i18n/en_US/templates/email/assigned.alert.yaml`
* `include/i18n/en_US/templates/email/message.alert.yaml`
* `include/i18n/en_US/templates/email/note.alert.yaml`
* `include/i18n/en_US/templates/email/task.activity.alert.yaml`
* `include/i18n/en_US/templates/email/task.alert.yaml`
* `include/i18n/en_US/templates/email/task.assignment.alert.yaml`
* `include/i18n/en_US/templates/email/task.overdue.alert.yaml`
* `include/i18n/en_US/templates/email/task.transfer.alert.yaml`
* `include/i18n/en_US/templates/email/ticket.alert.yaml`
* `include/i18n/en_US/templates/email/ticket.overdue.yaml`
* `include/i18n/en_US/templates/email/transfer.alert.yaml`
* `include/i18n/en_US/templates/page/email2fa-staff.yaml`
* `include/i18n/en_US/templates/page/pwreset-staff.yaml`
* `include/i18n/en_US/templates/page/registration-staff.yaml`
* `include/i18n/en_US/templates/premade.yaml`
* `include/i18n/en_US/templates/ticket/installed.yaml`
* `include/i18n/en_US/templates/ticket/upgraded.yaml`

**Imágenes (logos e íconos)**

* `assets/default/images/logo.png`
* `assets/default/images/poweredby.png`
* `images/favicon.png`
* `images/oscar-favicon-16x16.png`
* `images/oscar-favicon-32x32.png`
* `include/fpdf/print-logo.png`
* `scp/images/ost-logo.png`
* `scp/images/osticket-grey.png`
* `setup/images/logo.png`
