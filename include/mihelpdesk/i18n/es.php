<?php
/*********************************************************************
    MiHelpdesk — Traducciones propias al español

    Textos que agregó MiHelpdesk (portal, panel, tablero y editor) y que no
    existen en el paquete de idioma oficial de osTicket. Se suman encima del
    paquete sin modificarlo; ver TextDomain::getAppMessagesFile() en
    include/class.translation.php.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.

    Formato: 'texto original en inglés' => 'traducción'. El texto original
    debe ser idéntico al que usa el código en __('...') o t('...').
    Los marcadores (%s, %d, %1$s) deben conservarse.
**********************************************************************/

return array(

    // ------------------------------------------------ Portal de clientes
    'How can we help you?' => '¿Cómo podemos ayudarte?',
    'Get started' => 'Empezar',
    'Tell us about your problem or request. You will get a ticket number to follow up.'
        => 'Cuéntanos tu problema o solicitud. Recibirás un número de solicitud para darle seguimiento.',
    'Check the status of your requests and reply to our team.'
        => 'Consulta el estado de tus solicitudes y responde a nuestro equipo.',
    'My Tickets' => 'Mis solicitudes',
    'Popular articles' => 'Artículos populares',
    'Check now' => 'Consultar',
    "%d article\0%d articles" => "%d artículo\0%d artículos",
    'Main topics' => 'Temas principales',
    'Topics in %s' => 'Temas de %s',
    '%d topic' => '%d tema',
    '%d topics' => '%d temas',
    'Menu' => 'Menú',
    '%1$s is free software based on %2$s, licensed under the GNU GPL v2.'
        => '%1$s es software libre basado en %2$s, con licencia GNU GPL v2.',
    'Professional installation services and commercial support are available.'
        => 'Hay servicios profesionales de instalación y soporte comercial disponibles.',

    // ------------------------------------------------ Apariencia
    'Appearance' => 'Apariencia',
    'Light' => 'Claro',
    'Dark' => 'Oscuro',
    'Automatic' => 'Automático',

    // ------------------------------------------------ Inicio de sesión del panel
    'Sign in to the agent panel' => 'Inicia sesión en el panel de agentes',
    'Reset your password' => 'Restablece tu contraseña',

    // ------------------------------------------------ Color de marca
    'Brand color' => 'Color de marca',
    'Main color of the portal and the agent panel. Lighter and darker shades are calculated from it.'
        => 'Color principal del portal y del panel de agentes. A partir de él se calculan sus tonos más claros y más oscuros.',
    'Pick a color' => 'Elegir un color',
    'Hex (%1$s) or RGB (%2$s). Leave empty to use the default color.'
        => 'Hexadecimal (%1$s) o RGB (%2$s). Déjalo vacío para usar el color predeterminado.',
    'Shades in use' => 'Tonos en uso',
    'Main' => 'Principal',
    'Hover' => 'Al pasar el ratón',
    'Soft background' => 'Fondo suave',
    'Links' => 'Enlaces',
    'Text on buttons' => 'Texto en botones',
    'Links are darkened or lightened automatically so they stay readable, also in dark mode.'
        => 'Los enlaces se oscurecen o aclaran solos para que se lean bien, también en modo oscuro.',
    'Enter a color in hex (#0072bc) or RGB (rgb(0, 114, 188)) format.'
        => 'Escribe un color en formato hexadecimal (#0072bc) o RGB (rgb(0, 114, 188)).',

    // ------------------------------------------------ Administración
    // Textos de osTicket que el paquete oficial no traduce
    'Apply To' => 'Aplicar a',
    'Useful for email processing' => 'Útil para procesar correos',
    'Required for email fetching' => 'Necesario para descargar correos',
    'All times are in %s timezone' => 'Todas las horas están en la zona horaria %s',
    'System status' => 'Estado del sistema',
    'System logs and information' => 'Registros e información del sistema',

    // ------------------------------------------------ Panel de agentes
    'Search tickets' => 'Buscar solicitudes',
    'Search by number, subject or user' => 'Buscar por número, asunto o cliente',
    'Ticket Details' => 'Detalles de la solicitud',

    // ------------------------------------------------ Tablero
    'Right now' => 'Ahora mismo',
    'Open tickets' => 'Abiertas',
    'Unassigned tickets' => 'Sin asignar',
    'Overdue tickets' => 'Vencidas',
    'View open tickets' => 'Ver solicitudes abiertas',
    'Handle now' => 'Atender ahora',
    'Assigned to me' => 'Asignadas a mí',
    'View my tickets' => 'Ver mis solicitudes',
    'Period' => 'Periodo',
    'This month' => 'Este mes',
    '%s to %s' => 'Del %s al %s',
    'Response times' => 'Tiempos de atención',
    'First response (average)' => 'Primera respuesta (promedio)',
    'Time to resolve (average)' => 'Tiempo hasta resolver (promedio)',
    'Resolved on time' => 'Resueltas a tiempo',
    'Time from when a ticket arrives until an agent first replies to the user. Lower is better.'
        => 'Tiempo desde que llega una solicitud hasta que un agente le responde por primera vez al cliente. Más bajo es mejor.',
    'Time from when a ticket arrives until it is resolved or closed. Only tickets resolved in this period count.'
        => 'Tiempo desde que llega una solicitud hasta que se resuelve o se cierra. Solo cuentan las solicitudes resueltas en este periodo.',
    'Share of tickets resolved before their due date (SLA). Higher is better.'
        => 'Porcentaje de solicitudes resueltas antes de su fecha de vencimiento (SLA). Más alto es mejor.',
    'vs. previous period' => 'frente al periodo anterior',
    'No change vs. previous period' => 'Sin cambios frente al periodo anterior',
    'Better than the previous period' => 'Mejor que el periodo anterior',
    'Worse than the previous period' => 'Peor que el periodo anterior',
    'pts' => 'pts',
    'Not enough data in this period yet.' => 'Todavía no hay datos suficientes en este periodo.',
    'Tickets per day' => 'Solicitudes por día',
    'Received' => 'Recibidas',
    'Resolved' => 'Resueltas',
    'If blue bars stay above orange ones for several days, work is piling up.'
        => 'Si las barras azules superan a las naranjas varios días seguidos, se está acumulando trabajo.',
    'Tickets received and resolved per day: %d received, %d resolved'
        => 'Solicitudes recibidas y resueltas por día: %d recibidas, %d resueltas',
    '%s: %d received, %d resolved' => '%s: %d recibidas, %d resueltas',
    'Performance' => 'Desempeño',
    'Group by' => 'Agrupar por',
    'By agent' => 'Por agente',
    'By department' => 'Por departamento',
    'By topic' => 'Por tema',
    'First response' => 'Primera respuesta',
    'Time to resolve' => 'Hasta resolver',
    'First response (minutes)' => 'Primera respuesta (minutos)',
    'Time to resolve (minutes)' => 'Hasta resolver (minutos)',
    'No department' => 'Sin departamento',
    'No help topic' => 'Sin tema de ayuda',
    'No tickets in this period.' => 'No hay solicitudes en este periodo.',
    'Average times for the period. Received and resolved count tickets in the period; overdue counts open tickets past their due date right now.'
        => 'Tiempos promedio del periodo. Recibidas y resueltas cuentan las solicitudes del periodo; vencidas cuenta las solicitudes abiertas que hoy ya pasaron su fecha límite.',
    '< 1 min' => '< 1 min',
    '%d min' => '%d min',
    '%d h' => '%d h',
    '%d h %d min' => '%d h %d min',
    '%d d' => '%d d',
    '%d d %d h' => '%d d %d h',

    // ------------------------------------------------ Editor (JavaScript)
    'Text formatting' => 'Formato del texto',
    'Format' => 'Formato',
    'Format selection' => 'Formato de la selección',
    'HTML' => 'HTML',
    'HTML source' => 'Código HTML',
    'Normal text' => 'Texto normal',
    'Heading' => 'Título',
    'Heading 1' => 'Título 1',
    'Heading 2' => 'Título 2',
    'Heading 3' => 'Título 3',
    'Quote' => 'Cita',
    'Code' => 'Código',
    'Font Color' => 'Color de letra',
    'Colors' => 'Colores',
    'Remove color' => 'Quitar color',
    'Highlight' => 'Resaltado',
    'Remove highlight' => 'Quitar resaltado',
    'Font Size' => 'Tamaño de letra',
    'Bold' => 'Negrita',
    'Italic' => 'Cursiva',
    'Underline' => 'Subrayado',
    'Strikethrough' => 'Tachado',
    'Unordered List' => 'Lista con viñetas',
    'Ordered List' => 'Lista numerada',
    'Image' => 'Imagen',
    'Image URL' => 'Dirección de la imagen',
    'Uploading...' => 'Subiendo...',
    'Unable to upload the image' => 'No se pudo subir la imagen',
    'Video' => 'Video',
    'YouTube or Vimeo link' => 'Enlace de YouTube o Vimeo',
    'Invalid video link' => 'El enlace del video no es válido',
    'Table' => 'Tabla',
    'Insert table' => 'Insertar tabla',
    'Add row above' => 'Agregar fila arriba',
    'Add row below' => 'Agregar fila abajo',
    'Add column left' => 'Agregar columna a la izquierda',
    'Add column right' => 'Agregar columna a la derecha',
    'Delete row' => 'Eliminar fila',
    'Delete column' => 'Eliminar columna',
    'Toggle header row' => 'Fila de encabezado',
    'Delete table' => 'Eliminar tabla',
    'Link' => 'Enlace',
    'Link URL' => 'Dirección del enlace',
    'Remove link' => 'Quitar enlace',
    'Line' => 'Línea',
    'Fullscreen' => 'Pantalla completa',
    'Left to right' => 'De izquierda a derecha',
    'Right to left' => 'De derecha a izquierda',
    'OK' => 'Aceptar',
    'Insert' => 'Insertar',
    'Variable' => 'Variable',
    'Canned Responses' => 'Respuestas predefinidas',
    'Message' => 'Mensaje',
    'Signature' => 'Firma',
    'No matches' => 'Sin resultados',
    'No variables found' => 'No se encontraron variables',
    'No agents found' => 'No se encontraron agentes',
    'Save Draft' => 'Guardar borrador',
    'Delete Draft' => 'Eliminar borrador',
    'all changes saved' => 'todos los cambios guardados',
    'unsaved' => 'sin guardar',
    'saving...' => 'guardando...',
    'save error' => 'error al guardar',
    'Unable to save draft.' => 'No se pudo guardar el borrador.',
    'Refresh the current page to restore and continue your draft.'
        => 'Recarga la página para recuperar tu borrador y continuar.',
    'Translate' => 'Traducir',
    'translation' => 'traducción',
    'save translation' => 'guardar traducción',
);
