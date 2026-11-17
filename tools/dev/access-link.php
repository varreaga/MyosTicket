<?php
/**
 * MiHelpdesk — Imprime el enlace de acceso de cliente de un ticket
 * (el mismo que se envía por correo). Solo para desarrollo y pruebas.
 *
 * Uso: php tools/dev/access-link.php NUMERO_DE_TICKET
 */
if (php_sapi_name() !== 'cli' || empty($argv[1]))
    die("Uso: php tools/dev/access-link.php NUMERO_DE_TICKET\n");
chdir(dirname(__DIR__, 2));
require_once 'bootstrap.php';
require_once INCLUDE_DIR . 'class.cli.php';
Bootstrap::i18n_prep(); Bootstrap::loadConfig(); Bootstrap::defineTables(TABLE_PREFIX);
Bootstrap::loadCode(); Bootstrap::connect();
global $ost, $cfg;
$ost = osTicket::start(); $cfg = $ost->getConfig();
require_once INCLUDE_DIR . 'class.client.php';
if (!($ticket = Ticket::lookupByNumber($argv[1])))
    die("No existe el ticket {$argv[1]}\n");
$client = new TicketOwner(new EndUser($ticket->getOwner()), $ticket);
echo $client->getTicketLink(), "\n";
