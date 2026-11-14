<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {            // delete one visit, or all visits, then come back to the same page
  csrf_check(); $act = $_POST['act'] ?? '';
  if ($act === 'delvisit') db()->prepare('DELETE FROM visits WHERE id=?')->execute([(int)($_POST['id'] ?? 0)]);
  elseif ($act === 'delall') db()->exec('DELETE FROM visits');
  header('Location: index.php?' . http_build_query(array_intersect_key($_GET, array_flip(['range', 'pp', 'rp'])) + ['del' => 1]) . '#recent'); exit;
}
require __DIR__ . '/_header.php';
$db = db(); $today = date('Y-m-d');
// ---- period: 7 days / 30 days / all time (everything on this page follows it)
$range = $_GET['range'] ?? '30';
if (!in_array($range, ['7', '30', 'all'], true)) $range = '30';
$since = $range === '7' ? date('Y-m-d', strtotime('-6 days')) : ($range === 'all' ? null : date('Y-m-d', strtotime('-29 days')));
$W = $since ? 'day>=?' : '1=1'; $P = $since ? [$since] : [];
$rangeName = t('range_' . $range);
$all = function ($sql, $a = []) use ($db) { $q = $db->prepare($sql); $q->execute($a); return $q->fetchAll(); };
$one = function ($sql, $a = []) use ($db) { $q = $db->prepare($sql); $q->execute($a); return $q->fetchColumn(); };
$vToday = $one('SELECT COUNT(*) FROM visits WHERE day=?', [$today]);
$visits = (int)$one("SELECT COUNT(*) FROM visits WHERE $W", $P);
$uniq = (int)$one("SELECT COUNT(DISTINCT ip_hash) FROM visits WHERE $W", $P);
$cTotal = (int)$one("SELECT COUNT(*) FROM clicks WHERE $W", $P);
// ---- chart: daily bars for 7 / 30 days, monthly bars (last 12 months) for all time
$series = [];
if ($range === 'all') {
  $rows = $all("SELECT DATE_FORMAT(day,'%Y-%m') m, COUNT(*) c FROM visits GROUP BY m ORDER BY m DESC LIMIT 12");
  foreach (array_reverse($rows) as $r) $series[$r['m']] = (int)$r['c'];
  $label = function ($k) { return (string)(int)substr($k, 5, 2); };
} else {
  $days = (int)$range; $byDay = [];
  foreach ($all('SELECT day,COUNT(*) c FROM visits WHERE day>=? GROUP BY day', [date('Y-m-d', strtotime('-' . ($days - 1) . ' days'))]) as $r) $byDay[$r['day']] = (int)$r['c'];
  for ($i = $days - 1; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-$i days")); $series[$d] = $byDay[$d] ?? 0; }
  $label = function ($k) { return date('j', strtotime($k)); };
}
$max = max(1, $series ? max($series) : 1); $dense = count($series) > 14;
// ---- breakdowns for the period
$srcRows = $all("SELECT source k, COUNT(*) c FROM visits WHERE $W GROUP BY source ORDER BY c DESC", $P);
$srcDet = $all("SELECT source, source_detail k, COUNT(*) c FROM visits WHERE $W AND source_detail<>'' GROUP BY source, source_detail ORDER BY c DESC LIMIT 10", $P);
$camps = $all("SELECT utm_campaign c1, utm_source s1, utm_medium m1, COUNT(*) c FROM visits WHERE $W AND (utm_campaign<>'' OR utm_source<>'') GROUP BY utm_campaign, utm_source, utm_medium ORDER BY c DESC LIMIT 10", $P);
$countries = $all("SELECT country k, country_code cc, COUNT(*) c FROM visits WHERE $W AND country<>'' GROUP BY country, country_code ORDER BY c DESC LIMIT 10", $P);
$cities = $all("SELECT city, region, country, country_code cc, COUNT(*) c FROM visits WHERE $W AND city<>'' GROUP BY city, region, country, country_code ORDER BY c DESC LIMIT 10", $P);
$top = $all("SELECT label,kind,COUNT(*) c FROM clicks WHERE $W GROUP BY kind,item_id,label ORDER BY c DESC LIMIT 10", $P);
$dev = $all("SELECT IF(os<>'', os, CONCAT('~', device)) k, COUNT(*) c FROM visits WHERE $W GROUP BY k ORDER BY c DESC", $P);
// ---- recent visits (paged, also follows the period)
$pp = (int)($_GET['pp'] ?? 10); if (!in_array($pp, [10, 15], true)) $pp = 10;
$pages = max(1, (int)ceil($visits / $pp)); $rp = max(1, min($pages, (int)($_GET['rp'] ?? 1))); $off = ($rp - 1) * $pp;
$recent = $all("SELECT id,os,ip,visited_at,device,browser,city,country,country_code,source,source_detail,utm_campaign FROM visits WHERE $W ORDER BY id DESC LIMIT $pp OFFSET $off", $P);
$q = function ($over) use ($range, $pp, $rp) { return '?' . http_build_query(array_merge(['range' => $range, 'pp' => $pp, 'rp' => $rp], $over)); };
$devName = function ($d) { return in_array($d, ['mobile', 'desktop', 'tablet'], true) ? t($d) : $d; };
$devLabel = function ($k) use ($devName) { return $k !== '' && $k[0] === '~' ? $devName(substr($k, 1)) : os_label($k, ''); };
$allVisits = (int)$one('SELECT COUNT(*) FROM visits');
$srcName = function ($s) { return in_array($s, ['qr', 'search', 'social', 'email', 'referral', 'campaign', 'direct'], true) ? t('src_' . $s) : $s; };
$place = function ($city, $country) { $p = implode(', ', array_filter([$city, $country])); return $p !== '' ? $p : t('unknown'); };
$bars = function ($rows, $labelFn) {
  if (!$rows) return '<p class="muted">' . e(t('no_data')) . '</p>';
  $m = max(array_column($rows, 'c')); $h = '';
  foreach ($rows as $bi => $r) $h .= '<div class="brow"><span class="n">' . $labelFn($r) . '</span><span class="b"><i style="width:' . round($r['c'] / $m * 100) . '%;--n:' . $bi . '"></i></span><span class="c">' . (int)$r['c'] . '</span></div>';
  return $h;
};
?>
<h1 class="pt"><?= e(t('visitors_title')) ?></h1>
<?php if (isset($_GET['del'])): ?><p class="ok"><?= e(t('visits_deleted')) ?></p><?php endif; ?>
<div class="live aniblock"><span class="dot"></span><b id="online"><?= online_count() ?></b><span class="l"><?= e(t('online_now')) ?></span></div>
<div class="panel aniblock"><b><?= e(t('live_visitors')) ?></b>
<ul class="vlist" id="live-list" data-unknown="<?= e(t('unknown')) ?>"></ul></div>

<div class="rangesw"><?php foreach (['7', '30', 'all'] as $k): ?><a href="?range=<?= $k ?>"<?= $range === $k ? ' class="on" aria-current="true"' : '' ?>><?= e(t('range_' . $k)) ?></a><?php endforeach; ?></div>

<div class="stats aniblock">
<div class="stat"><b><?= $vToday ?></b><?= e(t('visits_today')) ?></div><div class="stat"><b><?= $visits ?></b><?= e(t('stat_visits')) ?> <span class="muted">· <?= e($rangeName) ?></span></div>
<div class="stat"><b><?= $uniq ?></b><?= e(t('stat_unique')) ?> <span class="muted">· <?= e($rangeName) ?></span></div><div class="stat"><b><?= $cTotal ?></b><?= e(t('stat_clicks')) ?> <span class="muted">· <?= e($rangeName) ?></span></div></div>
<h2><?= e(t('visits_over_time')) ?> <span class="muted">· <?= e($rangeName) ?></span></h2>
<div class="panel aniblock"><div class="chart<?= $dense ? ' dense' : '' ?>"><?php $ci = 0; foreach ($series as $k => $c): ?><div style="height:<?= round($c / $max * 100) ?>%;--n:<?= $ci++ ?>" title="<?= e($k . ': ' . $c) ?>"><small><?= $c ?></small></div><?php endforeach; ?></div>
<div class="days"><?php foreach ($series as $k => $c): ?><span><?= e($label($k)) ?></span><?php endforeach; ?></div></div>

<div class="two">
<div><h2><?= e(t('sources')) ?></h2><div class="panel aniblock"><?= $bars($srcRows, function ($r) use ($srcName) { return e($srcName($r['k'])); }) ?></div>
<p class="note"><?= e(t('src_note')) ?></p></div>
<div><h2><?= e(t('src_detail')) ?></h2><div class="panel aniblock"><?= $bars($srcDet, function ($r) use ($srcName) { return e($srcName($r['source']) . ': ' . $r['k']); }) ?></div></div>
</div>
<h2><?= e(t('campaigns')) ?></h2>
<div class="panel aniblock"><?= $bars($camps, function ($r) { return e(($r['c1'] !== '' ? $r['c1'] : '-') . ' · ' . ($r['s1'] !== '' ? $r['s1'] : '-') . ($r['m1'] !== '' ? ' / ' . $r['m1'] : '')); }) ?></div>
<div class="two">
<div><h2><?= e(t('countries')) ?></h2><div class="panel aniblock"><?= $bars($countries, function ($r) { return '<span class="fl">' . flag_html($r['cc']) . '</span> ' . e($r['k']); }) ?></div></div>
<div><h2><?= e(t('cities')) ?></h2><div class="panel aniblock"><?= $bars($cities, function ($r) { return '<span class="fl">' . flag_html($r['cc']) . '</span> ' . e($r['city'] . ($r['region'] && $r['region'] !== $r['city'] ? ', ' . $r['region'] : '')); }) ?></div></div>
</div>
<p class="note"><?= e(t('geo_note')) ?></p>
<div class="two">
<div><h2><?= e(t('top_clicked')) ?></h2><div class="panel aniblock"><?= $bars($top, function ($r) { return e($r['label']) . ' <span class="muted">' . e($r['kind'] === 'p' ? t('k_product') : ($r['kind'] === 'b' ? t('k_banner') : ($r['kind'] === 'm' ? t('k_map') : ($r['kind'] === 'w' ? t('k_order') : ($r['kind'] === 'f' ? t('k_wafloat') : t('k_link')))))) . '</span>'; }) ?></div></div>
<div><h2><?= e(t('device')) ?></h2><div class="panel aniblock"><?= $bars($dev, function ($r) use ($devLabel) { return e($devLabel($r['k'])); }) ?></div></div>
</div>

<h2 id="recent"><?= e(t('recent_visits')) ?> <span class="muted">· <?= e($rangeName) ?></span></h2>
<div class="panel tblwrap"><table class="tbl"><tr><th><?= e(t('time')) ?></th><th><?= e(t('visitor_location')) ?></th><th><?= e(t('ip_col')) ?></th><th><?= e(t('source')) ?></th><th><?= e(t('device')) ?></th><th><?= e(t('browser')) ?></th><th></th></tr>
<?php foreach ($recent as $v): ?><tr><td dir="ltr"><?= e($v['visited_at']) ?></td><td><span class="fl"><?= flag_html($v['country_code']) ?></span> <?= e($place($v['city'], $v['country'])) ?></td><td dir="ltr"><?= $v['ip'] !== '' ? e($v['ip']) : '-' ?></td><td><?= e($srcName($v['source'])) ?><?= $v['source_detail'] !== '' ? ' <span class="muted">(' . e($v['source_detail']) . ')</span>' : '' ?><?= $v['utm_campaign'] !== '' ? ' <span class="tag">' . e($v['utm_campaign']) . '</span>' : '' ?></td><td><?= e(os_label($v['os'], $v['device'])) ?></td><td><?= e($v['browser']) ?></td><td><form method="post" data-confirm="<?= e(t('confirm_del_visit')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><button class="btn-sm del" name="act" value="delvisit"><?= e(t('del_visit')) ?></button></form></td></tr><?php endforeach; if (!$recent) echo '<tr><td colspan="7" class="muted">' . e(t('no_data')) . '</td></tr>'; ?></table></div>
<div class="pager">
<?php if ($rp > 1): ?><a href="<?= e($q(['rp' => $rp - 1])) ?>#recent">&laquo; <?= e(t('prev')) ?></a><?php endif;
$from = max(1, $rp - 2); $to = min($pages, $rp + 2);
if ($from > 1): ?><a href="<?= e($q(['rp' => 1])) ?>#recent">1</a><?php if ($from > 2): ?><span class="dots">…</span><?php endif; endif;
for ($n = $from; $n <= $to; $n++): ?><a href="<?= e($q(['rp' => $n])) ?>#recent"<?= $n === $rp ? ' class="on" aria-current="page"' : '' ?>><?= $n ?></a><?php endfor;
if ($to < $pages): if ($to < $pages - 1): ?><span class="dots">…</span><?php endif; ?><a href="<?= e($q(['rp' => $pages])) ?>#recent"><?= $pages ?></a><?php endif;
if ($rp < $pages): ?><a href="<?= e($q(['rp' => $rp + 1])) ?>#recent"><?= e(t('next')) ?> &raquo;</a><?php endif; ?>
</div>
<?php if ($allVisits > 0): ?><form method="post" data-confirm="<?= e(t('confirm_del_all')) ?>" style="margin:4px 0 10px"><?= csrf_field() ?><button class="btn-sm del" name="act" value="delall"><?= e(t('del_all_visits')) ?> (<?= $allVisits ?>)</button></form><?php endif; ?>
<p class="note"><?= e(sprintf(t('page_x_of'), $rp, $pages)) ?> · <?= e(t('per_page')) ?>:
<?php foreach ([10, 15] as $n): ?> <a href="<?= e($q(['pp' => $n, 'rp' => 1])) ?>#recent"<?= $pp === $n ? ' style="font-weight:800;text-decoration:none"' : '' ?>><?= $n ?></a><?php endforeach; ?></p>
<?php require __DIR__ . '/_footer.php';
