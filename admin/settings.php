<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = ''; $s = settings();
if (isset($_GET['ok'])) $msg = t('saved');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (isset($_POST['save'])) {
    $coords = trim($_POST['map_coords'] ?? '');
    if ($coords !== '' && !parse_coords($coords)) $err = t('bad_coords');
    $l1 = lang_valid($_POST['lang1'] ?? '') ? $_POST['lang1'] : 'en'; $l2 = lang_valid($_POST['lang2'] ?? '') ? $_POST['lang2'] : 'ar';
    if ($l2 === $l1) $l2 = $l1 === 'ar' ? 'en' : 'ar';
    set_setting('lang1', $l1); set_setting('lang2', $l2); set_setting('second_lang', $l2);
    set_setting('lang_default', ($_POST['lang_default'] ?? '') === 'first' ? 'first' : 'second');
    foreach (['map_kicker_en', 'map_kicker_ar'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
    $raw = trim($_POST['map_embed'] ?? ''); $emb = '';
    if ($raw !== '') {
      if (preg_match('/src=["\']([^"\']+)["\']/i', $raw, $m)) $raw = html_entity_decode($m[1]);
      if (preg_match('#^https://(www\.google\.com/maps|maps\.google\.com/maps)#i', $raw)) $emb = $raw; else $err = t('bad_embed');
    }
    if (!$err) set_setting('map_embed', $emb);
    foreach (['shop_name_en', 'shop_name_ar', 'tagline_en', 'tagline_ar', 'map_name_en', 'map_name_ar', 'map_link', 'store_url'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
    set_setting('links_layout', ($_POST['links_layout'] ?? '') === 'grid' ? 'grid' : 'list');
    if (!$err) { $c = parse_coords($coords); set_setting('map_coords', $c ? $c[0] . ',' . $c[1] : ''); }
    foreach (['cover', 'profile', 'favicon', 'og_image'] as $k) {
      if (!empty($_FILES[$k]['name'])) {
        $n = save_image($_FILES[$k], $k);
        if ($n) { delete_upload($s[$k] ?? ''); set_setting($k, $n); } else $err = t('upload_fail');
      }
    }
    if (!$err) { header('Location: settings.php?ok=1'); exit; }   // reload so the new language pair applies to this page too
  }
  if (isset($_POST['pw'])) {
    $q = db()->prepare('SELECT * FROM admins WHERE id=?'); $q->execute([$_SESSION['admin']]); $u = $q->fetch();
    if (!password_verify($_POST['old'] ?? '', $u['password_hash'])) $err = t('pw_wrong');
    elseif (!valid_pin($_POST['new'] ?? '')) $err = t('pin_invalid');
    else { db()->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $u['id']]); $msg = t('pw_changed'); }
  }
  $s = settings(true);
}
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('shop_info')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<div class="panel"><h3 class="sub"><?= e(t('site_langs')) ?></h3>
<?php $lopt = function ($sel) { $h = ''; foreach (LANGS as $c => $n) $h .= '<option value="' . $c . '"' . ($sel === $c ? ' selected' : '') . '>' . e($n['label'] . ' — ' . (L() === 'ar' ? $n['ar'] : $n['en'])) . '</option>'; return $h; }; ?>
<div class="two"><label><?= e(t('lang1_label')) ?><select name="lang1"><?= $lopt(first_lang()) ?></select></label>
<label><?= e(t('lang2_label')) ?><select name="lang2"><?= $lopt(second_lang()) ?></select></label></div>
<label><?= e(t('default_lang_label')) ?><select name="lang_default"><option value="first"<?= ($s['lang_default'] ?? 'second') === 'first' || ($s['lang_default'] ?? '') === 'en' ? ' selected' : '' ?>><?= e(t('dl_first')) ?>: <?= e(LANGS[first_lang()]['label']) ?></option><option value="second"<?= ($s['lang_default'] ?? 'second') !== 'first' && ($s['lang_default'] ?? '') !== 'en' ? ' selected' : '' ?>><?= e(t('dl_second')) ?>: <?= e(LANGS[second_lang()]['label']) ?></option></select></label>
<p class="muted"><?= e(t('lang_note')) ?></p></div>
<div class="panel">
<div class="two"><label><?= e(t('shop_name_en')) ?><input name="shop_name_en" dir="<?= first_dir() ?>" value="<?= e($s['shop_name_en'] ?? '') ?>"></label>
<label><?= e(t('shop_name_ar')) ?><input name="shop_name_ar" dir="<?= second_dir() ?>" value="<?= e($s['shop_name_ar'] ?? '') ?>"></label></div>
<div class="two"><label><?= e(t('tagline_en')) ?><input name="tagline_en" dir="<?= first_dir() ?>" value="<?= e($s['tagline_en'] ?? '') ?>"></label>
<label><?= e(t('tagline_ar')) ?><input name="tagline_ar" dir="<?= second_dir() ?>" value="<?= e($s['tagline_ar'] ?? '') ?>"></label></div>
<label><?= e(t('cover')) ?><img id="pc" class="thumb-sm" style="width:100%;height:110px;object-fit:contain;background:#f5f5f5;margin:6px 0" src="<?= e(img_src($s['cover'] ?? '', '../uploads/')) ?>" alt=""><input type="file" name="cover" accept="image/*" data-preview="pc"></label>
<p class="muted" style="margin:-6px 0 12px"><a href="covers.php"><?= e(t('cover_images')) ?></a></p>
<label><?= e(t('profile')) ?><img id="pp" class="thumb-sm" style="width:80px;height:80px;border-radius:50%;margin:6px 0" src="<?= e(img_src($s['profile'] ?? '', '../uploads/')) ?>" alt=""><input type="file" name="profile" accept="image/*" data-preview="pp"></label>
<label><?= e(t('favicon')) ?> <span class="muted hint">— <?= e(t('fav_hint')) ?></span><img id="pf" class="icon-sm" style="margin:6px 0;<?= empty($s['favicon']) ? 'display:none' : '' ?>" src="<?= empty($s['favicon']) ? '' : e(img_src($s['favicon'], '../uploads/')) ?>" alt=""><input type="file" name="favicon" accept="image/png,image/jpeg,image/webp,image/x-icon,.ico" data-preview="pf"></label>
<label><?= e(t('og_image')) ?> <span class="muted hint">— <?= e(t('og_hint')) ?></span><img id="po" class="thumb-sm" style="width:100%;height:110px;object-fit:cover;margin:6px 0;<?= empty($s['og_image']) ? 'display:none' : '' ?>" src="<?= empty($s['og_image']) ? '' : e(img_src($s['og_image'], '../uploads/')) ?>" alt=""><input type="file" name="og_image" accept="image/*" data-preview="po"></label>
<label><?= e(t('links_layout')) ?><select name="links_layout"><option value="list"<?= ($s['links_layout'] ?? 'list') !== 'grid' ? ' selected' : '' ?>><?= e(t('layout_list')) ?></option><option value="grid"<?= ($s['links_layout'] ?? '') === 'grid' ? ' selected' : '' ?>><?= e(t('layout_grid')) ?></option></select></label>
<label><?= e(t('store_url')) ?><input name="store_url" dir="ltr" value="<?= e($s['store_url'] ?? '') ?>" placeholder="https://"></label>
<label><?= e(t('map_coords')) ?> <span class="muted hint">— <?= e(t('map_coords_hint')) ?></span><input name="map_coords" dir="ltr" value="<?= e($s['map_coords'] ?? '') ?>" placeholder="25.8694, 43.4973"></label>
<div class="two"><label><?= e(t('map_name_en')) ?><input name="map_name_en" dir="<?= first_dir() ?>" value="<?= e($s['map_name_en'] ?? '') ?>"></label>
<label><?= e(t('map_name_ar')) ?><input name="map_name_ar" dir="<?= second_dir() ?>" value="<?= e($s['map_name_ar'] ?? '') ?>"></label></div>
<label><?= e(t('map_embed')) ?> <span class="muted hint">— <?= e(t('map_embed_hint')) ?></span><textarea name="map_embed" dir="ltr" rows="3" placeholder='<iframe src="https://www.google.com/maps/embed?pb=..."></iframe>'><?= e($s['map_embed'] ?? '') ?></textarea></label>
<div class="two"><label><?= e(t('map_kicker_en')) ?><input name="map_kicker_en" dir="<?= first_dir() ?>" value="<?= e($s['map_kicker_en'] ?? '') ?>"></label>
<label><?= e(t('map_kicker_ar')) ?><input name="map_kicker_ar" dir="<?= second_dir() ?>" value="<?= e($s['map_kicker_ar'] ?? '') ?>"></label></div>
<label><?= e(t('map_link')) ?><input name="map_link" dir="ltr" value="<?= e($s['map_link'] ?? '') ?>" placeholder="https://maps.app.goo.gl/..."></label>
<button class="btn-primary" name="save" value="1"><?= e(t('save')) ?></button></div></form>
<h2><?= e(t('change_pw')) ?></h2>
<form method="post" class="panel"><?= csrf_field() ?>
<label><?= e(t('current_pw')) ?><input type="password" name="old" required inputmode="numeric" maxlength="6" autocomplete="current-password"></label>
<label><?= e(t('new_pw')) ?><input type="password" name="new" required inputmode="numeric" pattern="\d{4,6}" maxlength="6" autocomplete="new-password"></label>
<button class="btn-primary" name="pw" value="1"><?= e(t('change')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
