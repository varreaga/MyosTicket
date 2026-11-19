#!/usr/bin/env python3
"""
Cambia el nombre visible del producto en todo el fork y regenera los logos
provisionales de texto.

Uso (desde la raiz del repositorio):

    pip install pillow pyyaml
    python3 tools/branding/rebrand.py "NombreNuevo"

Que hace:
  1. Lee el nombre actual de APP_NAME en bootstrap.php.
  2. Sustituye ese nombre por el nuevo en bootstrap.php, en los datos de
     instalacion (include/i18n/en_US/**/*.yaml), en README.md, NOTICE.md
     y en los avisos de modificacion de los archivos PHP.
  3. Regenera los logos de texto (portal de clientes, panel de agentes,
     instalador, PDF de impresion, pie de los correos).

Si ya tienes un logo de verdad, usa --sin-logos y reemplaza los PNG a mano
(ver BRANDING.md para la lista de archivos y tamanos).

No toca: avisos de copyright de osTicket, LICENSE.txt ni nombres internos
del codigo (clases osTicket::, etc.).
"""
import argparse
import base64
import io
import pathlib
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parents[2]

LOGOS = [
    # (ruta, tamano, modo, color, subtitulo)
    ("scp/images/ost-logo.png", (395, 132), "RGBA", (0, 114, 188), "Staff Control Panel"),
    ("assets/default/images/logo.png", (817, 170), "RGBA", (0, 114, 188), "Support Center"),
    ("setup/images/logo.png", (560, 144), "RGB", (0, 114, 188), "Installer"),
    ("include/fpdf/print-logo.png", (817, 170), "L", (0, 0, 0), None),
    ("scp/images/osticket-grey.png", (110, 26), "RGBA", (150, 150, 150), None),
    ("assets/default/images/poweredby.png", (218, 40), "RGBA", (120, 120, 120), None),
]
EMAIL_IMAGE_KEY = "b56944cb4722cc5cda9d1e23a3ea7fbc"


def current_name():
    m = re.search(r"define\('APP_NAME', '([^']+)'\);", (ROOT / "bootstrap.php").read_text("utf-8"))
    if not m:
        sys.exit("No encontre define('APP_NAME', ...) en bootstrap.php")
    return m.group(1)


def replace_in_files(old, new):
    targets = [ROOT / "bootstrap.php", ROOT / "README.md", ROOT / "NOTICE.md", ROOT / "BRANDING.md"]
    targets += list((ROOT / "include/i18n/en_US").rglob("*.yaml"))
    targets += list((ROOT / "include/mihelpdesk/i18n/datos").rglob("*.yaml"))
    targets += [p for p in ROOT.rglob("*.php") if "vendor" not in p.parts]
    changed = 0
    for p in targets:
        if not p.is_file():
            continue
        s = p.read_text("utf-8", errors="surrogateescape")
        if old not in s:
            continue
        p.write_text(s.replace(old, new), "utf-8", errors="surrogateescape")
        changed += 1
    return changed


def render(name, size, mode, fg, sub):
    from PIL import Image, ImageDraw, ImageFont

    def font(px):
        for f in ("DejaVuSans-Bold.ttf", "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf", "Arial Bold.ttf", "arialbd.ttf"):
            try:
                return ImageFont.truetype(f, px)
            except OSError:
                continue
        return ImageFont.load_default()

    w, h = size
    im = Image.new("RGBA", size, (255, 255, 255, 0))
    d = ImageDraw.Draw(im)
    fs = int(h * (0.55 if sub else 0.7))
    while True:
        f = font(fs)
        bb = d.textbbox((0, 0), name, font=f)
        if bb[2] - bb[0] <= w * 0.92 or fs < 8:
            break
        fs -= 1
    tw, th = bb[2] - bb[0], bb[3] - bb[1]
    y = (h - th) // 2 - bb[1] - (int(h * 0.12) if sub else 0)
    d.text(((w - tw) // 2 - bb[0], y), name, font=f, fill=fg)
    if sub:
        sf = font(max(8, int(h * 0.14)))
        sb = d.textbbox((0, 0), sub, font=sf)
        d.text(((w - (sb[2] - sb[0])) // 2, y + bb[3] + int(h * 0.04)), sub, font=sf, fill=(110, 110, 110))
    if mode in ("RGB", "L"):
        base = Image.new("RGB", size, (255, 255, 255))
        base.paste(im, mask=im.split()[3])
        im = base.convert(mode)
    return im


def regenerate_logos(name):
    powered = None
    for rel, size, mode, fg, sub in LOGOS:
        im = render(name, size, mode, fg, sub)
        im.save(ROOT / rel, optimize=True)
        if rel.endswith("poweredby.png"):
            powered = im
    # Imagen embebida en las plantillas de correo
    buf = io.BytesIO()
    powered.save(buf, "PNG", optimize=True)
    b64 = base64.encodebytes(buf.getvalue()).decode().strip().splitlines()
    fy = ROOT / "include/i18n/en_US/file.yaml"
    s = fy.read_text("utf-8")
    pat = re.compile(r"(- key: " + EMAIL_IMAGE_KEY + r"\n  name: [^\n]+\n  type: image/png\n  encoding: base64\n  data: \|\n)((?:    .*\n)+)")
    if not pat.search(s):
        sys.exit("No encontre la imagen del pie de correo en file.yaml")
    s = pat.sub(lambda m: m.group(1) + "".join("    " + l + "\n" for l in b64), s, count=1)
    fy.write_text(s, "utf-8")


def main():
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("nombre", help="Nombre nuevo del producto")
    ap.add_argument("--sin-logos", action="store_true", help="No regenerar los logos de texto")
    args = ap.parse_args()

    new = args.nombre.strip()
    if not new or "'" in new or "osTicket" in new:
        sys.exit("Nombre invalido (vacio, con comilla simple o que contiene 'osTicket').")
    old = current_name()
    if old == new:
        sys.exit(f"El nombre ya es {new}.")
    n = replace_in_files(old, new)
    print(f"{old} -> {new}: {n} archivos actualizados")
    if not args.sin_logos:
        regenerate_logos(new)
        print("Logos regenerados.")
    print("Revisa los cambios con: git diff --stat")


if __name__ == "__main__":
    main()
