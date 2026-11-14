<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$sid = (int)($_GET['slider'] ?? $_POST['slider'] ?? 0);
$q = db()->prepare('SELECT * FROM sliders WHERE id=?'); $q->execute([$sid]); $slider = $q->fetch();
if (!$slider) { header('Location: banners.php'); exit; }
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save' && $id) {                                   // EDIT one slide: new file OR image link OR keep current
    $q = db()->prepare('SELECT image FROM slides WHERE id=? AND slider_id=?'); $q->execute([$id, $sid]); $old = (string)$q->fetchColumn();
    $img = $old; $url = trim($_POST['image_url'] ?? '');
    if (!empty($_FILES['image']['name'])) {
      $n = save_image($_FILES['image'], 'b');
      if ($n) { delete_upload($old); $img = $n; } else $err = t('upload_fail');
    } elseif ($url !== '') {
      if (is_url($url)) { if ($url !== $old) delete_upload($old); $img = $url; } else $err = t('bad_url');
    }
    if (!$err) {
      db()->prepare('UPDATE slides SET image=?,url=?,caption_en=?,caption_ar=?,sort=?,active=? WHERE id=? AND slider_id=?')
        ->execute([$img, trim($_POST['url']), trim($_POST['caption_en']), trim($_POST['caption_ar']), (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0, $id, $sid]);
      $msg = t('slide_updated');
    }
  } elseif ($act === 'save') {                                    // ADD: several uploaded files and/or several image links at once
    $sort = (int)$_POST['sort']; $added = 0; $failed = 0;
    $ins = db()->prepare('INSERT INTO slides (slider_id,image,url,caption_en,caption_ar,sort,active) VALUES (?,?,?,?,?,?,?)');
    $add = function ($img) use ($ins, $sid, &$sort, &$added) {
      $ins->execute([$sid, $img, trim($_POST['url']), trim($_POST['caption_en']), trim($_POST['caption_ar']), $sort++, isset($_POST['active']) ? 1 : 0]); $added++;
    };
    $f = $_FILES['images'] ?? null;
    if ($f && is_array($f['name'])) {
      foreach ($f['name'] as $k => $nm) {
        if ($nm === '') continue;
        $n = save_image(['tmp_name' => $f['tmp_name'][$k], 'error' => $f['error'][$k], 'size' => $f['size'][$k]], 'b');
        if ($n) $add($n); else $failed++;
      }
    }
    foreach (preg_split('/\R/', (string)($_POST['image_urls'] ?? '')) as $u) {
      $u = trim($u); if ($u === '') continue;
      if (is_url($u)) $add($u); else $failed++;
    }
    if ($added > 0) $msg = sprintf(t('slides_added'), $added);
    if ($failed > 0) $err = t('upload_fail');
    elseif ($added === 0) $err = t('need_image');
  } elseif ($act === 'delete' && $id) {
    $q = db()->prepare('SELECT image FROM slides WHERE id=? AND slider_id=?'); $q->execute([$id, $sid]); delete_upload($q->fetchColumn());
    db()->prepare('DELETE FROM slides WHERE id=? AND slider_id=?')->execute([$id, $sid]); $msg = t('slide_deleted');
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE slides SET active=1-active WHERE id=? AND slider_id=?')->execute([$id, $sid]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM slides WHERE id=? AND slider_id=?'); $q->execute([(int)$_GET['edit'], $sid]); $edit = $q->fetch() ?: null; }
$q = db()->prepare('SELECT * FROM slides WHERE slider_id=? ORDER BY sort,id'); $q->execute([$sid]); $rows = $q->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
$curImg = (string)$cur('image');
?>
<p><a href="banners.php">&larr; <?= e(t('back_banners')) ?></a></p>
<h1 class="pt"><?= e(t('slides')) ?> — <?= e($slider['name']) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="slider" value="<?= $sid ?>"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_slide') : t('add_slide')) ?></h2>
<?php if ($edit): ?>
<label><?= e(t('slide_image')) ?><img id="psl" class="thumb-sm" style="width:100%;height:120px;object-fit:contain;background:#f5f5f5;margin:6px 0" src="<?= e(img_src($curImg, '../uploads/')) ?>" alt=""><input type="file" name="image" accept="image/*" data-preview="psl"></label>
<label><?= e(t('image_url')) ?><input name="image_url" dir="ltr" value="<?= is_url($curImg) ? e($curImg) : '' ?>" placeholder="https://"></label>
<?php else: ?>
<label><?= e(t('images_multi')) ?><input type="file" name="images[]" accept="image/*" multiple></label>
<label><?= e(t('image_urls')) ?><textarea name="image_urls" dir="ltr" rows="3" placeholder="https://example.com/banner1.jpg&#10;https://example.com/banner2.jpg"></textarea></label>
<?php endif; ?>
<div class="two"><label><?= e(t('caption_en')) ?><input name="caption_en" dir="<?= first_dir() ?>" value="<?= e($cur('caption_en')) ?>"></label>
<label><?= e(t('caption_ar')) ?><input name="caption_ar" dir="<?= second_dir() ?>" value="<?= e($cur('caption_ar')) ?>"></label></div>
<label><?= e(t('slide_link')) ?><input name="url" dir="ltr" value="<?= e($cur('url')) ?>" placeholder="https://"></label>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)$cur('sort', $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="slides.php?slider=<?= $sid ?>"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>

<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th></th><th><?= e(t('title')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td>
<td><img class="thumb-sm" style="width:84px;height:48px" src="<?= e(img_src($r['image'], '../uploads/')) ?>" alt=""></td><td><?= e(pick($r, 'caption')) ?> <span class="muted" dir="ltr"><?= e($r['url']) ?></span></td>
<td><div class="row"><a class="btn-sm" href="?slider=<?= $sid ?>&amp;edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="slider" value="<?= $sid ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_slide')) ?>"><?= csrf_field() ?><input type="hidden" name="slider" value="<?= $sid ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; if (!$rows): ?><tr><td colspan="4" class="muted"><?= e(t('no_slides')) ?></td></tr><?php endif; ?></table></div>
<?php require __DIR__ . '/_footer.php';
