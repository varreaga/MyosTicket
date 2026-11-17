<?php
/**
 * MiHelpdesk — Datos de demostración para desarrollo y pruebas.
 *
 * Crea temas de ayuda con subtemas, categorías y artículos de la base de
 * conocimiento, y un ticket con una conversación de ejemplo.
 *
 * Uso (desde la raíz del repositorio, con el sistema ya instalado):
 *
 *     php tools/dev/seed-demo.php
 *
 * Se puede correr varias veces: lo que ya existe no se duplica.
 * NO lo uses en una instalación de producción.
 */
if (php_sapi_name() !== 'cli')
    die("Solo desde la línea de comandos.\n");

chdir(dirname(__DIR__, 2));
require_once 'bootstrap.php';
require_once INCLUDE_DIR . 'class.cli.php';
Bootstrap::i18n_prep();
Bootstrap::loadConfig();
Bootstrap::defineTables(TABLE_PREFIX);
Bootstrap::loadCode();
Bootstrap::connect();
require_once INCLUDE_DIR . 'class.topic.php';
require_once INCLUDE_DIR . 'class.category.php';
require_once INCLUDE_DIR . 'class.faq.php';
require_once INCLUDE_DIR . 'class.ticket.php';

global $ost, $cfg, $thisstaff;
if (!($ost = osTicket::start()) || !($cfg = $ost->getConfig()))
    die("No se pudo cargar la configuración.\n");

function out($msg) { echo $msg, "\n"; }

// Igual que el panel de administración: crear vacío y luego actualizar.
function crear_tema($nombre, $pid, $deptId, &$errors) {
    $t = Topic::create();
    $ok = $t->update(array('topic' => $nombre, 'topic_pid' => $pid,
        'dept_id' => $deptId, 'ispublic' => 1, 'status' => 'active',
        'notes' => '', 'id' => null), $errors);
    return $ok ? $t : null;
}

// ---------------------------------------------------------------- Temas
$dept = Dept::objects()->order_by('id')->first();
$temas = array(
    'Soporte técnico' => array('Correo electrónico', 'Equipos y computadoras',
        'Internet y red', 'Impresoras', 'Accesos y contraseñas'),
    'Facturación' => array('Solicitar factura', 'Aclaración de cobro'),
    'Cuentas y usuarios' => array('Alta de usuario', 'Baja de usuario'),
    'Ventas' => array('Cotización', 'Información de servicios'),
);
$creados = array();
foreach ($temas as $padre => $hijos) {
    $pid = Topic::getIdByName($padre, 0);
    if (!$pid) {
        $errors = array();
        $t = crear_tema($padre, 0, $dept->getId(), $errors);
        if (!$t) { out("Error al crear $padre: ".print_r($errors, true)); continue; }
        $pid = $t->getId();
        out("Tema creado: $padre");
    }
    foreach ($hijos as $hijo) {
        if (Topic::getIdByName($hijo, $pid))
            continue;
        $errors = array();
        $t = crear_tema($hijo, $pid, $dept->getId(), $errors);
        if ($t) { $creados[$hijo] = $t->getId(); out("  Subtema creado: $hijo"); }
        else out("  Error al crear $hijo: ".print_r($errors, true));
    }
}

// --------------------------------------------- Base de conocimiento
db_query("UPDATE ".CONFIG_TABLE." SET value='1' WHERE namespace='core' AND `key`='enable_kb'");
$descripciones = array(
    'Cuentas y acceso' => 'Contraseñas, inicio de sesión y seguridad de tu cuenta.',
    'Correo' => 'Configuración y problemas del correo electrónico.',
    'Facturación' => 'Facturas, pagos y aclaraciones.',
);
$kb = array(
    'Cuentas y acceso' => array(
        'Cómo restablecer tu contraseña' => '<p>En la pantalla de inicio de sesión elige <strong>¿Olvidaste tu contraseña?</strong>, escribe tu correo y sigue el enlace que te enviaremos.</p>',
        'Activar la verificación en dos pasos' => '<p>Desde tu perfil, entra a <strong>Seguridad</strong> y activa la verificación en dos pasos con una app de autenticación.</p>',
    ),
    'Correo' => array(
        'Configurar el correo en tu celular' => '<p>Usa el servidor de salida con el <strong>puerto 587</strong> y cifrado TLS. El usuario es tu dirección de correo completa.</p>',
        'El correo no envía mensajes' => '<p>Revisa que tu buzón no esté lleno y que la contraseña guardada en la app sea la actual.</p>',
    ),
    'Facturación' => array(
        'Solicitar una factura' => '<p>Abre un ticket con el tema <em>Facturación › Solicitar factura</em> e incluye tus datos fiscales.</p>',
    ),
);
foreach ($kb as $cat => $faqs) {
    $c = Category::objects()->filter(array('name' => $cat))->first();
    if (!$c) {
        $errors = array();
        $c = Category::create();
        if (!$c->update(array('name' => $cat, 'ispublic' => Category::VISIBILITY_FEATURED,
                'description' => $descripciones[$cat], 'notes' => ''), $errors)) {
            out("Error categoría $cat: ".print_r($errors, true)); continue;
        }
        out("Categoría creada: $cat");
    }
    foreach ($faqs as $q => $a) {
        if (FAQ::objects()->filter(array('question' => $q))->exists())
            continue;
        $errors = array();
        $f = new FAQ();
        if ($f->update(array('question' => $q, 'answer' => $a, 'category_id' => $c->getId(),
                'ispublished' => FAQ::VISIBILITY_PUBLIC, 'notes' => ''), $errors))
            out("  Artículo creado: $q");
        else
            out("  Error artículo $q: ".print_r($errors, true));
    }
}

// ------------------------------------------------ Ticket de ejemplo
$asunto = 'No puedo enviar correos desde mi celular';
$existe = Ticket::objects()->filter(array('cdata__subject' => $asunto))->first();
$pidSoporte = Topic::getIdByName('Soporte técnico', 0);
$tidCorreo = $pidSoporte ? Topic::getIdByName('Correo electrónico', $pidSoporte) : 0;
if (!$existe && $tidCorreo) {
    // Sin internet, la verificación DNS del correo falla: se desactiva solo
    // en memoria durante este script (no se guarda en la base de datos).
    $cfg->persist('verify_email_addrs', 0);
    $errors = array();
    $ticket = Ticket::create(array(
        'name' => 'Jorge Ramírez', 'email' => 'jorge.ramirez@example.com',
        'subject' => $asunto, 'topicId' => $tidCorreo,
        'message' => 'Desde ayer mi celular no envía correos. Los recibo bien, pero al enviar dice "Error de servidor".',
        'source' => 'Web', 'ip' => '127.0.0.1'), $errors, 'web', false, false);
    if ($ticket) {
        $thisstaff = Staff::objects()->order_by('staff_id')->first();
        $errors = array();
        $ticket->postReply(array('response' => '<p>Hola, gracias por escribir. ¿Podrías confirmar qué aplicación de correo usas y enviarnos una captura del error?</p>',
            'reply-to' => 'all', 'emailreply' => 0), $errors, false, true);
        $ticket->postMessage(array('message' => 'Uso la app de Gmail. ¿Qué datos necesitan?',
            'userId' => $ticket->getOwnerId(), 'source' => 'Web'), 'Web', false);
        out("Ticket de ejemplo creado: #".$ticket->getNumber().' (correo: jorge.ramirez@example.com)');
    } else {
        out("Error ticket: ".print_r($errors, true));
    }
}
out("Listo.");
