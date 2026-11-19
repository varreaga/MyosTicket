<?php
/**
 * MiHelpdesk — Correcciones a los datos del paquete oficial es_MX.
 *
 * El paquete (include/i18n/es_MX.phar) no se modifica. Aquí se corrigen
 * textos completos de sus archivos de datos: '*' aplica a todos los archivos
 * y cada 'archivo.yaml' solo a ese archivo. La clave es el texto del paquete
 * tal cual y el valor, el texto que se usa en su lugar.
 *
 * Los archivos que están completos en datos/es_MX/ (plantillas de correo,
 * páginas, solicitudes de bienvenida, organización y respuestas de ejemplo)
 * reemplazan a los del paquete. "osTicket" se cambia después por APP_NAME.
 */
return array(
    // Textos que MiHelpdesk usó antes y que tools/i18n/cargar-idioma.php
    // puede reemplazar por los actuales (no cuentan como editados a mano)
    '_anteriores' => array('Abierto', 'Respondidos', 'Vencidos', 'Cerrados'),
    '*' => array(
        'Fecha de Vencimiento' => 'Fecha de vencimiento',
        'Procurador' => 'Asignado a',
        'Correo Electrónico' => 'Correo electrónico',
        'Estados del Ticket' => 'Estados de la solicitud',
        'Detalles del ticket' => 'Detalles de la solicitud',
        'Este formulario se adjuntará a cada ticket, independientemente de su origen. Puede agregar nuevos campos a este formulario y estarán disponibles para todos los tickets, se podrán consultar a través de búsqueda avanzada y filtros.'
            => 'Este formulario se agrega a todas las solicitudes, sin importar su origen. Los campos que agregue estarán disponibles en todas las solicitudes y se pueden usar en la búsqueda avanzada y en los filtros.',
        'Detalles de los motivos para abrir el ticket.' => 'Explique el motivo de su solicitud.',
    ),
    'ticket_status.yaml' => array(
        // Estados de una solicitud: en femenino
        'Abiertos' => 'Abierta',
        'Resuelto' => 'Resuelta',
        'Cerrado' => 'Cerrada',
        'Archivado' => 'Archivada',
        'Eliminado' => 'Eliminada',
        'Tickets abiertos.' => 'Solicitudes abiertas.',
        'Tickets resueltos' => 'Solicitudes resueltas.',
        'Tickets cerrados. Los tickets siguen siendo accesibles por el cliente y los miembros del panel.'
            => 'Solicitudes cerradas. El cliente y los agentes las siguen pudiendo consultar.',
        'Tickets solamente administrables, pero no accesibles en las colas de entrada ni en el panel del cliente.'
            => 'Solicitudes que solo se pueden administrar; no aparecen en las colas ni en el portal del cliente.',
        'Tickets en cola para su eliminación. No se puede acceder en las colas de entrada.'
            => 'Solicitudes en espera de ser eliminadas. No aparecen en las colas.',
    ),
    'list.yaml' => array(
        'Propiedades del estado del Ticket' => 'Propiedades del estado de la solicitud',
        'Propiedades que pueden establecerse en un estado del Ticket.' => 'Propiedades que se pueden definir en un estado de la solicitud.',
        'Estado de un Ticket' => 'Estado de una solicitud',
    ),
    'queue.yaml' => array(
        'Ticket' => 'Solicitud',
        'Abiertos' => 'Abiertas',
        'Respondido' => 'Respondidas',
        'Atrasado' => 'Vencidas',
        'Cerrado' => 'Cerradas',
        'Mis Solicitudes' => 'Mis solicitudes',
        'Asignados a mí' => 'Asignadas a mí',
        'Asignados a equipos' => 'Asignadas a equipos',
    ),
    'queue_sort.yaml' => array(
        'Prioridad + Vencidos' => 'Prioridad + Fecha de vencimiento',
        'Creado en' => 'Fecha de creación',
    ),
    'role.yaml' => array(
        'Ampliar Acceso' => 'Acceso ampliado',
        'Solo observar' => 'Solo lectura',
    ),
    'schedule.yaml' => array(
        'Lunes - Viernes 8am - 5pm con vacaciones de EE.UU' => 'Lunes a viernes de 8 a 17 h con días festivos de EE. UU.',
        'Vacaciones de EE.UU.' => 'Días festivos de EE. UU.',
    ),
    'help_topic.yaml' => array(
        'Tickets que se refieren principalmente a los departamentos de ventas y facturación'
            => 'Solicitudes que corresponden principalmente a los departamentos de ventas y facturación',
        'Informar de un problema' => 'Reportar un problema',
    ),
    'email_template_group.yaml' => array(
        // El nombre del juego de plantillas admite 32 caracteres
        'Plantilla osTicket (HTML) por defecto' => 'Plantillas osTicket (español)',
        'Plantillas osTicket por defecto' => 'Plantillas de correo en español.',
    ),
);
