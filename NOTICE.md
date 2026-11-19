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
