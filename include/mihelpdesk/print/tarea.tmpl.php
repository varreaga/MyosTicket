<?php
/*********************************************************************
    MiHelpdesk — PDF de una tarea, para agentes

    Lo usa Task2PDF (include/class.pdf.php) cuando el tema está activo.
    Variables: $task, $thisstaff, $cfg, $ost.

    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.
**********************************************************************/
require_once __DIR__ . '/comun.php';

$now = Misc::gmtime();
$created = MhPrint::ts($task->getCreateDate());
$closed = $task->isOpen() ? null : MhPrint::ts($task->getCloseDate());
$ticket = $task->ticket;
$url = rtrim($cfg->getUrl(), '/') . '/scp/tasks.php?id=' . $task->getId();
$printedBy = $thisstaff ? $thisstaff->getName() : '';
$entries = $task->getThreadEntries(array('M', 'R', 'N'));
$count = $entries ? $entries->count() : 0;
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
        <div class="muted small"><?php echo sprintf(__('Task #%s'), $task->getNumber()); ?> · <?php
            echo Format::htmlchars(Format::truncate($task->getTitle(), 60)); ?></div>
    </td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="mh" style="display:none">
<table class="ftr"><tr>
    <td><?php echo sprintf(__('Task #%1$s · Printed by %2$s on %3$s'), $task->getNumber(),
        Format::htmlchars($printedBy), Format::daydatetime($now)); ?></td>
    <td style="text-align:right"><?php echo sprintf(__('Page %1$s of %2$s'), '{PAGENO}', '{nbpg}'); ?></td>
</tr></table>
</htmlpagefooter>

<table style="width:100%"><tr>
    <td style="vertical-align:top">
        <div class="kicker"><?php echo __('Internal task'); ?></div>
        <h1>#<?php echo $task->getNumber(); ?></h1>
        <div class="subject"><?php echo Format::htmlchars($task->getTitle()); ?></div>
        <?php echo MhPrint::pills(
            MhPrint::statePill($task->isOpen() ? 'open' : 'closed', $task->isOpen() ? __('In progress') : __('Finished')),
            $task->isOverdue() ? MhPrint::pill(__('Past due'), '#fde8e8', '#9b1c1c') : ''); ?>
    </td>
    <td style="width:30mm;vertical-align:top" class="qrbox">
        <?php echo MhPrint::qr($url, 24); ?><br/><?php echo __('Scan to open it in the system'); ?>
    </td>
</tr></table>

<table class="kpi"><tr>
    <td>
        <div class="kpi-label"><?php echo $closed ? __('Done in') : __('Open for'); ?></div>
        <div class="kpi-value"><?php echo MhPrint::duration(($closed ?: $now) - $created); ?></div>
        <div class="kpi-note"><?php echo sprintf(__('Since %s'), Format::date($task->getCreateDate())); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo $closed ? __('Closed on') : __('Due'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php
            $d = $closed ? $task->getCloseDate() : $task->getDueDate();
            echo $d ? Format::date($d) : '—'; ?></div>
        <div class="kpi-note"><?php echo $d ? Format::time($d) : __('No due date'); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('Entries'); ?></div>
        <div class="kpi-value"><?php echo $count; ?></div>
        <div class="kpi-note"><?php echo __('notes and messages'); ?></div>
    </td>
    <td>
        <div class="kpi-label"><?php echo __('Related request'); ?></div>
        <div class="kpi-value" style="font-size:10pt"><?php echo $ticket ? '#'.$ticket->getNumber() : '—'; ?></div>
        <div class="kpi-note"><?php echo $ticket ? Format::htmlchars(Format::truncate($ticket->getSubject(), 40)) : __('Not linked'); ?></div>
    </td>
</tr></table>

<table class="cards"><tr>
    <td class="card">
        <div class="card-title"><?php echo __('Handling'); ?></div>
        <table class="kv">
            <tr><th><?php echo __('Department'); ?></th><td><?php echo Format::htmlchars((string) $task->getDept()); ?></td></tr>
            <tr><th><?php echo __('Assigned To'); ?></th><td><?php echo Format::htmlchars((string) $task->getAssigned()) ?: '<span class="muted">'.__('Unassigned').'</span>'; ?></td></tr>
            <tr><th><?php echo __('Collaborators'); ?></th><td><?php echo Format::htmlchars(trim((string) $task->getParticipants())) ?: '—'; ?></td></tr>
        </table>
    </td>
    <td class="card">
        <div class="card-title"><?php echo __('Dates'); ?></div>
        <table class="kv">
            <tr><th><?php echo __('Created on'); ?></th><td><?php echo MhPrint::date($task->getCreateDate()); ?></td></tr>
            <tr><th><?php echo __('Due date'); ?></th><td><?php echo MhPrint::date($task->getDueDate()); ?></td></tr>
            <tr><th><?php echo __('Closed on'); ?></th><td><?php echo MhPrint::date($closed ? $task->getCloseDate() : null); ?></td></tr>
        </table>
    </td>
</tr></table>

<?php echo MhPrint::formAnswers(DynamicFormEntry::forTask($task->getId()), false, array('title')); ?>

<div class="section"><?php echo __('Activity'); ?></div>
<?php
$roles = array('M' => __('Message'), 'R' => __('Agent reply'), 'N' => __('Internal note'));
if ($entries) {
    foreach ($entries as $entry) { ?>
<div class="entry <?php echo strtolower($entry->type); ?>">
    <table class="entry-head"><tr>
        <td><span class="who"><?php echo Format::htmlchars((string) $entry->getName()); ?></span>
            <span class="role">· <?php echo $roles[$entry->type]; ?></span>
            <?php if ($entry->title) echo '<span class="role">· '.Format::htmlchars(Format::truncate($entry->title, 80)).'</span>'; ?></td>
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
