<?php
/*********************************************************************
    mh-dashboard.tmpl.php

    MiHelpdesk — Tablero del panel de agentes (tema visual).
    Archivo nuevo de MiHelpdesk, fork de osTicket, bajo GPLv2. Ver NOTICE.md.

    Variables: $mh (MhDashboard), $mh_group ('staff', 'dept' o 'topic').
**********************************************************************/
if (!isset($mh) || !$thisstaff) die('Access Denied');

$h = function ($s) { return Format::htmlchars($s); };
$counts = $mh->getCurrentCounts();
$times = $mh->getTimes();
$daily = $mh->getDaily();
$rows = $mh->getPerformance($mh_group);
$periods = MhDashboard::getPeriodChoices();
$groups = MhDashboard::getGroupChoices();
$q = function ($extra) use ($mh, $mh_group) {
    return 'dashboard.php?' . http_build_query(array_merge(
        array('period' => $mh->period, 'group' => $mh_group), $extra));
};
$fmtChange = function ($t, $pts=false) {
    if ($t['change'] === null || $t['improved'] === null)
        return null;
    $v = round($t['change']);
    return ($v > 0 ? '+' : '−') . abs($v) . ($pts ? ' ' . __('pts') : '%');
};
?>
<div class="mh-dash">

  <div class="mh-dash-head">
    <h1><?php echo __('Dashboard'); ?></h1>
    <form class="mh-dash-period no-pjax" method="get" action="dashboard.php">
      <input type="hidden" name="group" value="<?php echo $h($mh_group); ?>">
      <label for="mh-period"><?php echo __('Period'); ?></label>
      <select id="mh-period" name="period" onchange="this.form.submit()">
<?php foreach ($periods as $k => $label) { ?>
        <option value="<?php echo $k; ?>" <?php if ($k == $mh->period) echo 'selected'; ?>><?php echo $h($label); ?></option>
<?php } ?>
      </select>
      <noscript><button type="submit" class="button"><?php echo __('Apply'); ?></button></noscript>
      <a class="button mh-dash-export no-pjax" href="<?php echo $h($q(array('export' => 1))); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
        <?php echo __('Export'); ?></a>
    </form>
  </div>

  <section class="mh-dash-section" aria-labelledby="mh-now">
    <h2 id="mh-now"><?php echo __('Right now'); ?></h2>
    <div class="mh-dash-cards">
<?php
$cards = array(
    'open' => array(__('Open'), __('View open tickets')),
    'unassigned' => array(__('Unassigned'), __('Assign')),
    'overdue' => array(__('Overdue'), __('Handle now')),
    'mine' => array(__('Assigned to me'), __('View my tickets')),
);
foreach ($cards as $k => $info) {
    $danger = ($k == 'overdue' && $counts[$k] > 0); ?>
      <a class="mh-kpi no-pjax<?php if ($danger) echo ' is-danger'; ?>" href="dashboard.php?go=<?php echo $k; ?>">
        <span class="mh-kpi-label"><?php echo $h($info[0]); ?></span>
        <span class="mh-kpi-value"><?php echo (int) $counts[$k]; ?></span>
        <span class="mh-kpi-link"><?php echo $h($info[1]); ?> →</span>
      </a>
<?php } ?>
    </div>
  </section>

  <section class="mh-dash-section" aria-labelledby="mh-times">
    <h2 id="mh-times"><?php echo __('Response times'); ?>
      <span><?php echo $h($periods[$mh->period]); ?></span></h2>
    <div class="mh-dash-cards">
<?php
$tcards = array(
    'first_response' => array(__('First response (average)'),
        __('Time from when a ticket arrives until an agent first replies to the user. Lower is better.')),
    'resolution' => array(__('Time to resolve (average)'),
        __('Time from when a ticket arrives until it is resolved or closed. Only tickets resolved in this period count.')),
    'on_time' => array(__('Resolved on time'),
        __('Share of tickets resolved before their due date (SLA). Higher is better.')),
);
foreach ($tcards as $k => $info) {
    $t = $times[$k];
    $value = ($k == 'on_time')
        ? ($t['value'] === null ? '—' : round($t['value']) . '%')
        : MhDashboard::formatMinutes($t['value']);
    $change = $fmtChange($t, $k == 'on_time'); ?>
      <div class="mh-time">
        <span class="mh-kpi-label"><?php echo $h($info[0]); ?></span>
        <div class="mh-time-row">
          <span class="mh-time-value"><?php echo $h($value); ?></span>
<?php if ($change) { ?>
          <span class="mh-delta <?php echo $t['improved'] ? 'is-good' : 'is-bad'; ?>"
            title="<?php echo $h($t['improved'] ? __('Better than the previous period') : __('Worse than the previous period')); ?>">
            <?php echo $h($change); ?> <?php echo __('vs. previous period'); ?></span>
<?php } elseif ($t['change'] !== null) { ?>
          <span class="mh-delta"><?php echo __('No change vs. previous period'); ?></span>
<?php } ?>
        </div>
        <p><?php echo $h($info[1]); ?></p>
<?php if ($t['value'] === null) { ?>
        <p class="mh-muted"><?php echo __('Not enough data in this period yet.'); ?></p>
<?php } ?>
      </div>
<?php } ?>
    </div>
  </section>

<?php
// ------------------------------------------------ Gráfica de barras
$totalRec = array_sum(array_column($daily, 'received'));
$totalRes = array_sum(array_column($daily, 'resolved'));
$max = max(1, max(array_column($daily, 'received') ?: array(0)), max(array_column($daily, 'resolved') ?: array(0)));
$stepV = $max <= 5 ? 1 : ($max <= 10 ? 2 : ($max <= 25 ? 5 : ($max <= 50 ? 10 : ceil($max / 50) * 10)));
$top = (int) (ceil($max / $stepV) * $stepV);
$n = count($daily);
$X0 = 44; $Y0 = 12; $H = 200; $W = 1000;
$step = $W / max(1, $n);
$bw = max(2, min(18, $step * 0.32));
$every = $n <= 14 ? 1 : ($n <= 31 ? 2 : 5);
$tzName = $mh->tz->getName();
?>
  <section class="mh-dash-section mh-dash-card" aria-labelledby="mh-daily">
    <div class="mh-dash-row">
      <h2 id="mh-daily"><?php echo __('Tickets per day'); ?></h2>
      <span class="mh-legend"><span class="mh-swatch is-received" aria-hidden="true"></span><?php echo __('Received'); ?> <strong><?php echo $totalRec; ?></strong></span>
      <span class="mh-legend"><span class="mh-swatch is-resolved" aria-hidden="true"></span><?php echo __('Resolved'); ?> <strong><?php echo $totalRes; ?></strong></span>
    </div>
    <p class="mh-muted"><?php echo $h($mh->getRangeDescription()); ?>.
      <?php echo __('If blue bars stay above orange ones for several days, work is piling up.'); ?></p>
    <div class="mh-chart-scroll">
    <svg class="mh-chart" viewBox="0 0 <?php echo $X0 + $W + 8; ?> <?php echo $Y0 + $H + 30; ?>" role="img"
      aria-label="<?php echo $h(sprintf(__('Tickets received and resolved per day: %d received, %d resolved'), $totalRec, $totalRes)); ?>">
<?php for ($v = 0; $v <= $top; $v += $stepV) {
    $y = $Y0 + $H - $H * $v / $top; ?>
      <line x1="<?php echo $X0; ?>" x2="<?php echo $X0 + $W; ?>" y1="<?php echo round($y, 1); ?>" y2="<?php echo round($y, 1); ?>" class="mh-grid"></line>
      <text x="<?php echo $X0 - 8; ?>" y="<?php echo round($y + 4, 1); ?>" text-anchor="end" class="mh-axis"><?php echo $v; ?></text>
<?php }
foreach ($daily as $i => $d) {
    $x = $X0 + $i * $step + ($step - 2 * $bw - 2) / 2;
    $hr = $H * $d['received'] / $top; $hs = $H * $d['resolved'] / $top;
    $label = Format::date($d['ts'], false, false, $tzName); ?>
      <g>
        <title><?php echo $h(sprintf(__('%s: %d received, %d resolved'), $label, $d['received'], $d['resolved'])); ?></title>
        <rect class="is-received" x="<?php echo round($x, 1); ?>" y="<?php echo round($Y0 + $H - $hr, 1); ?>" width="<?php echo round($bw, 1); ?>" height="<?php echo round($hr, 1); ?>" rx="2"></rect>
        <rect class="is-resolved" x="<?php echo round($x + $bw + 2, 1); ?>" y="<?php echo round($Y0 + $H - $hs, 1); ?>" width="<?php echo round($bw, 1); ?>" height="<?php echo round($hs, 1); ?>" rx="2"></rect>
<?php if ($i % $every == 0) { ?>
        <text x="<?php echo round($x + $bw + 1, 1); ?>" y="<?php echo $Y0 + $H + 20; ?>" text-anchor="middle" class="mh-axis"><?php
            $dt = new DateTime('@'.$d['ts']); $dt->setTimezone($mh->tz); echo $dt->format('j'); ?></text>
<?php } ?>
      </g>
<?php } ?>
    </svg>
    </div>
  </section>

  <section class="mh-dash-section" aria-labelledby="mh-perf">
    <div class="mh-dash-row">
      <h2 id="mh-perf"><?php echo __('Performance'); ?></h2>
      <nav class="mh-dash-tabs" aria-label="<?php echo __('Group by'); ?>">
<?php foreach ($groups as $k => $label) { ?>
        <a class="no-pjax" href="<?php echo $h($q(array('group' => $k))); ?>"<?php if ($k == $mh_group) echo ' aria-current="true"'; ?>><?php echo $h($label); ?></a>
<?php } ?>
      </nav>
    </div>
    <div class="mh-dash-table">
      <table>
        <thead>
          <tr>
            <th scope="col"><?php echo $h(array('staff' => __('Agent'), 'dept' => __('Department'), 'topic' => __('Help Topic'))[$mh_group]); ?></th>
            <th scope="col" class="num"><?php echo __('Received'); ?></th>
            <th scope="col" class="num"><?php echo __('Resolved'); ?></th>
            <th scope="col" class="num"><?php echo __('Overdue'); ?></th>
            <th scope="col" class="num"><?php echo __('First response'); ?></th>
            <th scope="col" class="num"><?php echo __('Time to resolve'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php if (!$rows) { ?>
          <tr><td colspan="6" class="mh-muted"><?php echo __('No tickets in this period.'); ?></td></tr>
<?php }
foreach ($rows as $r) {
    $ini = '';
    if ($mh_group == 'staff' && !$r['none'])
        foreach (array_slice(preg_split('/\s+/u', trim($r['name'])), 0, 2) as $w)
            $ini .= mb_strtoupper(mb_substr($w, 0, 1)); ?>
          <tr<?php if ($r['none']) echo ' class="is-none"'; ?>>
            <td><span class="mh-dash-name"><?php if ($mh_group == 'staff') { ?><span class="mh-avatar" aria-hidden="true"><?php echo $h($ini); ?></span><?php } ?><?php echo $h($r['name']); ?></span></td>
            <td class="num"><?php echo (int) $r['received']; ?></td>
            <td class="num"><?php echo (int) $r['resolved']; ?></td>
            <td class="num<?php if ($r['overdue']) echo ' is-danger'; ?>"><?php echo (int) $r['overdue']; ?></td>
            <td class="num"><?php echo $h(MhDashboard::formatMinutes($r['first_response'])); ?></td>
            <td class="num"><?php echo $h(MhDashboard::formatMinutes($r['resolution'])); ?></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
    <p class="mh-muted"><?php echo __('Average times for the period. Received and resolved count tickets in the period; overdue counts open tickets past their due date right now.'); ?></p>
  </section>

</div>
