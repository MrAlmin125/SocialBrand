<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
$tm = function ($v) { $v = trim((string)$v); return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v) ? $v : ''; };
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  foreach (['hours_enabled', 'hours_badge'] as $k) set_setting($k, isset($_POST[$k]) ? '1' : '0');
  foreach (['hours_title_en', 'hours_title_ar'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
  set_setting('hours_format', ($_POST['hours_format'] ?? '') === '24' ? '24' : '12');
  set_setting('hours_start', (string)max(0, min(6, (int)($_POST['hours_start'] ?? 6))));
  $st = db()->prepare('INSERT INTO hours (day,closed,all_day,open1,close1,open2,close2) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE closed=VALUES(closed),all_day=VALUES(all_day),open1=VALUES(open1),close1=VALUES(close1),open2=VALUES(open2),close2=VALUES(close2)');
  for ($d = 0; $d <= 6; $d++) {
    $st->execute([$d, isset($_POST['closed'][$d]) ? 1 : 0, isset($_POST['all_day'][$d]) ? 1 : 0, $tm($_POST['open1'][$d] ?? ''), $tm($_POST['close1'][$d] ?? ''), $tm($_POST['open2'][$d] ?? ''), $tm($_POST['close2'][$d] ?? '')]);
  }
  settings(true); $msg = t('hours_saved');
}
$s = settings(); $rows = [];
foreach (db()->query('SELECT * FROM hours')->fetchAll() as $x) $rows[(int)$x['day']] = $x;
$start = ((int)($s['hours_start'] ?? 6)) % 7;
require __DIR__ . '/_header.php';
$chk = function ($k) use ($s) { return flag_on($s, $k) ? ' checked' : ''; };
?>
<h1 class="pt"><?= e(t('hours')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="hours_enabled" value="1"<?= $chk('hours_enabled') ?>><?= e(t('hours_enabled')) ?></label>
<label class="checkrow"><input type="checkbox" name="hours_badge" value="1"<?= $chk('hours_badge') ?>><?= e(t('hours_badge')) ?></label>
<div class="two"><label><?= e(t('hours_title_en')) ?><input name="hours_title_en" dir="<?= first_dir() ?>" value="<?= e($s['hours_title_en'] ?? '') ?>"></label>
<label><?= e(t('hours_title_ar')) ?><input name="hours_title_ar" dir="<?= second_dir() ?>" value="<?= e($s['hours_title_ar'] ?? '') ?>"></label></div>
<div class="two"><label><?= e(t('hours_fmt')) ?><select name="hours_format"><option value="12"<?= ($s['hours_format'] ?? '12') !== '24' ? ' selected' : '' ?>><?= e(t('fmt_12')) ?></option><option value="24"<?= ($s['hours_format'] ?? '') === '24' ? ' selected' : '' ?>><?= e(t('fmt_24')) ?></option></select></label></div>
<p class="muted"><?= e(t('pl_note')) ?> <a href="layout.php"><?= e(t('page_layout')) ?></a></p>
<label><?= e(t('hours_start')) ?><select name="hours_start"><?php for ($d = 0; $d <= 6; $d++): ?><option value="<?= $d ?>"<?= $start === $d ? ' selected' : '' ?>><?= e(t('day_' . $d)) ?></option><?php endfor; ?></select></label></div>

<p class="note" style="font-size:14px"><?= e(t('hours_note')) ?></p>
<?php for ($i = 0; $i < 7; $i++): $d = ($start + $i) % 7; $r = $rows[$d] ?? ['closed' => 0, 'all_day' => 0, 'open1' => '', 'close1' => '', 'open2' => '', 'close2' => '']; ?>
<div class="panel dayrow">
  <div class="hrow"><b class="dayname"><?= e(t('day_' . $d)) ?></b>
    <label class="checkrow" style="margin:0"><input type="checkbox" name="closed[<?= $d ?>]" value="1"<?= $r['closed'] ? ' checked' : '' ?>><?= e(t('closed')) ?></label>
    <label class="checkrow" style="margin:0"><input type="checkbox" name="all_day[<?= $d ?>]" value="1"<?= $r['all_day'] ? ' checked' : '' ?>><?= e(t('all_day')) ?></label></div>
  <div class="times">
    <label><?= e(t('shift1')) ?> · <?= e(t('opens')) ?><input type="time" name="open1[<?= $d ?>]" value="<?= e($r['open1']) ?>"></label>
    <label><?= e(t('closes')) ?><input type="time" name="close1[<?= $d ?>]" value="<?= e($r['close1']) ?>"></label>
    <label><?= e(t('shift2')) ?> · <?= e(t('opens')) ?><input type="time" name="open2[<?= $d ?>]" value="<?= e($r['open2']) ?>"></label>
    <label><?= e(t('closes')) ?><input type="time" name="close2[<?= $d ?>]" value="<?= e($r['close2']) ?>"></label>
  </div>
</div>
<?php endfor; ?>
<button class="btn-primary"><?= e(t('save')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
