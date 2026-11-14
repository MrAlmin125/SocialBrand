<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $pos = in_array($_POST['position'] ?? '', BANNER_POSITIONS, true) ? $_POST['position'] : 'before_products';
    $ratio = isset(RATIOS[$_POST['ratio'] ?? '']) ? $_POST['ratio'] : '3/1';
    $fit = ($_POST['fit'] ?? '') === 'contain' ? 'contain' : 'cover';
    $f = [trim($_POST['name']), $pos, isset($_POST['autoplay']) ? 1 : 0, max(2, min(20, (int)($_POST['interval_s'] ?? 5))), $ratio, $fit, isset($_POST['arrows']) ? 1 : 0, isset($_POST['dots']) ? 1 : 0, (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0];
    if ($id) { $f[] = $id; db()->prepare('UPDATE sliders SET name=?,position=?,autoplay=?,interval_s=?,ratio=?,fit=?,arrows=?,dots=?,sort=?,active=? WHERE id=?')->execute($f); $msg = t('slider_updated'); }
    else { db()->prepare('INSERT INTO sliders (name,position,autoplay,interval_s,ratio,fit,arrows,dots,sort,active) VALUES (?,?,?,?,?,?,?,?,?,?)')->execute($f); $msg = t('slider_added'); }
  } elseif ($act === 'delete' && $id) {
    $q = db()->prepare('SELECT image FROM slides WHERE slider_id=?'); $q->execute([$id]);
    foreach ($q->fetchAll(PDO::FETCH_COLUMN) as $img) delete_upload($img);
    db()->prepare('DELETE FROM slides WHERE slider_id=?')->execute([$id]);
    db()->prepare('DELETE FROM sliders WHERE id=?')->execute([$id]); $msg = t('slider_deleted');
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE sliders SET active=1-active WHERE id=?')->execute([$id]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM sliders WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query("SELECT r.*, (SELECT COUNT(*) FROM slides s WHERE s.slider_id=r.id) cnt FROM sliders r ORDER BY FIELD(position,'" . implode("','", BANNER_POSITIONS) . "'), sort, id")->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
$posLabel = function ($p) { return $p === 'top' ? t('bpos_top') : t('pos_' . $p); };
?>
<h1 class="pt"><?= e(t('banners')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('bn_intro')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_slider') : t('add_slider')) ?></h2>
<div class="two"><label><?= e(t('slider_name')) ?><input name="name" value="<?= e($cur('name')) ?>" required></label>
<label><?= e(t('slider_position')) ?><select name="position"><?php foreach (BANNER_POSITIONS as $p): ?><option value="<?= $p ?>"<?= $cur('position', 'before_products') === $p ? ' selected' : '' ?>><?= e($posLabel($p)) ?></option><?php endforeach; ?></select></label></div>
<div class="two"><label><?= e(t('ratio')) ?><select name="ratio"><?php foreach (RATIOS as $k => $l): ?><option value="<?= e($k) ?>"<?= $cur('ratio', '3/1') === $k ? ' selected' : '' ?>><?= e(t($l)) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('fit')) ?><select name="fit"><option value="cover"<?= $cur('fit', 'cover') !== 'contain' ? ' selected' : '' ?>><?= e(t('fit_cover')) ?></option><option value="contain"<?= $cur('fit') === 'contain' ? ' selected' : '' ?>><?= e(t('fit_contain')) ?></option></select></label></div>
<div class="two"><label class="checkrow"><input type="checkbox" name="autoplay" value="1"<?= !$edit || $edit['autoplay'] ? ' checked' : '' ?>><?= e(t('autoplay')) ?></label>
<label><?= e(t('interval')) ?><input type="number" name="interval_s" min="2" max="20" value="<?= (int)$cur('interval_s', 5) ?>"></label></div>
<div class="two"><label class="checkrow"><input type="checkbox" name="arrows" value="1"<?= !$edit || $edit['arrows'] ? ' checked' : '' ?>><?= e(t('show_arrows')) ?></label>
<label class="checkrow"><input type="checkbox" name="dots" value="1"<?= !$edit || $edit['dots'] ? ' checked' : '' ?>><?= e(t('show_dots')) ?></label></div>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)$cur('sort', $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="banners.php"><?= e(t('cancel')) ?></a><a class="btn-sm" href="slides.php?slider=<?= (int)$edit['id'] ?>"><?= e(t('manage_slides')) ?></a><?php endif; ?></div></form>

<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th><?= e(t('slider')) ?></th><th><?= e(t('position')) ?></th><th><?= e(t('slides_count')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td><td><?= e($r['name']) ?></td><td><span class="tag"><?= e($posLabel($r['position'])) ?></span></td><td><?= (int)$r['cnt'] ?></td>
<td><div class="row"><a class="btn-sm" href="slides.php?slider=<?= (int)$r['id'] ?>"><?= e(t('manage_slides')) ?></a><a class="btn-sm gray" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_slider')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
