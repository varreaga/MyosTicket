<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="assets/mihelpdesk/marca/mihelpdesk-logo-oscuro.png">
    <img src="assets/mihelpdesk/marca/mihelpdesk-logo.png" alt="MiHelpdesk" width="260">
  </picture>
</p>

Pasar de osTicket a MiHelpdesk
==============================

MiHelpdesk usa la misma base de datos que osTicket. Para pasar una
instalación de osTicket a MiHelpdesk no hay que exportar ni importar nada:
se cambia el código y se conservan la base de datos y el archivo de
configuración.

**Probado** con instalaciones de osTicket **1.18.4** y **1.17.8** en
español, con solicitudes, adjuntos, agentes y logo propio.

| Desde | Qué pasa |
|---|---|
| osTicket 1.18.x | Entra directo, sin actualizar la base de datos. |
| osTicket 1.17.x | El panel abre el actualizador, que aplica los cambios a la base de datos en menos de un minuto. |
| osTicket 1.16 o anterior | El actualizador de osTicket lo soporta, pero no está probado con MiHelpdesk. Hágalo primero en una copia. |


Qué se conserva
---------------
- Solicitudes, mensajes, notas y adjuntos.
- Usuarios, organizaciones, agentes, departamentos, equipos y permisos.
- Temas de ayuda, formularios, listas, colas, SLA y filtros.
- Correos configurados (cuentas, SMTP e IMAP) y plantillas de correo.
- Logos, páginas del portal y la base de conocimientos.
- Configuración general y contraseñas.

Qué **no** se conserva:
- Cambios hechos a mano en el código o en el CSS del osTicket anterior.
  MiHelpdesk trae su propio diseño; la marca de su empresa se cambia en
  **Administración › Configuración › Personalización**.
- Complementos (plugins) que no se copien a la carpeta nueva (ver paso 3).


Antes de empezar
----------------
- **PHP 8.2 o mayor** (probado hasta 8.5). osTicket 1.17 funcionaba con
  PHP 8.0 y 8.1; si su servidor tiene una versión anterior, actualícelo
  primero.
- Elija una hora con poca actividad. El sistema queda en pausa unos
  minutos.


Pasos
-----

### 1. Respalde todo

- **Base de datos:** desde phpMyAdmin (pestaña *Exportar*) o con:

      mysqldump -u USUARIO -p NOMBRE_BD > respaldo-osticket.sql

- **Archivos:** copie la carpeta completa de osTicket, por ejemplo
  `osticket` → `osticket-respaldo`.

### 2. Descargue MiHelpdesk en una carpeta nueva

Junto a la de osTicket, no encima de ella:

    git clone https://github.com/varreaga/MyosTicket mihelpdesk

(o descomprima el .zip de la versión en una carpeta `mihelpdesk`).

### 3. Copie su configuración

De la carpeta de osTicket a la de MiHelpdesk:

| Qué | Desde (osTicket) | Hacia (MiHelpdesk) |
|---|---|---|
| Configuración (obligatorio) | `include/ost-config.php` | `include/ost-config.php` |
| Complementos, si usa alguno | `include/plugins/*` | `include/plugins/` |
| Otros idiomas, si usa alguno | `include/i18n/*.phar` | `include/i18n/` (el de español ya viene incluido) |

Si guarda los adjuntos en disco (complemento *Attachments on the
filesystem*), la carpeta de adjuntos queda donde está; solo revise que el
servidor web pueda leerla.

Linux:

    cp osticket/include/ost-config.php mihelpdesk/include/

Windows (PowerShell):

    Copy-Item osticket\include\ost-config.php mihelpdesk\include\

### 4. Cambie las carpetas

Para que la dirección del sistema siga siendo la misma (los enlaces de
los correos ya enviados apuntan a ella):

    mv osticket osticket-anterior
    mv mihelpdesk osticket

En Windows, cambie los nombres desde el Explorador de archivos.

### 5. Entre al panel

Abra `https://su-servidor/scp/` con una cuenta de administrador.

- **Desde 1.18:** entra directo al panel.
- **Desde 1.17 o anterior:** aparece el actualizador. Pulse **Empezar la
  actualización** y luego **Actualizar ahora**. No cierre la página hasta
  que diga *La actualización terminó*.

### 6. Pase los textos a la nueva versión

Las páginas, plantillas de correo, colas, estados y formularios que
instaló osTicket conservan sus textos (en inglés o con las traducciones
del paquete de osTicket, que dicen "ticket"). Este comando los cambia por
los de MiHelpdesk. **Solo cambia los textos que nadie editó**; los que su
equipo personalizó se dejan como están.

Primero vea qué va a cambiar (no modifica nada):

    php tools/i18n/cargar-idioma.php es_MX

Luego aplíquelo:

    php tools/i18n/cargar-idioma.php es_MX --aplicar

En Windows con XAMPP use `C:/xampp/php/php.exe` en lugar de `php`. Se puede
ejecutar más de una vez sin problema.

Si su osTicket estaba en inglés, después elija el idioma en
**Administración › Configuración › Sistema › Idioma principal**.

### 7. Revise la marca

En **Administración › Configuración › Personalización**:
- su logo ya está; agregue la versión para fondo oscuro si la tiene;
- elija el color de su marca;
- revise la portada del portal y el pie de página.

### 8. Termine

- **Tarea programada (cron):** si la ruta de la carpeta cambió, actualice
  la línea que revisa el correo, por ejemplo:

      * * * * * php /var/www/osticket/api/cron.php

- **Seguridad:** borre la carpeta `setup/` y quite el permiso de escritura
  a `include/ost-config.php` (`chmod 0644 include/ost-config.php`).
- **Pruebe:** entre como agente, abra una solicitud con adjunto, responda
  y revise que llegue el correo.


Si algo sale mal
----------------
1. Vuelva a poner la carpeta anterior (`osticket-anterior` → `osticket`).
2. Si el actualizador ya había cambiado la base de datos (paso 5, desde
   1.17), restaure el respaldo:

       mysql -u USUARIO -p NOMBRE_BD < respaldo-osticket.sql

Las actualizaciones de la base de datos no se pueden deshacer sin el
respaldo.

¿Necesita ayuda con la migración? Ver [SOPORTE.md](SOPORTE.md).
