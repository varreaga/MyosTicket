<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="../../assets/mihelpdesk/marca/mihelpdesk-logo-oscuro.png">
    <img src="../../assets/mihelpdesk/marca/mihelpdesk-logo.png" alt="MiHelpdesk" width="260">
  </picture>
</p>

Cómo publicar una versión
=========================

Ramas y números de versión
--------------------------
* `develop`: aquí entra todo el trabajo, por pull request.
* `main`: solo versiones publicadas. Cada versión es una etiqueta
  `vX.Y.Z` sobre `main`.
* Números: `1.0.0` (versión), `1.0.1` (correcciones), `1.1.0` (funciones
  nuevas), `2.0.0` (cambios que obligan a hacer algo al actualizar). Antes
  de una versión se puede publicar una de prueba: `1.0.0-rc1`, `-rc2`...
* `APP_VERSION` en `bootstrap.php` vale `1.0.0-dev` en git; el paquete lleva
  el número de la etiqueta. `APP_UPSTREAM_VERSION` es la versión de
  osTicket en la que se basa: cámbiala cuando incorpores una versión nueva
  de osTicket.

Pasos
-----
1. **Unir a `main`.** En GitHub, abre un pull request de `develop` hacia
   `main` y únelo.

2. **Notas de la versión.** En `WHATSNEW.md`, agrega arriba la sección de
   la versión con lo más importante (en `develop`, antes del paso 1).

3. **Etiqueta.** En tu copia local:

       git checkout main
       git pull
       git tag -a v1.0.0 -m "MiHelpdesk 1.0.0"
       git push origin v1.0.0

4. **Paquete.** Necesita bash, git, zip y sha256sum (en Windows, Git Bash
   trae todo menos zip; usa el Debian o WSL):

       tools/release/empaquetar.sh v1.0.0

   Deja en `dist/` el archivo `mihelpdesk-1.0.0.zip` y su huella `.sha256`.
   `dist/` no se sube a git.

5. **Probarlo.** Descomprime el .zip en un servidor de prueba, instala desde
   cero y revisa en *Administración › Estado del sistema › Información*
   que diga la versión correcta. Si es una actualización, pruébala también
   sobre una copia de una instalación anterior.

6. **Publicar en GitHub.** *Releases › Draft a new release*: elige la
   etiqueta, pon como título `MiHelpdesk 1.0.0`, copia las notas de
   `WHATSNEW.md` y adjunta el .zip y el .sha256. Para una versión de prueba
   (`-rc`), marca **Set as a pre-release**.

7. **Volver a `develop`.** Une `main` en `develop` si hubo cambios
   directos en `main` (por ejemplo, una corrección urgente).

Qué lleva el paquete
--------------------
Una carpeta `mihelpdesk/` con todo lo que está en git en esa etiqueta,
**incluido** `LICENSE.txt`, `NOTICE.md` y el código fuente del editor
(`tools/editor/`), que la licencia GPL obliga a entregar junto con el
archivo compilado.

**No** lleva: `include/ost-config.php` (el script se detiene si lo
encuentra en git), las capturas de `docs/`, las pruebas automáticas de
`setup/test/`, los datos de prueba de `tools/dev/` ni la plantilla de
issues de GitHub.

Correcciones urgentes
---------------------
Para corregir una versión publicada sin incluir lo que está a medias en
`develop`: crea una rama desde `main`, corrige, abre el pull request hacia
`main`, publica `vX.Y.(Z+1)` con los pasos 3 a 6 y después une `main` en
`develop`.
