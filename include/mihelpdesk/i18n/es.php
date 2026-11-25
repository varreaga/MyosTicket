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
    'Based on %s, licensed under the GNU GPL v2.'
        => 'Basado en %s bajo licencia GNU GPL v2.',
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

    // ------------------------------------------------ Personalización
    'Add a header with your logo and brand color to every email' => 'Agregar un encabezado con su logo y color de marca a todos los correos',
    'Address' => 'Dirección',
    'Agent panel' => 'Panel de agentes',
    'Agent panel (dark mode)' => 'Panel de agentes (modo oscuro)',
    'Agent panel sign in' => 'Inicio de sesión del panel de agentes',
    'Articles and categories only appear if the knowledge base is enabled and has public content.' => 'Los artículos y las categorías solo aparecen si la base de conocimiento está activada y tiene contenido público.',
    'Best regards,' => 'Saludos cordiales,',
    'Brand' => 'Marca',
    'Branded emails' => 'Correos con su marca',
    'Browser icon (favicon)' => 'Ícono de la pestaña del navegador (favicon)',
    'Change how the support center looks for your customers and agents, without programming. Each tab is saved on its own.' => 'Cambie cómo se ve el centro de soporte para sus clientes y agentes, sin programar. Cada pestaña se guarda por separado.',
    'Changes saved.' => 'Cambios guardados.',
    'Create pages such as "Opening hours" or "Privacy notice" in %s (type "Other"). Then choose here where each one appears.' => 'Cree páginas como "Horarios de atención" o "Aviso de privacidad" en %s (tipo "Otro"). Después elija aquí dónde aparece cada una.',
    'Customization' => 'Personalización',
    'Customization needs the MiHelpdesk theme to be enabled.' => 'La personalización requiere que el tema de MiHelpdesk esté activado.',
    'Default icon' => 'Ícono predeterminado',
    'Default logo' => 'Logo predeterminado',
    'Description of the "check" card' => 'Descripción de la tarjeta "consultar"',
    'Description of the "open" card' => 'Descripción de la tarjeta "abrir"',
    'Each link needs a name and an address that starts with https://, mailto: or tel:.' => 'Cada enlace necesita un texto y una dirección que empiece con https://, mailto: o tel:.',
    'Email footer' => 'Pie de los correos',
    'Emails › Templates' => 'Correos › Plantillas',
    'Example: Support hours Monday to Friday from 9:00 to 18:00.' => 'Ejemplo: Horario de atención de lunes a viernes de 9:00 a 18:00.',
    'Example: company name, address and phone. Leave it empty to show the company name.' => 'Ejemplo: nombre de la empresa, dirección y teléfono. Déjelo vacío para mostrar el nombre de la empresa.',
    'Footer' => 'Pie de página',
    'Footer links' => 'Enlaces del pie de página',
    'Footer text' => 'Texto del pie de página',
    'Hello Jorge,' => 'Hola Jorge,',
    'Helpdesk name is required' => 'El nombre del centro de soporte es obligatorio',
    'Home cards' => 'Tarjetas de la portada',
    'Knowledge base categories' => 'Categorías de la base de conocimiento',
    'Knowledge base search box' => 'Buscador de la base de conocimiento',
    'Leave a field empty to use the default text, which is shown in the language of each person.' => 'Deje un campo vacío para usar el texto predeterminado, que se muestra en el idioma de cada persona.',
    'Leave it empty to use the default text.' => 'Déjelo vacío para usar el texto predeterminado.',
    'Lighter and darker shades are calculated from it.' => 'Los tonos más claros y más oscuros se calculan a partir de él.',
    'Links to your website, social networks, WhatsApp, phone or email. Addresses must start with https://, mailto: or tel:.' => 'Enlaces a su sitio web, redes sociales, WhatsApp, teléfono o correo. Las direcciones deben empezar con https://, mailto: o tel:.',
    'Logo' => 'Logo',
    'Logos, brand color, favicon and the texts of the portal are now in %s.' => 'Los logos, el color de marca, el favicon y los textos del portal ahora están en %s.',
    'Manage › Pages' => 'Administrar › Páginas',
    'Menu and footer' => 'Menú y pie de página',
    'Name and color' => 'Nombre y color',
    'Open the portal' => 'Abrir el portal',
    'PNG, JPG, GIF or WEBP, at least twice as wide as it is tall. A transparent background looks best in light and dark mode.' => 'PNG, JPG, GIF o WEBP, al menos el doble de ancho que de alto. Con fondo transparente se ve mejor en modo claro y oscuro.',
    'Portal' => 'Portal',
    'Portal (dark mode)' => 'Portal (modo oscuro)',
    'Portal home' => 'Portada del portal',
    'Portal menu' => 'Menú del portal',
    'Portal sign in' => 'Inicio de sesión del portal',
    'Preview (saved settings)' => 'Vista previa (con lo guardado)',
    'Sections' => 'Secciones',
    'Settings › Customization' => 'Configuración › Personalización',
    'Shown at the top of the portal home. Use a heading for the title and a paragraph for the subtitle.' => 'Aparece arriba en la portada del portal. Use un encabezado para el título y un párrafo para el subtítulo.',
    'Shown in the browser tab and in emails. Example: ACME Support Center.' => 'Aparece en la pestaña del navegador y en los correos. Ejemplo: Centro de Soporte ACME.',
    'Shown in the portal footer, above the copyright line.' => 'Aparece en el pie del portal, arriba de la línea de derechos reservados.',
    'Sign in' => 'Inicio de sesión',
    'Square image, ideally 64 × 64 pixels, PNG or ICO, up to 512 KB.' => 'Imagen cuadrada, de preferencia de 64 × 64 píxeles, PNG o ICO, de hasta 512 KB.',
    'Support center name' => 'Nombre del centro de soporte',
    'Text below the title' => 'Texto debajo del título',
    'Text below the title (optional)' => 'Texto debajo del título (opcional)',
    'The icon must be smaller than 512 KB.' => 'El ícono debe pesar menos de 512 KB.',
    'The text of each email is edited in %s.' => 'El texto de cada correo se edita en %s.',
    'There are no active pages of type "Other" yet.' => 'Todavía no hay páginas activas de tipo "Otro".',
    'There is no home page selected. Choose one in Settings › Company › Site Pages.' => 'No hay una página de inicio seleccionada. Elija una en Configuración › Empresa › Páginas del sitio.',
    'Title of the "check" card' => 'Título de la tarjeta "consultar"',
    'Title of the "open" card' => 'Título de la tarjeta "abrir"',
    'To add more links, save and new empty rows will appear.' => 'Para agregar más enlaces, guarde y aparecerán renglones vacíos nuevos.',
    'Unable to save the changes.' => 'No se pudieron guardar los cambios.',
    'Unable to save the page.' => 'No se pudo guardar la página.',
    'Unable to upload the icon.' => 'No se pudo subir el ícono.',
    'Upload a PNG, ICO, GIF, WEBP or JPG image.' => 'Suba una imagen PNG, ICO, GIF, WEBP o JPG.',
    'Upload an icon' => 'Subir un ícono',
    'Upload your logos and choose where each one is used. The dark mode logo is used when the person has dark mode on; if you do not choose one, the normal logo is used.' => 'Suba sus logos y elija dónde se usa cada uno. El logo para modo oscuro se muestra cuando la persona tiene activado el modo oscuro; si no elige uno, se usa el logo normal.',
    'Use the default icon' => 'Usar el ícono predeterminado',
    'Uses the portal logo (or the company name if there is none) and the brand color. The logo travels inside the email, so it is shown even without internet images.' => 'Usa el logo del portal (o el nombre de la empresa si no hay logo) y el color de marca. El logo va dentro del correo, así que se ve aunque el programa de correo bloquee las imágenes de internet.',
    'We received your request and one of our agents will reply soon.' => 'Recibimos su solicitud y uno de nuestros agentes le responderá pronto.',
    'Welcome text' => 'Texto de bienvenida',
    'WhatsApp' => 'WhatsApp',
    'Your pages' => 'Sus páginas',
    'Open page' => 'Abrir página',
    '%s Alerts' => 'Alertas de %s',
    'Support' => 'Soporte',

    // ------------------------------------------------ Instalador (include/mihelpdesk/setup/)
    'The configuration file is missing'
        => 'Falta el archivo de configuración',
    'The installer saves its settings in %1$s. Create it by copying the template %2$s:'
        => 'El instalador guarda la configuración en %1$s. Créelo copiando la plantilla %2$s:',
    'The file still does not exist.'
        => 'El archivo todavía no existe.',
    'Linux, macOS or Git Bash'
        => 'Linux, macOS o Git Bash',
    'Windows (PowerShell)'
        => 'Windows (PowerShell)',
    'With a file manager or FTP: copy the file and rename the copy to ost-config.php.'
        => 'Con un administrador de archivos o por FTP: copie el archivo y cambie el nombre de la copia a ost-config.php.',
    'Done, continue'
        => 'Listo, continuar',
    'The configuration file cannot be written'
        => 'No se puede escribir en el archivo de configuración',
    'The installer needs to write to %s. Give the web server write access to it:'
        => 'El instalador necesita escribir en %s. Dé permiso de escritura al servidor web:',
    'The file still cannot be written.'
        => 'Todavía no se puede escribir en el archivo.',
    'Linux or macOS'
        => 'Linux o macOS',
    'When the installation finishes, we will remind you to remove that access.'
        => 'Al terminar la instalación le recordaremos quitar ese permiso.',
    '%s is already installed'
        => '%s ya está instalado',
    'The file %s already has a configuration.'
        => 'El archivo %s ya tiene una configuración.',
    'To update to a new version, sign in to the control panel. If you think this is a mistake, replace the file with a fresh copy of include/ost-sampleconfig.php and try again.'
        => 'Para actualizar a una versión nueva, entre al panel de control. Si cree que es un error, reemplace el archivo por una copia nueva de include/ost-sampleconfig.php y vuelva a intentarlo.',
    'Go to the control panel'
        => 'Ir al panel de control',
    'Check again'
        => 'Revisar de nuevo',
    'Requirements check'
        => 'Requisitos',
    'Installation details'
        => 'Datos',
    'All set'
        => 'Listo',
    'Copied'
        => 'Copiado',
    'Copy'
        => 'Copiar',
    'Install %s'
        => 'Instalar %s',
    'Installer · version %s'
        => 'Instalador · versión %s',
    '%s does not support Internet Explorer. Use a current browser.'
        => '%s no funciona con Internet Explorer. Use un navegador actual.',
    '%s is installed!'
        => '¡%s quedó instalado!',
    'You can now sign in to the control panel with the username and password you just created.'
        => 'Ya puede entrar al panel de control con el usuario y la contraseña que acaba de crear.',
    'View the customer portal'
        => 'Ver el portal de clientes',
    'Customer portal'
        => 'Portal de clientes',
    'Control panel for agents'
        => 'Panel de control para agentes',
    'Protect your installation'
        => 'Proteja la instalación',
    'Remove write access to the configuration file'
        => 'Quite el permiso de escritura al archivo de configuración',
    'The installer no longer needs to write to %s.'
        => 'El instalador ya no necesita escribir en %s.',
    'Delete the setup folder'
        => 'Borre la carpeta setup',
    'On a production server, delete it; the control panel will show a warning while it exists.'
        => 'En un servidor de producción, bórrela; mientras exista, el panel de control mostrará un aviso.',
    'First steps'
        => 'Primeros pasos',
    'Make it yours'
        => 'Póngale su marca',
    'Name, color, logos and customer portal home page.'
        => 'Nombre, color, logos y portada del portal de clientes.',
    'Set up email'
        => 'Configure el correo',
    'Outgoing server (SMTP) and the mailbox your customers write to.'
        => 'Servidor de salida (SMTP) y el buzón al que escriben sus clientes.',
    'Add your team'
        => 'Agregue a su equipo',
    'Agents, departments and permissions.'
        => 'Agentes, departamentos y permisos.',
    'Review help topics'
        => 'Revise los temas de ayuda',
    'What customers can choose when they open a request.'
        => 'Lo que los clientes pueden elegir al abrir una solicitud.',
    'PHP %s or newer'
        => 'PHP %s o mayor',
    'MySQLi extension'
        => 'Extensión MySQLi',
    'Connects to the MySQL or MariaDB database'
        => 'Conecta con la base de datos MySQL o MariaDB',
    'Logos, favicon and images'
        => 'Logos, favicon e imágenes',
    'Dates, numbers and language'
        => 'Fechas, números e idioma',
    'Text with accents and other alphabets'
        => 'Textos con acentos y otros alfabetos',
    'Emails in other character sets'
        => 'Correos con otras codificaciones de texto',
    'Fetching emails'
        => 'Descargar correos',
    'HTML emails'
        => 'Correos en HTML',
    'Faster performance'
        => 'Mejor rendimiento',
    'Language packs and plugins'
        => 'Paquetes de idioma y complementos',
    'Faster performance (optional)'
        => 'Mejor rendimiento (opcional)',
    'Welcome to %s'
        => 'Bienvenido a %s',
    'Your helpdesk will be ready in a few minutes. First, let’s check that your server has everything it needs.'
        => 'En unos minutos tendrá su mesa de ayuda lista. Primero revisamos que el servidor tenga todo lo necesario.',
    'Your server does not meet the minimum requirements.'
        => 'El servidor no cumple los requisitos mínimos.',
    'Fix the items marked in red and reload this page.'
        => 'Corrija lo marcado en rojo y vuelva a cargar esta página.',
    'Your server meets the minimum requirements.'
        => 'El servidor cumple los requisitos mínimos.',
    'Your server meets all the requirements.'
        => 'El servidor cumple todos los requisitos.',
    'Loaded'
        => 'Activa',
    'Missing'
        => 'Falta',
    '%s works without these PHP extensions, but some features need them.'
        => '%s funciona sin estas extensiones de PHP, pero algunas funciones las necesitan.',
    'Not installed'
        => 'No instalada',
    'To enable an extension, remove the %1$s at the start of its line in %2$s (for example %3$s) and restart the web server.'
        => 'Para activar una extensión, quite el %1$s al inicio de su línea en %2$s (por ejemplo %3$s) y reinicie el servidor web.',
    'Set up %s'
        => 'Configure %s',
    'Fill in these details to finish the installation. All fields are required.'
        => 'Complete estos datos para terminar la instalación. Todos los campos son obligatorios.',
    'Your helpdesk'
        => 'Su mesa de ayuda',
    'How your customers will see it.'
        => 'Cómo la verán sus clientes.',
    'Helpdesk address'
        => 'Dirección de la mesa de ayuda',
    'Helpdesk name'
        => 'Nombre de la mesa de ayuda',
    'Shown on the customer portal, in emails and in the browser title. For example, your company name.'
        => 'Aparece en el portal de clientes, en los correos y en el título del navegador. Por ejemplo, el nombre de su empresa.',
    'Support email'
        => 'Correo de soporte',
    'The address your customers write to, for example support@yourcompany.com.'
        => 'La dirección a la que le escriben sus clientes, por ejemplo soporte@suempresa.com.',
    'Primary language'
        => 'Idioma principal',
    'Used for emails, pages and sample data.'
        => 'Se usa en los correos, las páginas y los datos de ejemplo.',
    'Administrator account'
        => 'Cuenta de administrador',
    'You will use this account to sign in to the control panel. You can add more agents later.'
        => 'Con esta cuenta entrará al panel de control. Después puede agregar más agentes.',
    'Your email'
        => 'Su correo',
    'Must be different from the support email.'
        => 'Debe ser distinto del correo de soporte.',
    'Without spaces. It cannot be “admin”.'
        => 'Sin espacios. No puede ser «admin».',
    'Repeat password'
        => 'Repita la contraseña',
    'Database'
        => 'Base de datos',
    'Create an empty database and a user with permissions on it first (for example, in phpMyAdmin).'
        => 'Antes, cree una base de datos vacía y un usuario con permisos sobre ella (por ejemplo, en phpMyAdmin).',
    'Database server'
        => 'Servidor de la base de datos',
    'Usually localhost.'
        => 'Normalmente es localhost.',
    'Database name'
        => 'Nombre de la base de datos',
    'Database user'
        => 'Usuario de la base de datos',
    'Database password'
        => 'Contraseña de la base de datos',
    'Advanced options'
        => 'Opciones avanzadas',
    'Table prefix'
        => 'Prefijo de las tablas',
    'Change it only if the database is shared with another installation. It must end with _'
        => 'Cámbielo solo si la base de datos la comparte otra instalación. Debe terminar en _',
    'Required to install'
        => 'Obligatorios',
    'Recommended extensions'
        => 'Extensiones recomendadas',
    'Install now'
        => 'Instalar ahora',
    'Installing…'
        => 'Instalando…',
    'Creating the tables and settings. This takes less than a minute; please do not close this page.'
        => 'Estamos creando las tablas y la configuración. Tarda menos de un minuto; no cierre esta página.',
    "You can continue. %d recommended extension is missing; some features may not work.\0You can continue. %d recommended extensions are missing; some features may not work."
        => "Puede continuar. Falta %d extensión recomendada; algunas funciones podrían no estar disponibles.\0Puede continuar. Faltan %d extensiones recomendadas; algunas funciones podrían no estar disponibles.",

    // ------------------------------------------------ Actualizador (include/mihelpdesk/upgrader/)
    'The update stopped'
        => 'La actualización se detuvo',
    'An error occurred while updating the database, so the update was stopped.'
        => 'Ocurrió un error al actualizar la base de datos y la actualización se detuvo.',
    'Write down the error'
        => 'Anote el error',
    'You can see the details in %s.'
        => 'Puede ver los detalles en %s.',
    'Restore your backup'
        => 'Restaure su respaldo',
    'Restore the database and the previous files, then try again or get technical help.'
        => 'Restaure la base de datos y los archivos anteriores; luego vuelva a intentarlo o pida ayuda técnica.',
    'The update is complete'
        => 'La actualización terminó',
    'The database is now up to date with %1$s %2$s.'
        => 'La base de datos ya está al día con %1$s %2$s.',
    'Next steps'
        => 'Siguientes pasos',
    'Update the texts to the new version'
        => 'Actualice los textos a la nueva versión',
    'Translates the pages, email templates, queues and statuses that came from the previous installation. It only changes texts that nobody edited, and it can be run more than once.'
        => 'Traduce las páginas, plantillas de correo, colas y estados que venían de la instalación anterior. Solo cambia los textos que nadie editó y se puede ejecutar más de una vez.',
    'Preview the changes'
        => 'Ver los cambios antes',
    'Apply them'
        => 'Aplicarlos',
    'Review your brand'
        => 'Revise su marca',
    'Color, logos and portal home page are in %s.'
        => 'El color, los logos y la portada del portal están en %s.',
    'Update the database'
        => 'Actualizar la base de datos',
    'This version of %s needs to update the database. Your requests, users and settings are kept.'
        => 'Esta versión de %s necesita actualizar la base de datos. Sus solicitudes, usuarios y configuración se conservan.',
    'MySQL %s or newer'
        => 'MySQL %s o mayor',
    'Before you start'
        => 'Antes de empezar',
    'Back up the database'
        => 'Respalde la base de datos',
    'If something goes wrong, you can restore it. For example, from phpMyAdmin (Export) or with:'
        => 'Si algo sale mal, podrá restaurarla. Por ejemplo, desde phpMyAdmin (Exportar) o con:',
    'Keep this page open'
        => 'No cierre esta página',
    'The update usually takes less than a minute.'
        => 'La actualización suele tardar menos de un minuto.',
    'Start the update'
        => 'Empezar la actualización',
    'Rename the configuration file'
        => 'Cambie el nombre del archivo de configuración',
    'Your installation uses the old name %1$s. Rename it to %2$s and continue.'
        => 'Su instalación usa el nombre anterior %1$s. Cámbielo a %2$s y continúe.',
    'Updating to %1$s %2$s'
        => 'Actualizando a %1$s %2$s',
    'The database changes below will be applied. Do not close or reload this page until it finishes.'
        => 'Se aplicarán los cambios de la base de datos que aparecen abajo. No cierre ni recargue esta página hasta que termine.',
    'Changes to apply: %s'
        => 'Cambios por aplicar: %s',
    'Update now'
        => 'Actualizar ahora',
    'Updating the database… Please wait.'
        => 'Actualizando la base de datos… Espere un momento.',
    'Cleaning up!...'
        => 'Terminando…',
    'Still busy... smile #'
        => 'Seguimos trabajando… paso ',

    // ------------------------------------------------ PDF (include/mihelpdesk/print/)
    'less than a minute'
        => '< 1 min',
    '%1$d d %2$d h'
        => '%1$d d %2$d h',
    '%1$d h %2$d min'
        => '%1$d h %2$d min',
    'Task #%1$s · Printed by %2$s on %3$s'
        => 'Tarea #%1$s · Impresa por %2$s el %3$s',
    'Page %1$s of %2$s'
        => 'Página %1$s de %2$s',
    'Internal task'
        => 'Tarea interna',
    'Scan to open it in the system'
        => 'Escanee para abrirla en el sistema',
    'Done in'
        => 'Terminada en',
    'Open for'
        => 'Abierta desde hace',
    'Since %s'
        => 'Desde el %s',
    'Closed on'
        => 'Cerrada el',
    'Due'
        => 'Vence',
    'No due date'
        => 'Sin fecha de vencimiento',
    'notes and messages'
        => 'notas y mensajes',
    'Related request'
        => 'Solicitud relacionada',
    'Not linked'
        => 'Sin solicitud',
    'Handling'
        => 'Atención',
    'Dates'
        => 'Fechas',
    'Activity'
        => 'Actividad',
    'Request #%s'
        => 'Solicitud #%s',
    'Request #%1$s · Printed by %2$s on %3$s'
        => 'Solicitud #%1$s · Impresa por %2$s el %3$s',
    'Support request'
        => 'Solicitud de soporte',
    'Priority: %s'
        => 'Prioridad: %s',
    'For internal use.'
        => 'Uso interno.',
    'This document includes internal notes that the customer cannot see.'
        => 'Este documento incluye notas internas que el cliente no puede ver.',
    'Resolved in'
        => 'Resuelta en',
    'after it was opened'
        => 'después de abrirse',
    'No response yet'
        => 'Todavía sin respuesta',
    'Conversation'
        => 'Conversación',
    'Customer: %1$d · Replies: %2$d'
        => 'Del cliente: %1$d · Respuestas: %2$d',
    'Customer'
        => 'Cliente',
    'Service acknowledgment'
        => 'Constancia de atención',
    'Use these lines when the request is handled in person or the customer needs to sign that it was received.'
        => 'Úselas cuando la atención sea en persona o el cliente deba firmar de recibido.',
    'Handled by'
        => 'Atendió',
    'Received by the customer'
        => 'Recibió conforme',
    'Request #%1$s · Printed on %2$s'
        => 'Solicitud #%1$s · Impresa el %2$s',
    'Request receipt'
        => 'Comprobante de solicitud',
    'Scan to check its status'
        => 'Escanee para ver su estado',
    'Opened on'
        => 'Abierta el',
    'Closed on %s'
        => 'Cerrada el %s',
    'In progress'
        => 'En curso',
    'Yours: %1$d · Replies: %2$d'
        => 'Suyos: %1$d · Respuestas: %2$d',
    'Your details'
        => 'Sus datos',
    'How to follow up'
        => 'Cómo darle seguimiento',
    'Keep this receipt. To check the status or reply, go to %1$s and use your email and the number %2$s, or scan the QR code.'
        => 'Conserve este comprobante. Para ver el estado o responder, entre a %1$s con su correo y el número %2$s, o escanee el código QR.',
    'You'
        => 'Usted',
    'Support team'
        => 'Equipo de soporte',
    'Past due'
        => 'Vencida',
    'Agent reply'
        => 'Respuesta',
    'Received via'
        => 'Recibida por',
    'Created on'
        => 'Creada el',
    'Due date'
        => 'Vencimiento',
    'Finished'
        => 'Terminada',
);
