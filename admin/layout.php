<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
$order = function () { $s = settings(true); $o = array_keys(SECTIONS); usort($o, function ($a, $b) use ($s) { $x = (int)($s['order_' . $a] ?? SECTIONS[$a]); $y = (int)($s['order_' . $b] ?? SECTIONS[$b]); return $x <=> $y ?: strcmp($a, $b); }); return $o; };
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $key = $_POST['key'] ?? ''; $dir = $_POST['dir'] ?? '';
  $o = $order(); $i = array_search($key, $o, true);
  if ($i !== false && in_array($dir, ['up', 'down'], true)) {
    $j = $dir === 'up' ? $i - 1 : $i + 1;
    if (isset($o[$j])) { $t = $o[$i]; $o[$i] = $o[$j]; $o[$j] = $t; }
    foreach ($o as $n => $k) set_setting('order_' . $k, (string)(($n + 1) * 10));
    $msg = t('pl_saved');
  }
}
$o = $order();
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('page_layout')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('pl_intro')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<div class="panel">
<?php foreach ($o as $n => $k): ?>
<div class="layrow"><span class="num"><?= $n + 1 ?></span><b><?= e(t('sn_' . $k)) ?></b>
  <form method="post" class="lay-btns"><?= csrf_field() ?><input type="hidden" name="key" value="<?= e($k) ?>">
    <button class="btn-sm gray" name="dir" value="up"<?= $n === 0 ? ' disabled' : '' ?> title="<?= e(t('pl_up')) ?>" aria-label="<?= e(t('pl_up')) ?>">&#9650;</button>
    <button class="btn-sm gray" name="dir" value="down"<?= $n === count($o) - 1 ? ' disabled' : '' ?> title="<?= e(t('pl_down')) ?>" aria-label="<?= e(t('pl_down')) ?>">&#9660;</button></form></div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/_footer.php';
