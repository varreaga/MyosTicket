<?php
/**
 * MiHelpdesk — Ajustes a las traducciones del paquete oficial es_MX.
 *
 * El paquete mezcla "ticket" y "solicitud". MiHelpdesk usa "solicitud" en
 * todo el sistema, así que aquí se reemplazan las traducciones del paquete
 * que decían "ticket" (con su género corregido: "la solicitud", "cerrada").
 * Estas traducciones SÍ tienen prioridad sobre las del paquete, que no se
 * modifica.
 *
 * Generado con ayuda de un script y revisado a mano. Cada entrada es
 * 'texto en inglés' => 'traducción' (o un arreglo para las formas plurales).
 */
return array(
    '%1$d of %2$d selected tickets'
        => '%1$d a %2$d solicitudes seleccionadas',
    '%1$s Ticket #%2$s'
        => '%1$s solicitud #%2$s',
    "%d Closed Ticket\x00%d Closed Tickets"
        => "%d solicitud cerrada\x00%d solicitudes cerradas",
    '%s <%s> will no longer have access to the ticket'
        => '%s <%s> ya no tendrá acceso a la solicitud',
    '%s does not have any tickets'
        => '%s no tiene ninguna solicitud',
    '(These fields are requested for new tickets
                via the %s form)'
        => '(Estos campos son solicitados para nuevas solicitudes mediante el formulario de %s)',
    '<b>{somebody}</b> linked this ticket with %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}'
        => '<b>{somebody}</b> esta solicitud vinculada con %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}',
    '<b>{somebody}</b> merged this ticket with %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}'
        => '<b>{somebody}</b> esta solicitud combinada con %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}',
    '<b>{somebody}</b> released ticket assignment {timestamp}'
        => '<b>{somebody}</b> realizó la asignación de solicitud {timestamp}',
    '<b>{somebody}</b> unlinked this ticket from %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}'
        => '<b>{somebody}</b> esta solicitud desvinculada de %s{data.id}%s<b>{data.ticket}</b>%s {timestamp}',
    '<strong>Disable</strong> new ticket auto-response'
        => '<strong>Desactivar</strong> nueva solicitud de respuesta automática',
    'A one-time notice sent, if enabled, when user has reached the maximum allowed open tickets.'
        => 'Se envía un aviso, si está habilitado, cuando el usuario ha alcanzado la máxima apertura de solicitudes permitida.',
    'Ability to add/remove emails from banlist via ticket interface'
        => 'Capacidad para añadir o eliminar los emails  de la lista de bloqueados a través de la interfaz de la solicitud',
    'Ability to assign tasks to agents or teams'
        => 'Capacidad de asignar solicitudes a agentes o equipos',
    'Ability to close tickets'
        => 'Capacidad de cerrar solicitudes',
    'Ability to delete tickets'
        => 'Capacidad de borrar solicitudes',
    'Ability to edit tickets'
        => 'Posibilidad de editar solicitudes',
    'Ability to link tickets'
        => 'Capacidad de vincular solicitudes',
    'Ability to manage ticket referrals'
        => 'Habilidad para administrar solicitudes referidas',
    'Ability to mark a ticket as Answered/Unanswered'
        => 'Habilitado para marcar una solicitud como respondida/no respondida',
    'Ability to merge tickets'
        => 'Capacidad de combinar solicitudes',
    'Ability to open tickets on behalf of users'
        => 'Capacidad de abrir solicitudes en nombre de los usuarios',
    'Ability to post a ticket reply'
        => 'Capacidad de publicar una respuesta de la solicitud',
    'Ability to release ticket assignment'
        => 'Habilidad para realizar una asignación de solicitud',
    'Ability to transfer tasks between departments'
        => 'Capacidad de transferencia de solicitudes entre departamentos',
    'Ability to transfer tickets between departments'
        => 'Capacidad de transferir solicitudes entre departamentos',
    'Access Denied. Possibly invalid ticket ID'
        => 'Acceso denegado. Posiblemente un ID de solicitud no válido',
    'Action Denied. Ticket is locked by someone else!'
        => 'Acción denegada. La solicitud está bloqueada por otro!',
    'Action Denied. You are not allowed to assign/reassign tickets.'
        => 'Acción denegada. No le está permitido asignar o reasignar solicitudes.',
    'Add a Ticket'
        => 'Agregar una solicitud',
    'Add to all tickets from this organization'
        => 'Añadir todas las solicitudes de esta organización        ',
    'Advanced Ticket Search'
        => 'Búsqueda avanzada de solicitudes',
    'Agent assigning the ticket'
        => 'Agente asignando la solicitud',
    'Agent will have access to tickets assigned to a team they belong to regardless of the ticket\'s department. Alerts can be enabled for each associated team.'
        => 'El agente tendrá acceso a las solicitudes asignadas a un equipo al que pertenezca independientemente del departamento de la solicitud. Los avisos pueden ser activados para cada equipo asociado.',
    'Agent\'s ticket view link'
        => 'Ver enlace de solicitud del agente',
    'Alert sent out to Agents when internal activity such as an internal note or an agent reply is appended to a ticket.'
        => 'Se envia una alerta a los agentes cuando se registra alguna actividad interna como una nota interna o que un agente haya respondido a una solicitud.',
    'Alert sent to agents on stale or overdue tickets.'
        => 'Alerta enviada a los agentes de solicitudes atrasadas u obsoletas.',
    'Alert sent to agents on task assignment.'
        => 'Aviso enviado a los agentes en la asignación de una solicitud.',
    'Alert sent to agents on ticket assignment.'
        => 'Aviso enviado a los agentes en la asignación de una solicitud.',
    'Alert sent to agents on ticket transfer.'
        => 'Alerta enviada a los agentes de la transferencia de solicitudes.',
    'Alert sent to agents, if enabled, on new task.'
        => 'Alerta enviada a los agentes, si habilitada, en una solicitud nueva.',
    'Alert sent to agents, if enabled, on new ticket.'
        => 'Alerta enviada a los agentes, si habilitada, en una solicitud nueva.',
    'Alert sent to agents, if enabled, when user replies to an existing ticket.'
        => 'Cuando los usuarios contestan a una solicitud ya existente, si esta activo, se enviara un aviso a los agentes.',
    'All members see all tickets'
        => 'Todos los miembros ven todas las solicitudes',
    'Allow tickets on this status to be reopened by end users'
        => 'Permitir al usuario volver abrir la solicitud en este estado',
    'Are you sure you want to <b>change</b> ticket owner to %s?'
        => '¿Está seguro de querer <b>cambiar</b> al dueño de la solicitud a %s?',
    'Are you sure you want to <b>unassign</b> ticket from <b>%s</b>?'
        => '¿Estás seguro de que desea <b>desasignar </b> la solicitud de <b>%s </b>?',
    'Are you sure you want to delete the child ticket(s)?'
        => '¿Está seguro de que quiere borrar las solicitudes secundarias?',
    'Are you sure you want to flag the ticket as <b>answered</b>?'
        => '¿Está seguro que desea marcar la solicitud como <b>contestada</b>?',
    'Are you sure you want to flag the ticket as <b>unanswered</b>?'
        => '¿Está seguro que desea marcar la solicitud como <b>no contestada</b>?',
    'Are you sure you want to flag the ticket as <font color="red"><b>overdue</b></font>?'
        => '¿Está seguro que desea marcar la solicitud como <font color="red"> <b>atrasada</b></font>?',
    'Are you sure you want to mark ticket as <b>%s</b>?'
        => '¿Está seguro que desea marcar la solicitud como <b>%s</b>?',
    'Assign tickets from this organization to the <em>Account Manager</em>'
        => 'Asignar las solicitudes de esta organización al <em>Administrador de cuentas</em>',
    'Automatic'
        => 'Automático',
    'Autoresponse sent to user, if enabled, on new ticket.'
        => 'Autorespuesta enviada al usuario, si esta activa, en una solicitud nueva.',
    'Basic Ticket Information'
        => 'Información básica de la solicitud',
    'Can Create Tickets <em>(XML/JSON/EMAIL)</em>'
        => 'Puede crear solicitudes <em>(XML/JSON/EMAIL)</em>',
    'Cannot mark ticket as answered'
        => 'No es posible marcar la solicitud como contestada',
    'Cannot mark ticket as unanswered'
        => 'No es posible marcar la solicitud como no contestada',
    'Change Tickets Ownership'
        => 'Cambiar la propiedad de la solicitud',
    'Change Tickets Status'
        => 'Cambiar estado de las solicitudes',
    'Change user for ticket #%s'
        => 'Cambiar usuario por solicitud # %s',
    'Check Ticket Status'
        => 'Ver estado de una solicitud',
    'Child Tickets'
        => 'Solicitudes secundarias',
    'Child Tickets do not allow the end user or agent to reply.'
        => 'Las solicitudes secundarias no permiten que el usuario final o el agente respondan.',
    'Choose which Tickets to %s. The Ticket on top will be the Parent Ticket. Sort the order of the Tickets by clicking and dragging them.'
        => 'Elija las solicitudes para %s. La solicitud de arriba será la principal. Ordénelas haciendo clic y arrastrándolas.',
    'Clicking <strong>Save Changes</strong> will unlink this Ticket'
        => 'Clicando <strong>Guardar Cambios</strong> desvinculará esta solicitud',
    'Collaborator Tickets Visibility'
        => 'Visibilidad de colaboradores de solicitudes',
    'Create Ticket'
        => 'Crear solicitud',
    'Current ticket status (%s) does not allow the end user to reply.'
        => 'El estado de la solicitud actual (% s) no permite al usuario responder.',
    'Data is required in this field in order to close the related ticket'
        => 'Se requiere información en este campo para cerrar la solicitud seleccionada',
    'Default Ticket Number Format'
        => 'Formato de número de solicitud por defecto',
    'Default Ticket Number Sequence'
        => 'Secuencia del número de solicitud por defecto',
    'Default Ticket Queue'
        => 'Cola de solicitud por defecto',
    'Default email address of ticket owner'
        => 'Correo por defecto del propietario de la solicitud',
    'Delete Child Ticket'
        => 'Borrar solicitud secundaria',
    'Delete Ticket'
        => 'Borrar solicitud',
    'Deleted tickets CANNOT be recovered, including any associated attachments.'
        => 'Las solicitudes borradas NO se podrán recuperar, ni tampoco sus archivos adjuntos.',
    'Deleted users and tickets CANNOT be recovered'
        => 'Los usuarios y las solicitudes borradas NO PUEDEN ser recuperados',
    'Due date can NOT be set on a closed ticket'
        => 'La fecha no puede establecerse a una solicitud cerrada',
    'Edit Ticket'
        => 'Editar solicitud',
    'Emailed Tickets Priority'
        => 'Prioridad de solicitudes por Email',
    'Enable CAPTCHA on new web tickets.'
        => 'Activar CAPTCHA en las nuevas solicitudes de web.',
    'Error updating ticket status'
        => 'Error actualizando el estado de la solicitud',
    'Error updating ticket status for %s'
        => 'Error al actualizar el estado de la solicitud para %s',
    'Filters are executed based on execution order. Filter can target specific ticket source.'
        => 'Los filtros se ejecutan basados en un orden de ejecución. Un filtro puede dirigirse a una fuente específica de solicitud.',
    'Guest Ticket Access'
        => 'Acceder a una solicitud como invitado',
    'If this is your first time contacting us or you\'ve lost the ticket number, please %s open a new ticket %s'
        => 'Si es la primera vez que se pone en contacto con nosotros o perdio el número de solicitud, por favor %s abra una nueva solicitud %s ',
    'Initial response for the ticket'
        => 'Respuesta inicial para la solicitud',
    'Invalid email or ticket number'
        => 'Correo o número de solicitud no válido',
    'Limit ticket access to ONLY assigned tickets'
        => 'Acceso limitado solo a las solicitudes asignadas',
    'Link Tickets'
        => 'Enlazar solicitudes',
    'Link to view the task'
        => 'Enlace para ver la solicitud',
    'Link to view the task inside the ticket'
        => 'Enlace para ver la tarea dentro de la solicitud',
    'Link to view the ticket'
        => 'Enlace para ver la solicitud',
    'Loading ...'
        => 'Cargando…',
    'Maximum <b>Open</b> Tickets'
        => '<b>Número</b> máximo de solicitudes abiertas',
    'Maximum Open Tickets Limit (%s)'
        => 'Limite máximo de solicitudes abiertas (%s)',
    'Maximum open tickets (%1$d) reached for %2$s'
        => 'Se alcanzó el máximo de solicitudes abiertas (%1$d) por %2$s',
    'Maximum open tickets reached for %s.'
        => 'Máximo de solicitudes abiertas alcanzado por %s.',
    'Merge Tickets'
        => 'Unir solicitudes',
    'Merged tickets cannot be added manually. They must be preselected.'
        => 'Las solicitudes combinadas no se pueden agregar manualmente. Deben estar preseleccionadas.',
    'More than one Parent Ticket selected. %1$s cannot be %2$s.'
        => 'Más de una solicitud principal fue seleccionada. %1$s no puede ser %2$s.',
    'Name of ticket owner'
        => 'Nombre del propietario de la solicitud',
    'New Ticket'
        => 'Nueva solicitud',
    'New Ticket Alert'
        => 'Aviso de nueva solicitud',
    'New Ticket Settings'
        => 'Nueva configuración de solicitud',
    'New ticket options'
        => 'Nuevas opciones de solicitud',
    'New tickets from the email address will be automatically rejected.'
        => 'Las solicitudes nuevas que lleguen de estas direcciones de correo se rechazarán automáticamente.',
    'No such ticket'
        => 'No existe la solicitud',
    'One or more Tickets selected is a merged child. %1$s cannot be %2$s.'
        => 'Una o más de las solicitudes seleccionadas son parte de una fusión. Las solicitudes fusionadas no pueden ser %s',
    'One or more Tickets selected is part of a merge. Merged Tickets cannot be %s.'
        => 'Una o más de las solicitudes seleccionadas son parte de una fusión. Las solicitudes fusionadas no pueden ser %s',
    'Open a New Ticket'
        => 'Abrir una nueva solicitud',
    'Open tickets: %d'
        => 'Solicitudes abiertas: %d',
    'Optional reason for marking ticket as (un)answered'
        => 'Motivo (opcional) para marcar la solicitud como (no) respondida',
    'Overdue Ticket Alert'
        => 'Alerta de solicitud retrasada',
    'Paper size used when printing tickets to PDF'
        => 'Tamaño del papel usado al imprimir solicitudes en PDF',
    'Permission Denied. You are not allowed to edit tickets'
        => 'Permiso denegado. No puede editar solicitudes',
    'Phone number of ticket owner'
        => 'Teléfono del propietario de la solicitud',
    'Please Wait!'
        => 'Un momento, por favor',
    'Please wait... it will take a second!'
        => 'Estamos procesando la información.',
    'Please fill in the form below to open a new ticket.'
        => 'Favor de completar el siguiente formulario para crear una nueva solicitud.',
    'Please provide your email address and a ticket number.'
        => 'Por favor proporcione su dirección de e-mail y el número de solicitud.',
    'Please wait... while we install your new support ticket system!'
        => '¡Por favor espere... mientras instalamos su nuevo sistema de solicitud de soporte!',
    'Primary contacts see all tickets'
        => 'Contactos principales ven todas las solicitudes',
    'Problems assigning the ticket.'
        => 'Problemas asignando la solicitud.',
    'Problems marking the the ticket overdue.'
        => 'Problemas marcando la solicitud como vencida.',
    'Reason for editing the ticket (optional)'
        => 'Razón para editar solicitud (opcional)',
    'Redirect URL used after replying to a ticket.'
        => 'Redireccionar URL usada después de responder a una solicitud.',
    'Reject Ticket'
        => 'Rechazar solicitud',
    'Release (unassign) Ticket'
        => 'Liberar (cancelar) solicitud',
    'Require email verification on "Check Ticket Status" page'
        => 'Requiere verificación del correo en la página de "Comprobar estado de solicitud"',
    'Require registration and login to create tickets'
        => 'Se requiere registrarse para crear solicitudes',
    'Required to close ticket'
        => 'Requerido para cerrar la solicitud',
    'SLA can be overridden on ticket transfer or help topic change'
        => 'El SLA puede sobrepasarse en la transferencia de una solicitud o en el cambio del tema de ayuda',
    'See all tickets in search results, regardless of access'
        => 'Ver todas las solicitudes en resultados de la búsqueda, independientemente del acceso',
    'Select Ticket'
        => 'Seleccionar solicitud',
    'Select a default ticket queue'
        => 'Elige una cola de solicitud por defecto',
    'Set Ticket Status'
        => 'Establecer Estado de la solicitud',
    'Signature is made available as a choice, on ticket reply.'
        => 'La firma se hace disponible como una opción en la respuesta de una solicitud.',
    'Size and maximum uploads setting mainly apply to web tickets.'
        => 'Ajustes de tamaño y máximas subidas aplicados principalmente a las solicitudes de la página.',
    // Títulos de edición mal traducidos en el paquete ("Equipo de actualización")
    'Update Department'
        => 'Actualizar departamento',
    'Update Team'
        => 'Actualizar equipo',
    'Update FAQ'
        => 'Actualizar FAQ',
    'Update Instance'
        => 'Actualizar instancia',
    'Update Page'
        => 'Actualizar página',
    'Update Role'
        => 'Actualizar rol',
    'Update Schedule'
        => 'Actualizar horario',
    'Update User'
        => 'Actualizar usuario',
    'Update Ticket #%s'
        => 'Actualizar solicitud #%s',
    'Members'
        => 'Miembros',
    // Actualizador (mayúsculas y signos que faltaban en el paquete)
    'Upgrade Now'
        => 'Actualizar ahora',
    "We're done!"
        => '¡Listo!',
    'Cleaning up!...'
        => 'Terminando…',
    'Still busy... smile #'
        => 'Seguimos trabajando… paso ',
    // Pantalla final del instalador (el paquete tiene una errata y una frase cortada)
    'Config file permission'
        => 'Permisos del archivo de configuración',
    'Your osTicket installation has been completed successfully. Your next step is to fully configure your new support ticket system for use, but before you get to it please take a minute to cleanup.'
        => 'La instalación de osTicket se completó correctamente. El siguiente paso es configurar el sistema de solicitudes, pero antes tómese un minuto para hacer limpieza.',
    // Aviso de apartado (bloqueo) por vencer y otros textos con mayúscula o género mal puestos
    'Your lock is expiring soon.'
        => '¿Sigues trabajando en esta solicitud?',
    'Renew'
        => 'Seguir trabajando',
    'Post Internal Note'
        => 'Publicar nota interna',
    'Marked overdue!'
        => '¡Marcada como atrasada!',
    'Someone else could be working on the same ticket.'
        => 'Alguien más podría estar trabajando en la misma solicitud.',
    'Sort the forms on this ticket by click and dragging on them. Use the box below the forms list to add new forms to the ticket.'
        => 'Ordena los formularios de esta solicitud haciendo clic y arrastrando sobre ellos. Usa la caja a continuación de la lista de formularios para añadir nuevos formularios para la solicitud.',
    'Statistics of tickets organized by department, help topic, and agent.'
        => 'Las estadísticas de las solicitudes se organizan por departamento, ayuda, tema y agente.',
    'Support Ticket System'
        => 'Sistema de solicitudes de soporte',
    'Support Ticket System Offline'
        => 'El sistema de solicitudes de soporte está fuera de servicio',
    'Support ticket request created'
        => 'Solicitud de soporte creada',
    'System-wide default ticket settings and options.'
        => 'Todo el sistema por defecto ajustes de solicitud y opciones.',
    'Template used on ticket response/reply'
        => 'Plantilla utilizada en solicitud de respuesta',
    'The lock you hold on this ticket will expire soon. Would you like to renew the lock?'
        => 'Mientras la atiendes, la solicitud queda apartada a tu nombre para que nadie más responda al mismo tiempo. El apartado vence pronto por inactividad.',
    'This Task was created from Ticket %1$s'
        => 'Esta Tarea fue creada desde la solicitud %1$s',
    'This Ticket is Merged into another Ticket. Please go to the %s%d%s to reply.'
        => 'Esta solicitud se fusionó con otra. Vaya a %s%d%s para responder.',
    'This Ticket is already in the list.'
        => 'Esta solicitud ya esta en la lista.',
    'This Ticket was created from %1$s %2$s'
        => 'Esta solicitud fue creada desde %1$s %2$s',
    'This ticket'
        => 'Esta solicitud',
    'Ticket'
        => 'Solicitud',
    'Ticket #%1$s deleted by %2$s'
        => 'Solicitud #%1$s borrada por %2$s',
    'Ticket #%1$s: %2$s'
        => 'Solicitud #%1$s: %2$s',
    'Ticket #%s'
        => 'Solicitud #%s',
    'Ticket #%s deleted'
        => 'Solicitud #%s borrada',
    'Ticket #%s: %s'
        => 'Solicitud #%s: %s',
    'Ticket #%s: %s %s'
        => 'Solicitud #%s: %s %s',
    'Ticket %1$s<br/> Thread Entry: %2$s'
        => 'Solicitud %1$s<br/> Hilo de entrada: %2$s',
    'Ticket Activity'
        => 'Actividad de solicitudes',
    'Ticket Assigned to %s'
        => 'Solicitud asignada a %s',
    'Ticket Assignment Alert'
        => 'Aviso de asignación de solicitud',
    'Ticket Attachment Settings'
        => 'Configuración de Adjuntos para solicitudes',
    'Ticket Created From Thread Entry'
        => 'Solicitud creada desde el hilo de entrada',
    'Ticket Data'
        => 'Datos de la solicitud',
    'Ticket Filter'
        => 'Filtros de solicitudes',
    'Ticket ID'
        => 'ID de solicitud ',
    'Ticket ID (internal ID)'
        => 'ID de la solicitud (ID interna)',
    'Ticket Information and Options'
        => 'Información y opciones de la solicitud',
    'Ticket Link'
        => 'Link de solicitud',
    'Ticket Link with Preview'
        => 'Solicitud con link de previsualización',
    'Ticket Marked %s'
        => 'Solicitud marcada como %s',
    'Ticket Number'
        => 'Número de solicitud',
    'Ticket Number Format'
        => 'Formato del Número de solicitud',
    'Ticket Overdue Alerts'
        => 'Aviso de solicitud atrasada',
    'Ticket Owner'
        => 'Propietario de la solicitud',
    'Ticket Owner cannot be a Collaborator'
        => 'El propietario de la solicitud no puede ser un colaborador.',
    'Ticket Queues'
        => 'Colas de solicitudes',
    'Ticket Settings and Options'
        => 'Opciones y configuraciones de solicitudes',
    'Ticket Sharing'
        => 'Compartir solicitud',
    'Ticket Source'
        => 'Fuente de la solicitud',
    'Ticket Submitter'
        => 'Presentador de solicitudes',
    'Ticket Thread'
        => 'Asunto de la solicitud',
    'Ticket Thread expansions'
        => 'Expansiones de hilos de la solicitud',
    'Ticket Variables'
        => 'Variables de solicitud',
    'Ticket already set to %s status'
        => 'La solicitud tiene el estado %s establecido',
    'Ticket assignment released successfully'
        => 'Asignación de solicitud realizada exitosamente',
    'Ticket claimed by %s'
        => 'Solicitud reclamada por %s',
    'Ticket denied'
        => 'Solicitud denegada',
    'Ticket doesn\'t have any children.'
        => 'La solicitud no tiene solicitudes secundarias.',
    'Ticket flagged as %s by %s'
        => 'Solicitud marcada como %s por %s',
    'Ticket is already marked as answered'
        => 'La solicitud ya fue marcada como contestada',
    'Ticket is already marked as unanswered'
        => 'La solicitud ya fue marcada como no contestada',
    'Ticket is assigned to %s'
        => 'La solicitud está asignada a %s',
    'Ticket marked as %s successfully'
        => 'Solicitud marcada como %s exitosamente',
    'Ticket released from <strong>{<Staff>data.staff}</strong> by <b>{somebody}</b> {timestamp}'
        => 'Solicitud lanzada desde <strong>{<Staff>data.staff}</strong> por <b>{somebody}</b> {timestamp}',
    'Ticket released from <strong>{<Team>data.team}</strong> and <strong>{<Staff>data.staff}</strong> by <b>{somebody}</b> {timestamp}'
        => 'Solicitud realizada desde <strong>{<Team>data.team}</strong> y <strong>{<Staff>data.staff}</strong> por <b>{somebody}</b> {timestamp}',
    'Ticket released from <strong>{<Team>data.team}</strong> by <b>{somebody}</b> {timestamp}'
        => 'Solicitud lanzada desde <strong>{<Team>data.team}</strong> por <b>{somebody}</b> {timestamp}',
    'Ticket variables are only supported in thank-you pages.'
        => 'Las variables de la solicitud solo se permiten en las páginas de agradecimiento.',
    'Tickets (%d)'
        => 'Solicitudes (%d)',
    'Tickets <b>(%d)</b>'
        => 'Solicitudes <b>(%d)</b>',
    'Tickets Export'
        => 'Exportar solicitudes',
    'Tickets are marked overdue on grace period violation.'
        => 'Las solicitudes se marcan como atrasadas cuando sobrepasan el tiempo de gracia.',
    'Tickets page refresh rate in minutes.'
        => 'La frecuencia de actualización de la página de solicitudes es de unos minutos.',
    'Unable to assign ticket'
        => 'Imposible asignar solicitud',
    'Unable to change ticket ownership.'
        => 'No es posible cambiar el propietario de la solicitud.',
    'Unable to claim ticket'
        => 'Imposible reclamar solicitud',
    'Unable to create a ticket.'
        => 'Imposible crear una solicitud.',
    'Unable to create new ticket'
        => 'No se puede crear una nueva solicitud',
    'Unable to create the ticket.'
        => 'Imposible crear la solicitud.',
    'Unable to export the ticket to PDF for print.'
        => 'No es posible exportar la solicitud a PDF para imprimirla.',
    'Unable to export the ticket to ZIP.'
        => 'No se puede exportar la solicitud a ZIP.',
    'Unable to lock the ticket.'
        => 'No se pudo bloquear la solicitud.',
    'Unable to mark ticket as %s'
        => 'No es posible marcar la solicitud como %s',
    'Unable to refer ticket'
        => 'No es posible referir solicitud',
    'Unable to release ticket'
        => 'No es posible liberar la solicitud',
    'Unable to release ticket assignment'
        => 'No es posible realizar la asignación de solicitud',
    'Unable to transfer ticket'
        => 'Imposible transferir solicitud',
    'Unknown or invalid ticket ID.'
        => 'ID de solicitud desconocido o no válido.',
    'Used for ticket and task exporting'
        => 'Utilizado para la exportación de solicitudes y tareas',
    'Valid email address and ticket number required'
        => 'Son necesarios la dirección de e-mail y el número de solicitud',
    'We are delighted you have chosen osTicket for your customer support ticketing system!'
        => 'Nos complace que haya elegido osTicket para su sistema de solicitudes de atención al cliente!',
    'We\'ve just sent you an email to the address you entered. Please follow the link in the email to confirm your account and gain access to your tickets.'
        => 'Le acabamos de enviar un mail a la dirección que registraste. Por favor siga la liga en el email para confirmar su cuenta y acceder a sus solicitudes.',
    'You cannot delete a user with tickets!'
        => '¡No puede eliminar usuarios con solicitudes!',
    'You do not have permission to create a ticket in %s'
        => 'No tiene permiso para crear una solicitud en %s',
    'You do not have permission to delete a user with tickets!'
        => '¡No tiene permisos para eliminar usuarios con solicitudes!',
    'You\'ve confirmed your email address and successfully activated your account.  You may proceed to check on previously opened tickets or open a new ticket.'
        => 'Activó su cuenta y confirmó su dirección de correo. Ahora puede revisar sus solicitudes o abrir una nueva.',
    'Your osTicket installation has been completed successfully. Your next step is to fully configure your new support ticket system for use, but before you get to it please take a minute to cleanup.'
        => 'La instalación de osTicket se ha completado con éxito. El siguiente paso es configurar completamente su nuevo sistema de solicitudes de ayuda para su uso, pero antes de llegar a tomemos un minuto para limpiar.',
    'child tickets'
        => 'Solicitudes secundarias',
    'extension required to print ticket to PDF'
        => 'extensión requerida para imprimir la solicitud en PDF',
    'one ticket'
        => 'una solicitud',
    'or %s register for an account %s to access all your tickets.'
        => 'o %s cree una cuenta %s para ver todas sus solicitudes.',
    "selected ticket\x00selected tickets"
        => "Solicitud seleccionada\x00Solicitudes seleccionadas",
    "the selected Ticket\x00the selected Tickets"
        => "la solicitud seleccionada\x00las solicitudes seleccionadas",
    'this ticket'
        => 'esta solicitud',
    "ticket\x00tickets"
        => "solicitud\x00solicitudes",
    'to archive/delete tickets'
        => 'para archivar/borrar solicitudes',
    'to delete tickets'
        => 'eliminar solicitudes',
    'to mass manage tickets'
        => 'para administrar varias solicitudes',
    'to reopen tickets'
        => 'para reabrir solicitudes',
    'to resolve/close tickets'
        => 'para resolver/cerrar solicitudes',
    // Traducciones del paquete con errores (no relacionadas con "solicitud")
    'This content is translatable'
        => 'Este contenido se puede traducir',
    'Autoresponder'
        => 'Respuesta automática',
    'Optional reason for status change (internal note)'
        => 'Motivo opcional del cambio de estado (nota interna)',
    'Access Token'
        => 'Token de acceso',
    'Helpdesk URL'
        => 'URL del centro de soporte',
    'John Doe'
        => 'Juan Pérez',
    // Abierto/cerrado se refiere a solicitudes o tareas (femenino plural)
    'Open' => 'Abiertas',
    'Closed' => 'Cerradas',
    'Most Recently Closed' => 'Cerradas más recientemente',
    "action-button\x04Open" => 'Abiertas',
    "queue-name\x04Open" => 'Abiertas',
    "ticket state name\x04Open" => 'Abierta',
    "ticket state name\x04Closed" => 'Cerrada',
    'Highly recommended for plugins and language packs'
        => 'Muy recomendable para complementos y paquetes de idioma',
);
