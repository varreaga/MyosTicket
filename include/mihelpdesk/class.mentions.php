<?php
/*********************************************************************
    class.mentions.php

    MiHelpdesk — Avisos de menciones con "@" en las notas internas.

    Cuando un agente escribe "@Nombre Apellido" (o "@usuario") en una nota
    interna de una solicitud o tarea, se manda un correo de aviso al agente
    mencionado, aunque no esté asignado ni le toquen los avisos normales.

    - Se usa la plantilla de aviso de nota interna (solicitudes) o de
      actividad (tareas), con un asunto propio: "Te mencionaron en...".
    - No se avisa a quien escribe la nota, a agentes de vacaciones o
      inactivos, a quien no tiene acceso a la solicitud o tarea, ni a quien
      ya recibió el aviso normal de esa misma nota.

    Parte de MiHelpdesk, fork de osTicket bajo GNU GPL v2.
**********************************************************************/

class MhMentions {

    /**
     * Agentes mencionados en el texto (HTML) de una nota.
     * Coincide "@Nombre Apellido" o "@usuario", sin distinguir mayúsculas,
     * y que no siga una letra o número (para no tomar correos como
     * nombre@dominio).
     */
    static function find($html) {
        $text = Format::html2text((string) $html, 0, false);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        if (strpos($text, '@') === false)
            return array();

        $found = array();
        $agents = Staff::objects()->filter(array('isactive' => 1));
        foreach ($agents as $S) {
            $names = array((string) $S->getName(), (string) $S->getUserName());
            foreach ($names as $name) {
                $name = trim($name);
                if ($name === '')
                    continue;
                $re = '/(?<![\p{L}\p{N}._%+-])@' . preg_quote($name, '/')
                    . '(?![\p{L}\p{N}])/iu';
                if (preg_match($re, $text)) {
                    $found[$S->getId()] = $S;
                    break;
                }
            }
        }
        return array_values($found);
    }

    /**
     * Manda el aviso a los agentes mencionados en $entry.
     *
     * $object   Ticket o Task
     * $entry    ThreadEntry (la nota)
     * $sent     array correo => 1 de los avisos normales ya enviados
     *
     * Devuelve la lista de agentes avisados.
     */
    static function notify($object, $entry, $sent=array()) {
        global $cfg;

        // Solo notas escritas por un agente (no las del sistema ni las que
        // llegan por correo)
        if (!$entry instanceof ThreadEntry || !$cfg || !$entry->getStaffId())
            return array();
        if (!($mentioned = self::find($entry->getBody())))
            return array();

        $isTask = $object instanceof Task;
        if (!($dept = $object->getDept())
            || !($email = $cfg->getAlertEmail())
            || !($tpl = $dept->getTemplate())
            || !($msg = $isTask
                ? $tpl->getTaskActivityAlertMsgTemplate()
                : $tpl->getNoteAlertMsgTemplate())
        ) {
            return array();
        }

        $posterId = $entry->getStaffId();
        $poster = $posterId ? Staff::lookup($posterId) : null;
        $posterName = $poster ? (string) $poster->getName() : (string) $entry->getPoster();

        $vars = array(
            'note' => $entry,
            'activity' => _S('New Internal Note'),
            'comments' => $entry,
            'message' => $entry,
        );
        $msg = $object->replaceVars($msg->asArray(), $vars);
        $subject = $isTask
            ? sprintf(__('%1$s mentioned you on task #%2$s'), $posterName, $object->getNumber())
            : sprintf(__('%1$s mentioned you on ticket #%2$s'), $posterName, $object->getNumber());

        $done = array();
        foreach ($mentioned as $staff) {
            if ($staff->getId() == $posterId
                || !$staff->isAvailable()
                || isset($sent[$staff->getEmail()])
                || !$object->checkStaffPerm($staff)
            ) {
                continue;
            }
            $alert = $object->replaceVars($msg, array('recipient' => $staff));
            $email->sendAlert($staff, $subject, $alert['body'], null,
                array('thread' => $entry));
            $done[] = $staff;
        }
        return $done;
    }
}
