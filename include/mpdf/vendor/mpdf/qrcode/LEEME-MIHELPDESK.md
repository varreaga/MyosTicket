mpdf/qrcode en MiHelpdesk
=========================

Biblioteca de códigos QR de mPDF (https://github.com/mpdf/qrcode),
licencia LGPL-2.1 o posterior (ver LICENSE), compatible con GPLv2.
MiHelpdesk la usa para el código QR de los PDF de solicitudes y tareas.

Cambios respecto al original (versión en VERSION-MIHELPDESK.txt):
* Solo se incluyen los datos de las versiones 1 a 10 del código QR
  (hasta unos 200 caracteres), suficientes para un enlace; se quitaron
  las versiones 11 a 40 para no sumar 6 MB al paquete.
* La carga de clases la hace `include/mihelpdesk/print/comun.php`.
