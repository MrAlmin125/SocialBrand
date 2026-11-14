<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $pos = in_array($_POST['position'] ?? '', HEADING_POSITIONS, true) ? $_POST['position'] : 'before_products';
    $al = in_array($_POST['align'] ?? '', ['start', 'center', 'end'], true) ? $_POST['align'] : 'center';
    $sz = in_array($_POST['size'] ?? '', ['sm', 'md', 'lg', 'xl', 'xxl'], true) ? $_POST['size'] : 'md';
    $bg = isset($_POST['use_bg']) ? hex_or($_POST['bg_color'] ?? '') : ''; $fg = isset($_POST['use_text']) ? hex_or($_POST['text_color'] ?? '') : '';
    $f = [$pos, trim($_POST['text_en']), trim($_POST['text_ar']), trim($_POST['sub_en']), trim($_POST['sub_ar']), trim($_POST['url']), $al, $sz, $bg, $fg, (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0];
    if ($id) { $f[] = $id; db()->prepare('UPDATE headings SET position=?,text_en=?,text_ar=?,sub_en=?,sub_ar=?,url=?,align=?,size=?,bg_color=?,text_color=?,sort=?,active=? WHERE id=?')->execute($f); $msg = t('heading_updated'); }
    else { db()->prepare('INSERT INTO headings (position,text_en,text_ar,sub_en,sub_ar,url,align,size,bg_color,text_color,sort,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')->execute($f); $msg = t('heading_added'); }
  } elseif ($act === 'delete' && $id) { db()->prepare('DELETE FROM headings WHERE id=?')->execute([$id]); $msg = t('heading_deleted'); }
  elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE headings SET active=1-active WHERE id=?')->execute([$id]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM headings WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query("SELECT * FROM headings ORDER BY FIELD(position,'" . implode("','", HEADING_POSITIONS) . "'), sort, id")->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
?>
<h1 class="pt"><?= e(t('headings')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('hd_intro')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_heading') : t('add_heading')) ?></h2>
<label><?= e(t('position')) ?><select name="position"><?php foreach (HEADING_POSITIONS as $p): ?><option value="<?= $p ?>"<?= $cur('position', 'before_products') === $p ? ' selected' : '' ?>><?= e(t('pos_' . $p)) ?></option><?php endforeach; ?></select></label>
<div class="two"><label><?= e(t('hd_text_en')) ?><input name="text_en" dir="<?= first_dir() ?>" value="<?= e($cur('text_en')) ?>"></label>
<label><?= e(t('hd_text_ar')) ?><input name="text_ar" dir="<?= second_dir() ?>" value="<?= e($cur('text_ar')) ?>"></label></div>
<div class="two"><label><?= e(t('hd_sub_en')) ?><input name="sub_en" dir="<?= first_dir() ?>" value="<?= e($cur('sub_en')) ?>"></label>
<label><?= e(t('hd_sub_ar')) ?><input name="sub_ar" dir="<?= second_dir() ?>" value="<?= e($cur('sub_ar')) ?>"></label></div>
<label><?= e(t('hd_link')) ?><input name="url" dir="ltr" value="<?= e($cur('url')) ?>" placeholder="https://"></label>
<div class="two"><label><?= e(t('align')) ?><select name="align"><?php foreach (['start' => 'al_start', 'center' => 'al_center', 'end' => 'al_end'] as $k => $l): ?><option value="<?= $k ?>"<?= $cur('align', 'center') === $k ? ' selected' : '' ?>><?= e(t($l)) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('size')) ?><select name="size"><?php foreach (['sm' => 'sz_sm', 'md' => 'sz_md', 'lg' => 'sz_lg', 'xl' => 'sz_xl', 'xxl' => 'sz_xxl'] as $k => $l): ?><option value="<?= $k ?>"<?= $cur('size', 'md') === $k ? ' selected' : '' ?>><?= e(t($l)) ?></option><?php endforeach; ?></select></label></div>
<div class="two"><div><label class="checkrow"><input type="checkbox" name="use_text" value="1"<?= $cur('text_color') !== '' ? ' checked' : '' ?>><?= e(t('use_text_color')) ?></label><input type="color" name="text_color" value="<?= e(hex_or($cur('text_color'), '#241B12')) ?>"></div>
<div><label class="checkrow"><input type="checkbox" name="use_bg" value="1"<?= $cur('bg_color') !== '' ? ' checked' : '' ?>><?= e(t('use_bg_color')) ?></label><input type="color" name="bg_color" value="<?= e(hex_or($cur('bg_color'), '#A47735')) ?>"></div></div>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)$cur('sort', $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="headings.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>

<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th><?= e(t('position')) ?></th><th><?= e(t('text')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td><td><span class="tag"><?= e(t('pos_' . $r['position'])) ?></span></td><td><?= e(pick($r, 'text')) ?></td>
<td><div class="row"><a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_heading')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
