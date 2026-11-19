#!/usr/bin/env python3
"""
MiHelpdesk — Textos nuevos sin traducir

Busca los textos traducibles que agregó MiHelpdesk (los que no existen en el
osTicket original) y muestra cuáles faltan en un archivo de traducciones de
include/mihelpdesk/i18n/.

Uso (desde la raíz del repositorio):

    python3 tools/i18n/textos-nuevos.py            # revisa es.php
    python3 tools/i18n/textos-nuevos.py pt.php     # otro idioma

Textos que revisa:
  * PHP: __('...'), _P('...'), _S('...') en todo el código, menos los que ya
    existían en osTicket (commit base del fork).
  * JavaScript: t('...') y __('...') del editor (tools/editor/src) y del tema
    (assets/mihelpdesk/js). Estos se revisan todos, porque el paquete de
    idioma oficial solo traduce los textos de los archivos JS originales.
"""
import glob, re, subprocess, sys

BASE = '8d38b06'   # osTicket v1.18.4, base del fork (ver NOTICE.md)
destino = sys.argv[1] if len(sys.argv) > 1 else 'es.php'

PHP = re.compile(r"""(?:__|_P|_S)\(\s*(?:'((?:[^'\\]|\\.)*)'|"((?:[^"\\]|\\.)*)")""")
JS = re.compile(r"""\b(?:t|__)\(\s*(?:'((?:[^'\\]|\\.)*)'|"((?:[^"\\]|\\.)*)")""")

def textos(patron, texto):
    for m in patron.finditer(texto):
        if m.group(1) is not None:
            yield m.group(1).replace("\\'", "'")
        else:
            yield m.group(2).replace('\\"', '"')

def git(*args):
    return subprocess.run(('git',) + args, capture_output=True, text=True, errors='ignore').stdout

originales = set()
for f in git('ls-tree', '-r', '--name-only', BASE).split():
    if f.endswith(('.php', '.js')):
        originales.update(textos(PHP, git('show', f'{BASE}:{f}')))

nuevos = {}
for f in git('ls-files').split():
    if f.endswith('.php') and not f.startswith(('include/i18n/', 'include/mihelpdesk/i18n/', 'tools/', 'setup/')):
        for s in textos(PHP, open(f, errors='ignore').read()):
            if s not in originales:
                nuevos.setdefault(s, set()).add(f)
for f in glob.glob('tools/editor/src/*.js') + glob.glob('assets/mihelpdesk/js/*.js'):
    for s in textos(JS, open(f).read()):
        nuevos.setdefault(s, set()).add(f)

archivo = open('include/mihelpdesk/i18n/' + destino, encoding='utf-8').read()
traducidos = set(re.findall(r"^\s*'((?:[^'\\]|\\.)*)'\s*(?:=>|\n)", archivo, re.M))

faltan = sorted(set(nuevos) - traducidos)
print(f'{len(nuevos)} textos nuevos; {len(faltan)} sin traducir en {destino}.')
for s in faltan:
    print(f"  '{s}'  ({', '.join(sorted(nuevos[s]))})")
sys.exit(1 if faltan else 0)
