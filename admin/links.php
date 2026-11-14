<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $old = '';
    if ($id) { $q = db()->prepare('SELECT icon FROM links WHERE id=?'); $q->execute([$id]); $old = (string)$q->fetchColumn(); }
    $icon = $old;
    if (!empty($_FILES['icon']['name'])) {
      $n = save_image($_FILES['icon'], 'icon');
      if ($n) { delete_upload($old); $icon = $n; } else $err = t('upload_fail');
    }
    if (isset($_POST['remove_icon'])) { delete_upload($old); $icon = ''; }
    $type = in_array($_POST['type'] ?? '', LINK_TYPES, true) ? $_POST['type'] : 'other';
    $bgc = isset($_POST['custom_colors']) ? hex_or($_POST['bg_color'] ?? '') : ''; $fgc = isset($_POST['custom_colors']) ? hex_or($_POST['text_color'] ?? '') : '';
    $f = [$type, trim($_POST['title_en']), trim($_POST['title_ar']), trim($_POST['value']), $icon, $bgc, $fgc, (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0];
    if ($id) { $f[] = $id; db()->prepare('UPDATE links SET type=?,title_en=?,title_ar=?,value=?,icon=?,bg_color=?,text_color=?,sort=?,active=? WHERE id=?')->execute($f); if (!$err) $msg = t('link_updated'); }
    else { db()->prepare('INSERT INTO links (type,title_en,title_ar,value,icon,bg_color,text_color,sort,active) VALUES (?,?,?,?,?,?,?,?,?)')->execute($f); if (!$err) $msg = t('link_added'); }
  } elseif ($act === 'remove_icon' && $id) {
    $q = db()->prepare('SELECT icon FROM links WHERE id=?'); $q->execute([$id]); delete_upload($q->fetchColumn());
    db()->prepare("UPDATE links SET icon='' WHERE id=?")->execute([$id]); $msg = t('icon_removed');
  } elseif ($act === 'delete' && $id) {
    $q = db()->prepare('SELECT icon FROM links WHERE id=?'); $q->execute([$id]); delete_upload($q->fetchColumn());
    db()->prepare('DELETE FROM links WHERE id=?')->execute([$id]); $msg = t('link_deleted');
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE links SET active=1-active WHERE id=?')->execute([$id]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM links WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query('SELECT * FROM links ORDER BY sort,id')->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('links')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_link') : t('add_link')) ?></h2>
<label><?= e(t('type')) ?><select name="type"><?php foreach (LINK_TYPES as $k): ?><option value="<?= e($k) ?>"<?= ($edit['type'] ?? '') === $k ? ' selected' : '' ?>><?= e(t('type_' . $k)) ?></option><?php endforeach; ?></select></label>
<div class="two"><label><?= e(t('title_en')) ?><input name="title_en" dir="<?= first_dir() ?>" value="<?= e($edit['title_en'] ?? '') ?>" required></label>
<label><?= e(t('title_ar')) ?><input name="title_ar" dir="<?= second_dir() ?>" value="<?= e($edit['title_ar'] ?? '') ?>" required></label></div>
<label><?= e(t('value')) ?> <span class="muted hint">(<?= e(t('value_hint')) ?>)</span><input name="value" dir="ltr" value="<?= e($edit['value'] ?? '') ?>" required></label>
<label><?= e(t('custom_icon')) ?><img id="pic" class="icon-sm" style="margin:6px 0;<?= empty($edit['icon']) ? 'display:none' : '' ?>" src="<?= empty($edit['icon']) ? '' : e(img_src($edit['icon'], '../uploads/')) ?>" alt=""><input type="file" name="icon" accept="image/png,image/jpeg,image/webp" data-preview="pic"></label>
<?php if (!empty($edit['icon'])): ?><p style="margin-bottom:12px"><button type="submit" form="rmicon" class="btn-sm del"><?= e(t('remove_icon')) ?></button></p><?php endif; ?>
<div class="two"><label><input type="checkbox" name="custom_colors" value="1"<?= !empty($edit['bg_color']) ? ' checked' : '' ?>><?= e(t('custom_colors')) ?></label><span></span></div>
<div class="two"><label><?= e(t('bg_color')) ?><input type="color" name="bg_color" value="<?= e(hex_or($edit['bg_color'] ?? '', '#A47735')) ?>"></label>
<label><?= e(t('text_color')) ?><input type="color" name="text_color" value="<?= e(hex_or($edit['text_color'] ?? '', '#FFFFFF')) ?>"></label></div>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)($edit['sort'] ?? $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="links.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>
<?php if (!empty($edit['icon'])): ?><form method="post" id="rmicon"><?= csrf_field() ?><input type="hidden" name="act" value="remove_icon"><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"></form><?php endif; ?>
<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th></th><th><?= e(t('type')) ?></th><th><?= e(t('title')) ?></th><th><?= e(t('value')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td><td><?php if ($r['icon'] !== ''): ?><img class="icon-sm" src="<?= e(img_src($r['icon'], '../uploads/')) ?>" alt=""><?php endif; ?></td><td><?= $r['bg_color'] !== '' ? '<span style="display:inline-block;width:14px;height:14px;border-radius:4px;vertical-align:middle;margin-inline-end:6px;border:1px solid #ccc;background:' . e($r['bg_color']) . '"></span>' : '' ?><?= e(t('type_' . $r['type'])) ?></td><td><?= e(pick($r, 'title')) ?></td><td dir="ltr" class="muted"><?= e($r['value']) ?></td>
<td><div class="row"><a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_link')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
