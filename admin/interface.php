<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$pair = [first_lang(), second_lang()]; $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  foreach ($pair as $c) foreach (UI_KEYS as $k) {
    $v = trim($_POST['ui'][$c][$k] ?? ''); $key = 'ui_' . $c . '_' . $k;
    if ($v === '' || $v === (STR[$c][$k] ?? '')) db()->prepare('DELETE FROM settings WHERE k=?')->execute([$key]);   // same as the built-in text: no override needed
    else set_setting($key, mb_substr($v, 0, 120));
  }
  settings(true); $msg = t('if_saved');
}
$s = settings();
require __DIR__ . '/_header.php';
$nm = function ($c) { return LANGS[$c]['label'] . ' — ' . (L() === 'ar' ? LANGS[$c]['ar'] : LANGS[$c]['en']); };
?>
<h1 class="pt"><?= e(t('interface_texts')) ?></h1>
<p class="note" style="font-size:14px"><?= e(sprintf(t('if_intro'), $nm($pair[0]) . '  |  ' . $nm($pair[1]))) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<div class="panel">
<?php foreach (UI_KEYS as $k): ?>
<div class="two ifrow"><label><span class="muted"><?= e(LANGS[$pair[0]]['label']) ?></span> <span class="muted">(<?= e(STR['en'][$k] ?? $k) ?>)</span><input name="ui[<?= e($pair[0]) ?>][<?= e($k) ?>]" dir="<?= LANGS[$pair[0]]['dir'] ?>" maxlength="120" value="<?= e($s['ui_' . $pair[0] . '_' . $k] ?? (STR[$pair[0]][$k] ?? '')) ?>"></label>
<label><span class="muted"><?= e(LANGS[$pair[1]]['label']) ?></span><input name="ui[<?= e($pair[1]) ?>][<?= e($k) ?>]" dir="<?= LANGS[$pair[1]]['dir'] ?>" maxlength="120" value="<?= e($s['ui_' . $pair[1] . '_' . $k] ?? (STR[$pair[1]][$k] ?? '')) ?>"></label></div>
<?php endforeach; ?>
</div>
<button class="btn-primary"><?= e(t('save')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
