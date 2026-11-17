MiHelpdesk
==========

> **MiHelpdesk** es un sistema de tickets de soporte de código abierto,
> **basado en [osTicket](https://osticket.com)** y distribuido bajo la
> **Licencia Pública General de GNU, versión 2 (GPLv2)**.
>
> MiHelpdesk es un proyecto independiente. **No está afiliado, patrocinado
> ni respaldado por Enhancesoft, LLC ni por el proyecto osTicket.**
> "osTicket" es una marca de Enhancesoft, LLC y aquí se menciona solo para
> indicar el origen del software.

Qué es
------
MiHelpdesk integra en una sola interfaz web las solicitudes que llegan por
correo, teléfono y formularios web. Permite organizar, asignar, dar
seguimiento y archivar los tickets de soporte.

  1. Los usuarios crean tickets desde el portal web, por correo o por teléfono.
  2. Los tickets se guardan y se asignan a agentes.
  3. Los agentes ayudan a resolver cada caso.

Licencia y libertades
---------------------
MiHelpdesk es **software libre**. Puedes usarlo, estudiarlo, modificarlo y
redistribuirlo bajo los términos de la GPLv2 (ver [LICENSE.txt](LICENSE.txt)).
En particular:

  * El código fuente completo, incluidas todas las modificaciones de este
    fork, está disponible en este repositorio.
  * Si redistribuyes MiHelpdesk o una versión modificada, debes hacerlo bajo
    la misma licencia GPLv2 y entregar también el código fuente.
  * El software se ofrece **SIN NINGUNA GARANTÍA**, tal como indica la GPLv2.

Los cambios hechos en este fork respecto a osTicket están documentados en
[NOTICE.md](NOTICE.md).

Soporte profesional
-------------------
El software es gratuito. De forma opcional se ofrecen **servicios de pago**:
instalación, configuración, personalización, alojamiento y soporte técnico
con tiempos de respuesta garantizados. Ver [SOPORTE.md](SOPORTE.md).

Contratar soporte no cambia la licencia del software ni limita tus derechos
bajo la GPLv2.

Requisitos
----------
  * Servidor HTTP: Apache o Microsoft® IIS
  * PHP 8.2 – 8.4 (se recomienda 8.4)
  * Extensión mysqli de PHP
  * MySQL 5.5 o superior (o MariaDB)

### Recomendado
  * Extensiones de PHP: ctype, fileinfo, gd, gettext, iconv, imap, intl,
    json, mbstring, Zend OPcache, phar, xml, xml-dom y zip
  * Módulo APCu habilitado y configurado

Instalación
-----------
Clona el repositorio y despliega el código en la carpeta pública de tu
servidor web:

    git clone https://github.com/varreaga/MyosTicket
    cd MyosTicket
    php manage.php deploy --setup /var/www/htdocs/mihelpdesk/

Después abre la URL en el navegador y sigue el instalador. Al terminar,
borra la carpeta `setup/` del servidor.

Para desplegar actualizaciones:

    git pull
    php manage.php deploy -v /var/www/htdocs/mihelpdesk/

Actualización desde osTicket
----------------------------
MiHelpdesk mantiene la estructura de base de datos de osTicket, así que se
puede instalar sobre una instalación existente de osTicket 1.6-rc1 o
posterior. Antes de actualizar, haz siempre un respaldo de la base de datos,
los adjuntos y el código. Consulta [UPGRADING.txt](UPGRADING.txt).

Cambiar el nombre o el logo
---------------------------
El nombre visible del producto está definido en un solo lugar
(`APP_NAME` en `bootstrap.php`). Para cambiarlo en todo el proyecto, ver
[BRANDING.md](BRANDING.md).

Tema visual
-----------
El aspecto de la interfaz se personaliza en `assets/mihelpdesk/`, una capa
de estilos propia que no modifica el CSS original. Ver
[assets/mihelpdesk/README.md](assets/mihelpdesk/README.md).

Traducciones
------------
La interfaz es traducible y es compatible con los paquetes de idioma de
osTicket. En los títulos, el pie de página y el instalador, los textos
traducidos que contienen la marca original se muestran con el nombre de
MiHelpdesk (ver la función `brand()` en `bootstrap.php`).

Créditos
--------
MiHelpdesk se basa en **osTicket**, creado por Peter Rotich y desarrollado
por Enhancesoft y su comunidad. Se conservan todos los avisos de copyright
originales de los archivos fuente.

El proyecto incluye, además, estas bibliotecas de código abierto, cada una
con su propia licencia:

  * [Font-Awesome](https://fontawesome.com/)
  * [HTMLawed](https://www.bioinformatics.org/phplabware/internal_utilities/htmLawed)
  * [jQuery dropdown](https://labs.abeautifulsite.net/jquery-dropdown/) (proyecto eliminado)
  * [jsTimezoneDetect](https://pellepim.bitbucket.org/jstz/)
  * [laminas-mail](https://github.com/laminas/laminas-mail)
  * [mPDF](https://github.com/mpdf/mpdf)
  * [PasswordHash](https://www.openwall.com/phpass/)
  * [PEAR](https://pear.php.net/package/PEAR)
  * [PEAR/Auth_SASL](https://pear.php.net/package/Auth_SASL)
  * [PEAR/Mail](https://pear.php.net/package/mail)
  * [PEAR/Net_SMTP](https://pear.php.net/package/Net_SMTP)
  * [PEAR/Net_Socket](https://pear.php.net/package/Net_Socket)
  * [PEAR/Serivces_JSON](https://pear.php.net/package/Services_JSON)
  * [php-gettext](https://launchpad.net/php-gettext/)
  * [phpseclib](https://phpseclib.sourceforge.net/)
  * [Spyc](https://github.com/mustangostang/spyc)
