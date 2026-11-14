<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'settings') {
    foreach (['cover_main', 'cover_autoplay'] as $k) set_setting($k, isset($_POST[$k]) ? '1' : '0');
    set_setting('cover_interval', (string)max(2, min(30, (int)($_POST['cover_interval'] ?? 4))));
    set_setting('cover_effect', ($_POST['cover_effect'] ?? '') === 'fade' ? 'fade' : 'slide');
    set_setting('cover_fit', ($_POST['cover_fit'] ?? '') === 'cover' ? 'cover' : 'contain');
    settings(true); $msg = t('saved');
  } elseif ($act === 'add') {                                     // several uploaded files and/or several image links at once
    $sort = (int)db()->query('SELECT COALESCE(MAX(sort),0) FROM cover_slides')->fetchColumn() + 1; $added = 0; $failed = 0;
    $ins = db()->prepare('INSERT INTO cover_slides (image,sort,active) VALUES (?,?,1)');
    $f = $_FILES['images'] ?? null;
    if ($f && is_array($f['name'])) {
      foreach ($f['name'] as $k => $nm) {
        if ($nm === '') continue;
        $n = save_image(['tmp_name' => $f['tmp_name'][$k], 'error' => $f['error'][$k], 'size' => $f['size'][$k]], 'cv');
        if ($n) { $ins->execute([$n, $sort++]); $added++; } else $failed++;
      }
    }
    foreach (preg_split('/\R/', (string)($_POST['image_urls'] ?? '')) as $u) {
      $u = trim($u); if ($u === '') continue;
      if (is_url($u)) { $ins->execute([$u, $sort++]); $added++; } else $failed++;
    }
    if ($added > 0) $msg = sprintf(t('slides_added'), $added);
    if ($failed > 0) $err = t('upload_fail'); elseif ($added === 0) $err = t('need_image');
  } elseif ($act === 'move' && $id) {
    $ids = db()->query('SELECT id FROM cover_slides ORDER BY sort,id')->fetchAll(PDO::FETCH_COLUMN); $i = array_search((string)$id, array_map('strval', $ids), true);
    $j = ($_POST['dir'] ?? '') === 'up' ? $i - 1 : $i + 1;
    if ($i !== false && isset($ids[$j])) { $t = $ids[$i]; $ids[$i] = $ids[$j]; $ids[$j] = $t; }
    $u = db()->prepare('UPDATE cover_slides SET sort=? WHERE id=?'); foreach ($ids as $n => $cid) $u->execute([$n + 1, $cid]);
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE cover_slides SET active=1-active WHERE id=?')->execute([$id]);
  } elseif ($act === 'delete' && $id) {
    $q = db()->prepare('SELECT image FROM cover_slides WHERE id=?'); $q->execute([$id]); delete_upload($q->fetchColumn());
    db()->prepare('DELETE FROM cover_slides WHERE id=?')->execute([$id]); $msg = t('slide_deleted');
  }
}
$rows = db()->query('SELECT * FROM cover_slides ORDER BY sort,id')->fetchAll();
$s = settings();
require __DIR__ . '/_header.php';
$chk = function ($k) use ($s) { return flag_on($s, $k) ? ' checked' : ''; };
?>
<h1 class="pt"><?= e(t('cover_images')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('cv_intro')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="settings">
<label class="checkrow"><input type="checkbox" name="cover_main" value="1"<?= $chk('cover_main') ?>><?= e(t('cv_main')) ?></label>
<label class="checkrow"><input type="checkbox" name="cover_autoplay" value="1"<?= $chk('cover_autoplay') ?>><?= e(t('cv_autoplay')) ?></label>
<div class="three"><label><?= e(t('cv_interval')) ?><input type="number" name="cover_interval" min="2" max="30" value="<?= e($s['cover_interval'] ?? '4') ?>"></label>
<label><?= e(t('cv_effect')) ?><select name="cover_effect"><option value="slide"<?= ($s['cover_effect'] ?? 'slide') !== 'fade' ? ' selected' : '' ?>><?= e(t('cv_slide')) ?></option><option value="fade"<?= ($s['cover_effect'] ?? '') === 'fade' ? ' selected' : '' ?>><?= e(t('cv_fade')) ?></option></select></label>
<label><?= e(t('cv_fit')) ?><select name="cover_fit"><option value="contain"<?= ($s['cover_fit'] ?? 'contain') !== 'cover' ? ' selected' : '' ?>><?= e(t('cv_fit_contain')) ?></option><option value="cover"<?= ($s['cover_fit'] ?? '') === 'cover' ? ' selected' : '' ?>><?= e(t('cv_fit_cover')) ?></option></select></label></div>
<button class="btn-sm"><?= e(t('save')) ?></button></form>

<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="add">
<h2 style="margin-top:0"><?= e(t('add_slide')) ?></h2>
<label><?= e(t('images_multi')) ?><input type="file" name="images[]" accept="image/*" multiple></label>
<label><?= e(t('image_urls')) ?><textarea name="image_urls" dir="ltr" rows="3" placeholder="https://example.com/cover1.jpg"></textarea></label>
<button class="btn-sm"><?= e(t('add')) ?></button></form>

<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th></th><th></th></tr>
<?php if (!empty($s['cover'])): ?><tr><td>★</td><td><img class="thumb-sm" style="width:84px;height:48px;object-fit:contain;background:#f5f5f5" src="<?= e(img_src($s['cover'], '../uploads/')) ?>" alt=""></td><td class="muted"><?= e(t('cv_main_note')) ?> <a href="settings.php"><?= e(t('shop_info')) ?></a></td></tr><?php endif; ?>
<?php foreach ($rows as $n => $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= $n + 1 ?></td>
<td><img class="thumb-sm" style="width:84px;height:48px;object-fit:cover" src="<?= e(img_src($r['image'], '../uploads/')) ?>" alt=""></td>
<td><div class="row">
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="move" <?= $n === 0 ? 'disabled ' : '' ?>onclick="this.form.dir.value='up'">&#9650;</button><input type="hidden" name="dir" value=""></form>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="move" <?= $n === count($rows) - 1 ? 'disabled ' : '' ?>onclick="this.form.dir.value='down'">&#9660;</button><input type="hidden" name="dir" value=""></form>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_slide')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
