<?php
/**
 * MiHelpdesk — Agentes de demostración para desarrollo y pruebas.
 *
 * Crea cuatro agentes de prueba (contraseña: Agente12345!) y, si ya existe
 * el historial de tools/dev/seed-history.php, reparte esas solicitudes entre
 * los agentes de Soporte, para que el tablero muestre el desempeño de varias
 * personas.
 *
 * Uso (desde la raíz del repositorio, con el sistema ya instalado):
 *
 *     php tools/dev/seed-agents.php
 *
 * Se puede correr varias veces: los agentes que ya existen no se duplican.
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
require_once INCLUDE_DIR . 'class.staff.php';
require_once INCLUDE_DIR . 'class.role.php';
require_once INCLUDE_DIR . 'class.dept.php';

global $ost, $cfg;
if (!($ost = osTicket::start()) || !($cfg = $ost->getConfig()))
    die("No se pudo cargar la configuración.\n");

function out($msg) { echo $msg, "\n"; }

$clave = 'Agente12345!';

// Sin internet la verificación DNS de correos falla; se desactiva durante
// el script y al final se restaura el valor anterior.
$verify = $cfg->get('verify_email_addrs');
$cfg->persist('verify_email_addrs', 0);

// Roles de fábrica: 1 acceso total, 2 ampliado, 3 limitado, 4 solo lectura.
// Si no existen (instalación modificada), se usa el primero que haya.
function rol($id) {
    if (Role::lookup($id)) return $id;
    $r = Role::objects()->order_by('id')->first();
    return $r ? $r->getId() : 0;
}

// Departamentos por nombre (en español o inglés); si no, el predeterminado.
function depto(array $nombres) {
    global $cfg;
    foreach ($nombres as $n)
        if ($id = Dept::getIdByName($n)) return $id;
    return $cfg->getDefaultDeptId();
}
$soporte = depto(array('Soporte', 'Support'));
$ventas  = depto(array('Ventas', 'Sales'));

$agentes = array(
    array('laura.pineda',  'Laura',  'Pineda',  $soporte, rol(1), 'Supervisora de soporte'),
    array('carlos.mejia',  'Carlos', 'Mejía',   $soporte, rol(2), 'Agente de soporte'),
    array('ana.rivas',     'Ana',    'Rivas',   $soporte, rol(2), 'Agente de soporte'),
    array('mario.fuentes', 'Mario',  'Fuentes', $ventas,  rol(2), 'Agente de ventas'),
);

foreach ($agentes as $a) {
    list($user, $nombre, $apellido, $dept, $role, $nota) = $a;
    if (Staff::getIdByUsername($user)) {
        out("Ya existe: $user");
        continue;
    }
    $errors = array();
    $staff = Staff::create();
    $ok = $staff->update(array(
        'username'   => $user,
        'firstname'  => $nombre,
        'lastname'   => $apellido,
        'email'      => "$user@example.com",
        'dept_id'    => $dept,
        'role_id'    => $role,
        'passwd1'    => $clave,
        'isvisible'  => 1,
        'notes'      => $nota,
        'perms'      => array('user.create', 'user.edit', 'user.manage',
                              'org.create', 'org.edit'),
    ), $errors);
    if ($ok)
        out("Agente creado: $user / $clave ($nota)");
    else
        out("Error al crear $user: " . print_r($errors, true));
}

// Repartir el historial de demostración entre los agentes de Soporte
$ids = array();
$res = db_query('SELECT staff_id, firstname, lastname FROM ' . STAFF_TABLE
    . ' WHERE dept_id=' . (int) $soporte . ' AND isactive=1 ORDER BY staff_id');
while ($row = db_fetch_array($res))
    $ids[] = $row;

$res = db_query('SELECT t.ticket_id, t.staff_id, th.id AS thread_id FROM ' . TICKET_TABLE . ' t'
    . ' JOIN ' . USER_EMAIL_TABLE . ' ue ON ue.user_id = t.user_id'
    . ' JOIN ' . THREAD_TABLE . " th ON th.object_id = t.ticket_id AND th.object_type = 'T'"
    . " WHERE ue.address LIKE '%@historial.demo.example.com' ORDER BY t.ticket_id");
$n = 0;
if (count($ids) > 1) {
    while ($t = db_fetch_array($res)) {
        $a = $ids[$t['ticket_id'] % count($ids)];
        $nombre = db_input(trim($a['firstname'] . ' ' . $a['lastname']));
        if ($t['staff_id'])
            db_query('UPDATE ' . TICKET_TABLE . ' SET staff_id=' . (int) $a['staff_id']
                . ' WHERE ticket_id=' . (int) $t['ticket_id']);
        db_query('UPDATE ' . THREAD_ENTRY_TABLE . ' SET staff_id=' . (int) $a['staff_id']
            . ", poster=$nombre WHERE thread_id=" . (int) $t['thread_id'] . " AND type='R'");
        $n++;
    }
}
if ($n)
    out("Historial repartido: $n solicitudes entre " . count($ids) . " agentes de Soporte.");
else
    out("No hay historial que repartir (corre antes tools/dev/seed-history.php si lo quieres).");

if ($verify === null || $verify === '')
    db_query('DELETE FROM '.CONFIG_TABLE." WHERE namespace='core' AND `key`='verify_email_addrs'");
else
    $cfg->persist('verify_email_addrs', $verify);

out("Listo.");
