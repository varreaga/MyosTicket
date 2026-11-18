<?php
/**
 * MiHelpdesk — Historial de tickets de demostración para probar el tablero.
 *
 * Crea unos 70 tickets repartidos en los últimos 60 días, con respuestas de
 * agente y cierres en distintos tiempos, para que el tablero (indicadores,
 * gráfica y tabla de desempeño) muestre datos con sentido.
 *
 * Uso (desde la raíz del repositorio, después de tools/dev/seed-demo.php):
 *
 *     php tools/dev/seed-history.php
 *
 * Si ya existe el historial, no hace nada. NO lo uses en producción.
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
require_once INCLUDE_DIR . 'class.ticket.php';

global $ost, $cfg, $thisstaff;
if (!($ost = osTicket::start()) || !($cfg = $ost->getConfig()))
    die("No se pudo cargar la configuración.\n");

$marca = 'historial.demo';
if (User::lookupByEmail("cliente1@$marca.example.com"))
    die("El historial de demostración ya existe.\n");

$thisstaff = Staff::objects()->order_by('staff_id')->first();
if (!$thisstaff)
    die("No hay agentes.\n");

$resuelto = TicketStatus::objects()->filter(array('state' => 'closed'))
    ->order_by('id')->first();

// Sin internet la verificación DNS de correos falla; se desactiva durante
// el script y al final se restaura el valor anterior.
$verify = $cfg->get('verify_email_addrs');
$cfg->persist('verify_email_addrs', 0);

mt_srand(20261118);
$clientes = array('Ana Torres', 'Pedro Gómez', 'María López', 'Luis Hernández',
    'Sofía Castro', 'Diego Morales', 'Valeria Ruiz', 'Andrés Silva');
$asuntos = array('No puedo iniciar sesión', 'La impresora no imprime',
    'Solicitud de factura', 'Error al enviar correos', 'Alta de usuario nuevo',
    'El sistema está muy lento', 'Cambio de contraseña', 'No carga el portal',
    'Duda sobre mi cobro', 'Instalar programa');
$temas = array_keys(Topic::getHelpTopics(false, false));

$creados = 0;
$ahora = time();
for ($i = 0; $i < 70; $i++) {
    $c = mt_rand(0, count($clientes) - 1);
    $errors = array();
    $ticket = Ticket::create(array(
        'name' => $clientes[$c],
        'email' => sprintf('cliente%d@%s.example.com', $c + 1, $marca),
        'subject' => $asuntos[mt_rand(0, count($asuntos) - 1)],
        'topicId' => $temas[mt_rand(0, count($temas) - 1)],
        'message' => 'Ticket de demostración para el tablero.',
        'source' => 'Web', 'ip' => '127.0.0.1'), $errors, 'web', false, false);
    if (!$ticket) {
        echo "Error: ", print_r($errors, true), "\n";
        continue;
    }

    // Fechas: llegada en los últimos 60 días, en horario de 8 a 19 h
    $dias = mt_rand(0, 59);
    $t0 = strtotime(date('Y-m-d', $ahora - $dias * 86400)) + mt_rand(8 * 3600, 19 * 3600);
    if ($t0 > $ahora) $t0 = $ahora - 3600;
    $respondido = mt_rand(1, 100) <= 90;
    $t1 = $t0 + mt_rand(10, 8 * 60) * 60;            // primera respuesta
    $cerrado = $respondido && mt_rand(1, 100) <= 80;
    $t2 = $t1 + mt_rand(30, 3 * 24 * 60) * 60;       // resolución
    if ($t1 > $ahora) $respondido = false;
    if ($t2 > $ahora) $cerrado = false;
    $vence = $t0 + 18 * 3600 + mt_rand(0, 36) * 3600;

    if ($respondido) {
        $ticket->postReply(array('response' => '<p>Gracias, ya lo estamos revisando.</p>',
            'reply-to' => 'none', 'emailreply' => 0), $errors, false, false);
    }
    $asignado = mt_rand(1, 100) <= 70 ? $thisstaff->getId() : 0;

    $id = $ticket->getId();
    $tid = $ticket->getThreadId();
    $d = function ($ts) { return db_input(date('Y-m-d H:i:s', $ts)); };
    db_query('UPDATE '.TICKET_TABLE.' SET created='.$d($t0).', updated='.$d($cerrado ? $t2 : $t1)
        .', lastupdate='.$d($cerrado ? $t2 : $t1).', est_duedate='.$d($vence)
        .', staff_id='.(int) $asignado
        .($cerrado
            ? ', status_id='.(int) $resuelto->getId().', closed='.$d($t2).', isoverdue=0'
            : ', isoverdue='.($vence < $ahora ? 1 : 0))
        .' WHERE ticket_id='.(int) $id);
    db_query('UPDATE '.THREAD_ENTRY_TABLE.' SET created='.$d($t0).', updated='.$d($t0)
        ." WHERE thread_id=$tid AND type='M'");
    db_query('UPDATE '.THREAD_ENTRY_TABLE.' SET created='.$d($t1).', updated='.$d($t1)
        ." WHERE thread_id=$tid AND type='R'");
    db_query('UPDATE '.THREAD_TABLE.' SET created='.$d($t0).' WHERE id='.(int) $tid);
    db_query('UPDATE '.THREAD_EVENT_TABLE.' SET timestamp='.$d($t0).' WHERE thread_id='.(int) $tid);
    $creados++;
}

if ($verify === null || $verify === '')
    db_query('DELETE FROM '.CONFIG_TABLE." WHERE namespace='core' AND `key`='verify_email_addrs'");
else
    $cfg->persist('verify_email_addrs', $verify);

echo "Historial creado: $creados tickets en los últimos 60 días.\n";
