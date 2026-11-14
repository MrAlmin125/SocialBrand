<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  foreach (['seo_title_en' => 70, 'seo_title_ar' => 70, 'seo_desc_en' => 200, 'seo_desc_ar' => 200] as $k => $n) set_setting($k, mb_substr(trim($_POST[$k] ?? ''), 0, $n));
  set_setting('seo_index', isset($_POST['seo_index']) ? '1' : '0');
  settings(true); $msg = t('saved');
}
$s = settings();
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('seo')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('seo_intro')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?>
<div class="two"><label><?= e(t('seo_title_en')) ?><input name="seo_title_en" dir="<?= first_dir() ?>" maxlength="70" value="<?= e($s['seo_title_en'] ?? '') ?>"></label>
<label><?= e(t('seo_title_ar')) ?><input name="seo_title_ar" dir="<?= second_dir() ?>" maxlength="70" value="<?= e($s['seo_title_ar'] ?? '') ?>"></label></div>
<div class="two"><label><?= e(t('seo_desc_en')) ?><textarea name="seo_desc_en" dir="<?= first_dir() ?>" rows="3" maxlength="200"><?= e($s['seo_desc_en'] ?? '') ?></textarea></label>
<label><?= e(t('seo_desc_ar')) ?><textarea name="seo_desc_ar" dir="<?= second_dir() ?>" rows="3" maxlength="200"><?= e($s['seo_desc_ar'] ?? '') ?></textarea></label></div>
<label class="checkrow"><input type="checkbox" name="seo_index" value="1"<?= flag_on($s, 'seo_index') ? ' checked' : '' ?>><?= e(t('seo_index')) ?></label>
<button class="btn-primary"><?= e(t('save')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
