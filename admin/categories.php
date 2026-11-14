<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $f = [trim($_POST['name_en']), trim($_POST['name_ar']), (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0];
    if ($id) { $f[] = $id; db()->prepare('UPDATE categories SET name_en=?,name_ar=?,sort=?,active=? WHERE id=?')->execute($f); $msg = t('cat_updated'); }
    else { db()->prepare('INSERT INTO categories (name_en,name_ar,sort,active) VALUES (?,?,?,?)')->execute($f); $msg = t('cat_added'); }
  } elseif ($act === 'delete' && $id) {
    db()->prepare('UPDATE products SET category_id=0 WHERE category_id=?')->execute([$id]);
    db()->prepare('DELETE FROM categories WHERE id=?')->execute([$id]); $msg = t('cat_deleted');
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE categories SET active=1-active WHERE id=?')->execute([$id]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM categories WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) n FROM categories c ORDER BY sort,id')->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
?>
<h1 class="pt"><?= e(t('categories')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_category') : t('add_category')) ?></h2>
<div class="two"><label><?= e(t('cat_name_en')) ?><input name="name_en" dir="<?= first_dir() ?>" value="<?= e($cur('name_en')) ?>" required></label>
<label><?= e(t('cat_name_ar')) ?><input name="name_ar" dir="<?= second_dir() ?>" value="<?= e($cur('name_ar')) ?>" required></label></div>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)$cur('sort', $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="categories.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>
<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th><?= e(t('category')) ?></th><th><?= e(t('admin_products')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td><td><?= e(pick($r, 'name')) ?></td><td><?= (int)$r['n'] ?></td>
<td><div class="row"><a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_cat')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
