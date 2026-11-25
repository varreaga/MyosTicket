<?php
/*********************************************************************
    MiHelpdesk — PDF de una solicitud, para agentes

    Lo usa Ticket2PDF (include/class.pdf.php) cuando el tema está activo.
    Variables: $ticket, $thisstaff, $cfg, $ost y $this (el PDF, con
    includenotes e includeevents).

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/
require_once __DIR__ . '/comun.php';

$c = MhPrint::colors();
$now = Misc::gmtime();
$created = MhPrint::ts($ticket->getCreateDate());
$closed = $ticket->isClosed() ? MhPrint::ts($ticket->getCloseDate()) : null;
$thread = $ticket->getThread();
$first = MhPrint::firstEntry($thread, 'R');
$firstResp = $first ? MhPrint::ts($first->created) - $created : null;
$status = $ticket->getStatus();
$n = MhPrint::counts($ticket->getThread());
$prio = $ticket->getPriority();
$user = $ticket->getOwner();
$org = $user ? $user->getOrganization() : null;
$url = rtrim($cfg->getUrl(), '/') . '/scp/tickets.php?id=' . $ticket->getId();
$printedBy = $thisstaff ? $thisstaff->getName() : '';
$withNotes = $this->includenotes;
$withEvents = $this->includeevents;
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
        <div class="muted small"><?php echo sprintf(__('Request #%s'), $ticket->getNumber()); ?> · <?php
            echo Format::htmlchars(Format::truncate($ticket->getSubject(), 60)); ?></div>
    </td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="mh" style="display:none">
<table class="ftr"><tr>
    <td><?php echo sprintf(__('Request #%1$s · Printed by %2$s on %3$s'), $ticket->getNumber(),
        Format::htmlchars($printedBy), Format::daydatetime($now)); ?></td>
    <td style="text-align:right"><?php echo sprintf(__('Page %1$s of %2$s'), '{PAGENO}', '{nbpg}'); ?></td>
</tr></table>
</htmlpagefooter>

<!-- Encabezado de la solicitud -->
<table style="width:100%"><tr>
    <td style="vertical-align:top">
        <div class="kicker"><?php echo __('Support request'); ?></div>
        <h1>#<?php echo $ticket->getNumber(); ?></h1>
        <div class="subject"><?php echo Format::htmlchars($ticket->getSubject()); ?></div>
        <?php echo MhPrint::pills(
            MhPrint::statePill($ticket->getState(), (string) $status),
            $prio ? MhPrint::pill(sprintf(__('Priority: %s'), $prio), '#f3f4f6', '#374151') : '',
            $ticket->isOverdue() ? MhPrint::pill(__('Past due'), '#fde8e8', '#9b1c1c') : ''); ?>
    </td>
    <td style="width:30mm;vertical-align:top" class="qrbox">
        <?php echo MhPrint::qr($url, 24); ?><br/><?php echo __('Scan to open it in the system'); ?>
    </td>
</tr></table>

<?php if ($withNotes) { ?>
<div class="banner"><b><?php echo __('For internal use.'); ?></b> <?php
    echo __('This document includes internal notes that the customer cannot see.'); ?></div>
<?php } ?>

<!-- Indicadores -->
<table class="kpi"><tr>
    <td>
        <div class="kpi-label"><?php echo $closed ? __('Resolved in') : __('Open for'); ?></div>
        <div class="kpi-value"><?php echo MhPrint::duration(($closed ?: $now) - $created); ?></div>
        <div class="kpi-note"><?php echo sprintf(__('Since %s'), Format::date($ticket->getCreateDate())); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('First response'); ?></div>
        <div class="kpi-value"><?php echo $first ? MhPrint::duration($firstResp) : '—'; ?></div>
        <div class="kpi-note"><?php echo $first ? __('after it was opened') : __('No response yet'); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('Conversation'); ?></div>
        <div class="kpi-value"><?php echo $n['M'] + $n['R']; ?></div>
        <div class="kpi-note"><?php echo sprintf(__('Customer: %1$d · Replies: %2$d'),
            $n['M'], $n['R']); ?></div>
    </td>
    <td>
<?php if ($closed) { ?>
        <div class="kpi-label"><?php echo __('Closed on'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php echo Format::date($ticket->getCloseDate()); ?></div>
        <div class="kpi-note"><?php echo Format::time($ticket->getCloseDate()); ?></div>
<?php } else { ?>
        <div class="kpi-label"><?php echo __('Due'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php echo $ticket->getEstDueDate() ? Format::date($ticket->getEstDueDate()) : '—'; ?></div>
        <div class="kpi-note"><?php echo $ticket->getEstDueDate() ? Format::time($ticket->getEstDueDate()) : __('No due date'); ?></div>
<?php } ?>
    </td>
</tr></table>

<!-- Cliente y atención -->
<table class="cards"><tr>
    <td class="card">
        <div class="card-title"><?php echo __('Customer'); ?></div>
        <table class="kv">
            <tr><th><?php echo __('Name'); ?></th><td><b><?php echo Format::htmlchars((string) $ticket->getName()); ?></b></td></tr>
            <tr><th><?php echo __('Email'); ?></th><td><?php echo Format::htmlchars($ticket->getEmail()); ?></td></tr>
            <?php if ($ticket->getPhoneNumber()) { ?><tr><th><?php echo __('Phone'); ?></th><td><?php echo Format::htmlchars($ticket->getPhoneNumber()); ?></td></tr><?php } ?>
            <?php if ($org) { ?><tr><th><?php echo __('Organization'); ?></th><td><?php echo Format::htmlchars($org->getName()); ?></td></tr><?php } ?>
            <tr><th><?php echo __('Received via'); ?></th><td><?php echo Format::htmlchars($ticket->getSource()); ?></td></tr>
        </table>
    </td>
    <td class="card">
        <div class="card-title"><?php echo __('Handling'); ?></div>
        <table class="kv">
            <tr><th><?php echo __('Department'); ?></th><td><?php echo Format::htmlchars((string) $ticket->getDept()); ?></td></tr>
            <tr><th><?php echo __('Help topic'); ?></th><td><?php echo Format::htmlchars((string) $ticket->getHelpTopic()); ?></td></tr>
            <tr><th><?php echo __('Assigned To'); ?></th><td><?php echo Format::htmlchars((string) $ticket->getAssigned()) ?: '<span class="muted">'.__('Unassigned').'</span>'; ?></td></tr>
            <tr><th><?php echo __('SLA Plan'); ?></th><td><?php echo ($sla = $ticket->getSLA()) ? Format::htmlchars($sla->getName()) : '—'; ?></td></tr>
            <tr><th><?php echo __('Last Response'); ?></th><td><?php echo MhPrint::date($ticket->getLastResponseDate()); ?></td></tr>
        </table>
    </td>
</tr></table>

<!-- Datos de los formularios -->
<?php echo MhPrint::formAnswers(DynamicFormEntry::forTicket($ticket->getId())); ?>

<!-- Conversación -->
<div class="section"><?php echo __('Conversation'); ?></div>
<?php
$types = array('M', 'R');
if ($withNotes)
    $types[] = 'N';
$entries = $ticket->getThreadEntries($types);
$events = $event = null;
if ($withEvents) {
    $events = new IteratorIterator($thread->getEvents()->order_by('id')->getIterator());
    $events->rewind();
    $event = $events->current();
}
$printEvent = function ($e) {
    $t = $e->getTypedEvent();
    $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/</', ' <', $t->getDescription(ThreadEvent::MODE_STAFF)))));
    echo '<div class="event">— ' . Format::htmlchars($text) . '</div>';
};
$roles = array('M' => __('Customer'), 'R' => __('Agent'), 'N' => __('Internal note'));
if ($entries->exists(true)) {
    foreach (ThreadEntry::sortEntries($entries, $ticket) as $entry) {
        while ($event && $event->timestamp < $entry->created) {
            $printEvent($event);
            $events->next();
            $event = $events->current();
        } ?>
<div class="entry <?php echo strtolower($entry->type); ?>">
    <table class="entry-head"><tr>
        <td><span class="who"><?php echo Format::htmlchars((string) $entry->getName()); ?></span>
            <span class="role">· <?php echo $roles[$entry->type]; ?></span>
            <?php if ($entry->title && $entry->type == 'N') echo '<span class="role">· '.Format::htmlchars(Format::truncate($entry->title, 80)).'</span>'; ?></td>
        <td style="text-align:right" class="muted"><?php echo Format::datetime($entry->created); ?></td>
    </tr></table>
    <div class="entry-body"><?php echo $entry->getBody()->display('pdf'); ?></div>
    <?php echo MhPrint::files($entry); ?>
</div>
<?php
    }
}
while ($event) {
    $printEvent($event);
    $events->next();
    $event = $events->current();
} ?>

<!-- Constancia de atención -->
<div style="page-break-inside: avoid">
<div class="section"><?php echo __('Service acknowledgment'); ?></div>
<div class="muted small"><?php echo __('Use these lines when the request is handled in person or the customer needs to sign that it was received.'); ?></div>
<table class="sign"><tr><td>&nbsp;</td><td>&nbsp;</td></tr></table>
<table class="signlbl"><tr>
    <td><b><?php echo __('Handled by'); ?></b><br/><?php echo Format::htmlchars((string) ($ticket->getStaff() ?: $printedBy)); ?></td>
    <td><b><?php echo __('Received by the customer'); ?></b><br/><?php echo Format::htmlchars((string) $ticket->getName()); ?> · <?php echo __('Date'); ?>: ____________</td>
</tr></table>
</div>

</body>
</html>
