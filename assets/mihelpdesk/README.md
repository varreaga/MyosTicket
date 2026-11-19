Tema visual de MiHelpdesk
=========================

Esta carpeta contiene el tema visual propio de MiHelpdesk. Cambia el aspecto
de la interfaz **sin modificar el CSS original de osTicket**: sus hojas de
estilo se cargan *después* de las originales y las sobrescriben.

Esto tiene dos ventajas:

* El tema se puede **desactivar** en cualquier momento para volver a la
  interfaz original, por ejemplo para comparar o para descartar que un
  error venga del tema.
* Al traer actualizaciones de osTicket casi no hay conflictos, porque sus
  archivos de CSS no se tocan.

Archivos
--------

| Archivo | Se carga en | Para qué sirve |
|---|---|---|
| `css/tokens.css` | Todas las áreas | Variables de diseño: colores, tipografía, espaciado, radios y sombras |
| `css/widgets.css` | Todas las áreas | Componentes comunes, como el calendario para elegir fecha y hora |
| `css/client.css` | Portal de clientes | Estilos del portal |
| `css/staff.css` | Panel de agentes y de administración | Estilos del panel |
| `css/login.css` | Inicio de sesión de agentes | Estilos de la pantalla de acceso |
| `js/client.js` | Portal de clientes | Menú en celular y selector de temas en dos niveles |
| `js/common.js` | Todas las áreas | Apariencia: claro, oscuro o automático |
| `js/staff.js` | Panel de agentes y de administración | Menú lateral en celular, menú del usuario, sugerencias del buscador, etiquetas de color en las colas e iniciales en la conversación |

El JavaScript es **mejora progresiva**: si no carga, el portal sigue
funcionando con los controles originales (por ejemplo, el `<select>` de
temas). Se carga con `app_theme_scripts()` y lleva `defer`.

Además, `include/client/templates/mh-home.tmpl.php` es la página de inicio
del tema; `index.php` la usa solo cuando el tema está activo.

El tablero del panel (`scp/dashboard.php`) usa
`include/staff/templates/mh-dashboard.tmpl.php` y los cálculos de
`include/class.mh-dashboard.php` cuando el tema está activo.

En el panel, `include/staff/header.inc.php` muestra la barra superior del
tema (buscador, nuevo ticket y menú del usuario) y
`include/staff/ticket-view.inc.php` agrega dos contenedores
(`.mh-ticket-main` y `.mh-ticket-side`) para la vista del ticket en dos
columnas. Ambos cambios solo se aplican con el tema activo.

El `<body>` de cada página recibe las clases `mh-theme` y `mh-client`,
`mh-staff` o `mh-login`. **Cada regla debe empezar con la clase de su área**
(por ejemplo `.mh-client #header { ... }`), para que no afecte otras áreas.

Reglas de trabajo
-----------------

1. **Colores y medidas, solo desde `tokens.css`.** Escribe
   `color: var(--mh-brand)`, no `color: #0072bc`. Así, cambiar la identidad
   visual consiste en editar un solo archivo.
2. **No edites** `assets/default/`, `css/`, `scp/css/` ni ningún otro CSS
   original.
3. **Cambios de HTML:** si una mejora necesita cambiar la estructura HTML
   de una plantilla PHP, hazlo en un cambio pequeño y aparte, y regístralo
   en `NOTICE.md` como cualquier otro archivo modificado.
4. **Prueba siempre en celular** (ancho de 390 px) además de en escritorio.

Activar y desactivar
--------------------

El tema está activo por defecto. Para desactivarlo, agrega esta línea en
`include/ost-config.php`:

```php
define('APP_THEME', '');
```

Para usar otro tema, crea una carpeta hermana (por ejemplo
`assets/cliente-x/`) con la misma estructura y escribe
`define('APP_THEME', 'cliente-x');`. Así puedes tener un tema distinto por
cliente.

Las hojas de estilo llevan `?v=<fecha de modificación>`, así que el
navegador las recarga solo cuando cambian.

Cómo funciona
-------------

Las funciones `app_theme()`, `app_theme_styles()`, `app_theme_scripts()` y
`app_theme_body_class()` están en `bootstrap.php`. Se llaman desde
`include/client/header.inc.php`, `include/staff/header.inc.php` e
`include/staff/login.header.php`.

Datos de demostración
---------------------

Para ver el tema con contenido realista (temas con subtemas, artículos y un
ticket con conversación), en una instalación de pruebas corre:

```bash
php tools/dev/seed-demo.php
```

Crea también un cliente con cuenta: `jorge.ramirez@example.com` /
`Demo12345!`. No lo uses en producción.

Para ver el tablero del panel con números, agrega un historial de unos 70
tickets repartidos en los últimos 60 días (con respuestas y cierres):

```bash
php tools/dev/seed-history.php
```

## Modo oscuro

Los colores del modo oscuro están en `css/tokens.css`, en el bloque
`html[data-mh-mode="dark"]`. El resto del tema usa solo variables
(`var(--mh-…)`), así que un color nuevo debe definirse en los dos bloques
de `tokens.css` (claro y oscuro) y no escribirse fijo en los demás archivos.

`app_theme_head()` (en `bootstrap.php`) pone en `<html>` los atributos
`data-mh-appearance` (lo que eligió la persona: `light`, `dark` o `auto`) y
`data-mh-mode` (lo que se aplica: `light` o `dark`) antes de que cargue el
CSS, para que no parpadee. La elección se guarda en el navegador
(`localStorage`, clave `mh-appearance`).

## Color de marca

El color principal se cambia sin tocar código, en **Admin Panel › Settings ›
Company › Brand color**. `include/mihelpdesk/class.brand.php` calcula los
tonos y `app_theme_styles()` los escribe después de `tokens.css`, así que
reemplazan a `--mh-brand`, `--mh-brand-hover`, `--mh-brand-soft`,
`--mh-brand-soft-hover`, `--mh-on-brand`, `--mh-link`, `--mh-link-hover`,
`--mh-focus` y `--mh-info-border` (en modo claro y oscuro). Si el campo
queda vacío se usan los valores de `tokens.css`.
