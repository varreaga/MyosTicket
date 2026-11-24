<?php
/*********************************************************************
    MiHelpdesk — PDF de una solicitud, para el cliente (portal)

    Comprobante de la solicitud: sin notas internas, sin datos internos
    (plan SLA, agente asignado) y con lo que el cliente necesita para
    darle seguimiento. Lo usa Ticket2PDF cuando el tema está activo.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/
require_once __DIR__ . '/comun.php';

$now = Misc::gmtime();
$created = MhPrint::ts($ticket->getCreateDate());
$closed = $ticket->isClosed() ? MhPrint::ts($ticket->getCloseDate()) : null;
$status = $ticket->getStatus();
$n = MhPrint::counts($ticket->getThread());
$base = rtrim($cfg->getUrl(), '/') . '/';
$url = $base . 'tickets.php?id=' . $ticket->getId();
$masking = $cfg->hideStaffName();
?>
<html>
<head>
<style type="text/css">
<?php echo MhPrint::css(); ?>
</style>
</head>
<body>

<htmlpageheader name="mh" style="display:none">
<table class="hdr"><tr>
    <td><?php echo MhPrint::logo(); ?></td>
    <td style="text-align:right">
        <div style="font-weight:bold;color:#111827"><?php echo Format::htmlchars((string) $ost->company); ?></div>
        <div class="muted small"><?php echo sprintf(__('Request #%s'), $ticket->getNumber()); ?></div>
    </td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="mh" style="display:none">
<table class="ftr"><tr>
    <td><?php echo sprintf(__('Request #%1$s · Printed on %2$s'), $ticket->getNumber(), Format::daydatetime($now)); ?></td>
    <td style="text-align:right"><?php echo sprintf(__('Page %1$s of %2$s'), '{PAGENO}', '{nbpg}'); ?></td>
</tr></table>
</htmlpagefooter>

<table style="width:100%"><tr>
    <td style="vertical-align:top">
        <div class="kicker"><?php echo __('Request receipt'); ?></div>
        <h1>#<?php echo $ticket->getNumber(); ?></h1>
        <div class="subject"><?php echo Format::htmlchars($ticket->getSubject()); ?></div>
        <?php echo MhPrint::pills(MhPrint::statePill($ticket->getState(), (string) $status)); ?>
    </td>
    <td style="width:30mm;vertical-align:top" class="qrbox">
        <?php echo MhPrint::qr($url, 24); ?><br/><?php echo __('Scan to check its status'); ?>
    </td>
</tr></table>

<table class="kpi"><tr>
    <td>
        <div class="kpi-label"><?php echo __('Opened on'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php echo Format::date($ticket->getCreateDate()); ?></div>
        <div class="kpi-note"><?php echo Format::time($ticket->getCreateDate()); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo $closed ? __('Resolved in') : __('Open for'); ?></div>
        <div class="kpi-value"><?php echo MhPrint::duration(($closed ?: $now) - $created); ?></div>
        <div class="kpi-note"><?php echo $closed ? sprintf(__('Closed on %s'), Format::date($ticket->getCloseDate())) : __('In progress'); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('Conversation'); ?></div>
        <div class="kpi-value"><?php echo $n['M'] + $n['R']; ?></div>
        <div class="kpi-note"><?php echo sprintf(__('Yours: %1$d · Replies: %2$d'),
            $n['M'], $n['R']); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('Department'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php echo Format::htmlchars((string) $ticket->getDept()); ?></div>
        <div class="kpi-note"><?php echo Format::htmlchars((string) $ticket->getHelpTopic()); ?></div>
    </td>
</tr></table>

<table class="cards"><tr>
    <td class="card">
        <div class="card-title"><?php echo __('Your details'); ?></div>
        <table class="kv">
            <tr><th><?php echo __('Name'); ?></th><td><b><?php echo Format::htmlchars((string) $ticket->getName()); ?></b></td></tr>
            <tr><th><?php echo __('Email'); ?></th><td><?php echo Format::htmlchars($ticket->getEmail()); ?></td></tr>
            <?php if ($ticket->getPhoneNumber()) { ?><tr><th><?php echo __('Phone'); ?></th><td><?php echo Format::htmlchars($ticket->getPhoneNumber()); ?></td></tr><?php } ?>
        </table>
    </td>
    <td class="card">
        <div class="card-title"><?php echo __('How to follow up'); ?></div>
        <div class="small"><?php echo sprintf(__('Keep this receipt. To check the status or reply, go to %1$s and use your email and the number %2$s, or scan the QR code.'),
            '<b>'.Format::htmlchars($base).'</b>', '<b>#'.$ticket->getNumber().'</b>'); ?></div>
    </td>
</tr></table>

<?php echo MhPrint::formAnswers(DynamicFormEntry::forTicket($ticket->getId()), true); ?>

<div class="section"><?php echo __('Conversation'); ?></div>
<?php
$roles = array('M' => __('You'), 'R' => __('Support team'));
if (($entries = $ticket->getThreadEntries(array('M', 'R'))) && $entries->exists(true)) {
    foreach (ThreadEntry::sortEntries($entries, $ticket) as $entry) {
        $name = ($entry->staff_id && $masking) ? __('Staff') : (string) $entry->getName(); ?>
<div class="entry <?php echo strtolower($entry->type); ?>">
    <table class="entry-head"><tr>
        <td><span class="who"><?php echo Format::htmlchars($name); ?></span>
            <span class="role">· <?php echo $roles[$entry->type]; ?></span></td>
        <td style="text-align:right" class="muted"><?php echo Format::datetime($entry->created); ?></td>
    </tr></table>
    <div class="entry-body"><?php echo $entry->getBody()->display('pdf'); ?></div>
    <?php echo MhPrint::files($entry); ?>
</div>
<?php
    }
} ?>

</body>
</html>
