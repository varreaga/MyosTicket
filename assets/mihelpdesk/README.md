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
| `css/client.css` | Portal de clientes | Estilos del portal |
| `css/staff.css` | Panel de agentes y de administración | Estilos del panel |
| `css/login.css` | Inicio de sesión de agentes | Estilos de la pantalla de acceso |

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

Las funciones `app_theme()`, `app_theme_styles()` y
`app_theme_body_class()` están en `bootstrap.php`. Se llaman desde
`include/client/header.inc.php`, `include/staff/header.inc.php` e
`include/staff/login.header.php`.
