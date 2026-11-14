<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = ''; $anims = ['none', 'pulse', 'ripple', 'bounce', 'shake', 'float'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  set_setting('wa_float', isset($_POST['wa_float']) ? '1' : '0');
  set_setting('wa_float_side', ($_POST['wa_float_side'] ?? '') === 'start' ? 'start' : 'end');
  set_setting('wa_float_anim', in_array($_POST['wa_float_anim'] ?? '', $anims, true) ? $_POST['wa_float_anim'] : 'pulse');
  foreach (['wa_float_label_en' => 30, 'wa_float_label_ar' => 30, 'wa_float_msg_en' => 300, 'wa_float_msg_ar' => 300] as $k => $n) set_setting($k, mb_substr(trim($_POST[$k] ?? ''), 0, $n));
  settings(true); $msg = t('saved');
}
$s = settings(); $num = wa_number($s);
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('whatsapp')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?>
<label class="checkrow"><input type="checkbox" name="wa_float" value="1"<?= flag_on($s, 'wa_float') ? ' checked' : '' ?>><?= e(t('wf_enabled')) ?></label>
<p class="muted"><?= e(t('wf_number_note')) ?> <b dir="ltr"><?= $num !== '' ? e($num) : '-' ?></b> · <a href="appearance.php"><?= e(t('appearance')) ?></a></p>
<div class="two"><label><?= e(t('wf_side')) ?><select name="wa_float_side"><option value="end"<?= ($s['wa_float_side'] ?? 'end') !== 'start' ? ' selected' : '' ?>><?= e(t('side_end')) ?></option><option value="start"<?= ($s['wa_float_side'] ?? '') === 'start' ? ' selected' : '' ?>><?= e(t('side_start')) ?></option></select></label>
<label><?= e(t('wf_anim')) ?><select name="wa_float_anim"><?php foreach ($anims as $a): ?><option value="<?= $a ?>"<?= ($s['wa_float_anim'] ?? 'pulse') === $a ? ' selected' : '' ?>><?= e(t('wf_' . $a)) ?></option><?php endforeach; ?></select></label></div>
<div class="two"><label><?= e(t('wf_label_en')) ?><input name="wa_float_label_en" dir="<?= first_dir() ?>" maxlength="30" value="<?= e($s['wa_float_label_en'] ?? '') ?>"></label>
<label><?= e(t('wf_label_ar')) ?><input name="wa_float_label_ar" dir="<?= second_dir() ?>" maxlength="30" value="<?= e($s['wa_float_label_ar'] ?? '') ?>"></label></div>
<div class="two"><label><?= e(t('wf_msg_en')) ?><textarea name="wa_float_msg_en" dir="<?= first_dir() ?>" rows="3" maxlength="300"><?= e($s['wa_float_msg_en'] ?? '') ?></textarea></label>
<label><?= e(t('wf_msg_ar')) ?><textarea name="wa_float_msg_ar" dir="<?= second_dir() ?>" rows="3" maxlength="300"><?= e($s['wa_float_msg_ar'] ?? '') ?></textarea></label></div>
<button class="btn-primary"><?= e(t('save')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
