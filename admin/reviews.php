<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = ''; $posOk = ['after_profile', 'before_links', 'before_products', 'before_map', 'bottom'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'settings') {
    foreach (['reviews_enabled', 'reviews_submit'] as $k) set_setting($k, isset($_POST[$k]) ? '1' : '0');
    set_setting('reviews_limit', (string)max(1, min(30, (int)($_POST['reviews_limit'] ?? 8))));
    settings(true); $msg = t('saved');
  } elseif ($act === 'save') {
    $f = [mb_substr(trim($_POST['name']), 0, 80), max(1, min(5, (int)$_POST['rating'])), mb_substr(trim($_POST['text']), 0, 500), isset($_POST['approved']) ? 1 : 0];
    if ($id) { $f[] = $id; db()->prepare('UPDATE reviews SET name=?,rating=?,text=?,approved=? WHERE id=?')->execute($f); $msg = t('rv_updated'); }
    else { array_push($f, date('Y-m-d H:i:s')); db()->prepare('INSERT INTO reviews (name,rating,text,approved,created_at) VALUES (?,?,?,?,?)')->execute($f); $msg = t('rv_added'); }
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE reviews SET approved=1-approved WHERE id=?')->execute([$id]);
  } elseif ($act === 'delete' && $id) { db()->prepare('DELETE FROM reviews WHERE id=?')->execute([$id]); $msg = t('rv_deleted'); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM reviews WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$pending = db()->query('SELECT * FROM reviews WHERE approved=0 ORDER BY id DESC')->fetchAll();
$approved = db()->query('SELECT * FROM reviews WHERE approved=1 ORDER BY id DESC')->fetchAll();
$s = settings();
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
$chk = function ($k) use ($s) { return flag_on($s, $k) ? ' checked' : ''; };
$list = function ($rows) {
  if (!$rows) { echo '<p class="muted">' . e(t('rv_none')) . '</p>'; return; }
  foreach ($rows as $r): ?>
<div class="panel msgbox"><div class="meta"><b><?= e($r['name']) ?></b><span class="stars"><?= e(stars_text($r['rating'])) ?></span><span class="muted" dir="ltr"><?= e($r['created_at']) ?></span></div>
<div class="body"><?= e($r['text']) ?></div>
<div class="row"><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm" name="act" value="toggle"><?= e($r['approved'] ? t('rv_hide') : t('rv_approve')) ?></button></form>
<a class="btn-sm gray" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post" data-confirm="<?= e(t('confirm_del_rv')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></div>
<?php endforeach; };
?>
<h1 class="pt"><?= e(t('reviews')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="settings">
<label class="checkrow"><input type="checkbox" name="reviews_enabled" value="1"<?= $chk('reviews_enabled') ?>><?= e(t('reviews_enabled')) ?></label>
<label class="checkrow"><input type="checkbox" name="reviews_submit" value="1"<?= $chk('reviews_submit') ?>><?= e(t('reviews_submit')) ?></label>
<div class="two"><p class="muted"><?= e(t('pl_note')) ?> <a href="layout.php"><?= e(t('page_layout')) ?></a></p>
<label><?= e(t('reviews_limit')) ?><input type="number" name="reviews_limit" min="1" max="30" value="<?= e($s['reviews_limit'] ?? '8') ?>"></label></div>
<button class="btn-sm"><?= e(t('save')) ?></button></form>

<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('rv_edit') : t('rv_add')) ?></h2>
<div class="two"><label><?= e(t('rv_name')) ?><input name="name" maxlength="80" required value="<?= e($cur('name')) ?>"></label>
<label><?= e(t('rv_rating')) ?><select name="rating"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"<?= (int)$cur('rating', 5) === $i ? ' selected' : '' ?>><?= e(stars_text($i)) ?></option><?php endfor; ?></select></label></div>
<label><?= e(t('rv_text')) ?><textarea name="text" rows="3" maxlength="500" required><?= e($cur('text')) ?></textarea></label>
<label class="checkrow"><input type="checkbox" name="approved" value="1"<?= !$edit || $edit['approved'] ? ' checked' : '' ?>><?= e(t('rv_approved')) ?></label>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="reviews.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>

<h2><?= e(t('rv_pending')) ?> (<?= count($pending) ?>)</h2><?php $list($pending); ?>
<h2><?= e(t('rv_approved')) ?> (<?= count($approved) ?>)</h2><?php $list($approved); ?>
<?php require __DIR__ . '/_footer.php';
