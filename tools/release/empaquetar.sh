#!/usr/bin/env bash
# MiHelpdesk — arma el paquete .zip de una versión.
#
# Uso (desde cualquier carpeta del repositorio):
#     tools/release/empaquetar.sh v1.0.0          # usa la etiqueta v1.0.0
#     tools/release/empaquetar.sh v1.0.0-rc1
#     tools/release/empaquetar.sh v1.0.0 develop  # prueba: arma desde una rama
#
# Resultado, en dist/ (fuera de git):
#     mihelpdesk-1.0.0.zip          carpeta mihelpdesk/ lista para el servidor
#     mihelpdesk-1.0.0.zip.sha256   huella para comprobar la descarga
#
# Toma solo los archivos guardados en git (nunca include/ost-config.php ni
# nada sin subir), quita lo que solo sirve para desarrollar y deja escrita la
# versión en bootstrap.php. Ver tools/release/LEEME.md.
set -euo pipefail

etiqueta="${1:-}"
if [[ ! "$etiqueta" =~ ^v[0-9]+\.[0-9]+\.[0-9]+(-[0-9A-Za-z.]+)?$ ]]; then
    echo "Uso: $0 vX.Y.Z[-rcN] [rama-o-commit]" >&2
    exit 1
fi
version="${etiqueta#v}"
raiz="$(git rev-parse --show-toplevel)"
cd "$raiz"

ref="${2:-$etiqueta}"
if ! git rev-parse --verify -q "$ref^{commit}" >/dev/null; then
    echo "No existe la etiqueta o rama '$ref'. Crea la etiqueta primero:" >&2
    echo "    git tag -a $etiqueta -m \"MiHelpdesk $version\"" >&2
    exit 1
fi
commit="$(git rev-parse --short=7 "$ref^{commit}")"

# Lo que no va en el paquete (solo sirve para desarrollar o para GitHub)
excluir=(
    .gitignore
    ISSUE_TEMPLATE.md
    docs                # capturas del README
    setup/test          # pruebas automáticas de osTicket
    tools/dev           # datos de prueba (crean solicitudes y agentes falsos)
)

tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
dest="$tmp/mihelpdesk"
mkdir -p "$dest"

git archive --format=tar "$ref" | tar -x -C "$dest"
for p in "${excluir[@]}"; do rm -rf "${dest:?}/$p"; done

# Por si acaso: nunca empaquetar una configuración con contraseñas
if [[ -e "$dest/include/ost-config.php" ]]; then
    echo "ERROR: include/ost-config.php está en git; no se empaqueta." >&2
    exit 1
fi

# Versión escrita en bootstrap.php
base="$(sed -n "s/^define('APP_UPSTREAM_VERSION', '\(.*\)');.*/\1/p" "$dest/bootstrap.php")"
sed -i.bak \
    -e "s/^define('APP_VERSION', .*/define('APP_VERSION', '$version'); \/\/ Fijado por empaquetar.sh/" \
    -e "s/^define('GIT_VERSION', *'\$git');.*/define('GIT_VERSION', '$commit'); \/\/ Fijado por empaquetar.sh/" \
    -e "s/^define('THIS_VERSION', .*/define('THIS_VERSION', '$base'); \/\/ Fijado por empaquetar.sh/" \
    "$dest/bootstrap.php"
rm -f "$dest/bootstrap.php.bak"
for c in "APP_VERSION', '$version'" "GIT_VERSION', '$commit'" "THIS_VERSION', '$base'"; do
    grep -q "define('$c" "$dest/bootstrap.php" || { echo "ERROR: no se pudo fijar $c" >&2; exit 1; }
done

# Revisión rápida de sintaxis de los archivos propios, si hay PHP
if command -v php >/dev/null; then
    for f in "$dest/bootstrap.php" "$dest"/include/mihelpdesk/*.php; do
        php -l "$f" >/dev/null || { echo "ERROR de sintaxis en $f" >&2; exit 1; }
    done
fi

mkdir -p "$raiz/dist"
zip="$raiz/dist/mihelpdesk-$version.zip"
rm -f "$zip"
(cd "$tmp" && zip -qr -X "$zip" mihelpdesk)
(cd "$raiz/dist" && sha256sum "$(basename "$zip")" > "$(basename "$zip").sha256")

echo "Listo: dist/$(basename "$zip")  ($(du -h "$zip" | cut -f1), $ref @ $commit, osTicket $base)"
cat "$zip.sha256"
