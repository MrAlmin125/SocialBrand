<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $old = '';
    if ($id) { $q = db()->prepare('SELECT image FROM products WHERE id=?'); $q->execute([$id]); $old = (string)$q->fetchColumn(); }
    $img = $old; $url = trim($_POST['image_url'] ?? '');
    if (!empty($_FILES['image']['name'])) {                       // 1) uploaded file wins
      $n = save_image($_FILES['image'], 'p');
      if ($n) { delete_upload($old); $img = $n; } else $err = t('upload_fail');
    } elseif ($url !== '') {                                      // 2) otherwise an image link
      if (is_url($url)) { if ($url !== $old) delete_upload($old); $img = $url; } else $err = t('bad_url');
    } elseif (is_url($old)) { $img = ''; }                        // link field cleared
    if (isset($_POST['remove_image'])) { delete_upload($old); $img = ''; }
    $num = function ($v) { $v = str_replace(',', '.', trim((string)$v)); return $v === '' ? null : (is_numeric($v) && (float)$v >= 0 ? round((float)$v, 2) : false); };
    $reg = $num($_POST['reg_price'] ?? ''); $sale = $num($_POST['sale_price'] ?? '');
    if ($reg === false || $sale === false) { $err = t('bad_price'); if ($reg === false) $reg = null; if ($sale === false) $sale = null; }
    if ($sale !== null && $reg === null) { $err = t('sale_needs_regular'); $sale = null; }
    elseif ($sale !== null && $sale >= $reg) { $err = t('sale_lower'); $sale = null; }
    $cur = isset(CURRENCIES[$_POST['currency'] ?? '']) ? $_POST['currency'] : '';
    $f = [trim($_POST['name_en']), trim($_POST['name_ar']), trim($_POST['price'] ?? ''), $reg, $sale, $cur, $img, trim($_POST['url']), trim($_POST['btn_en'] ?? ''), trim($_POST['btn_ar'] ?? ''), (int)$_POST['sort'], isset($_POST['active']) ? 1 : 0];
    $dt = function ($v) { $v = trim((string)$v); $t = $v === '' ? false : strtotime($v); return $t ? date('Y-m-d H:i:s', $t) : null; };
    $sst = $dt($_POST['sale_start'] ?? ''); $sen = $dt($_POST['sale_end'] ?? '');
    if ($sst !== null && $sen !== null && strtotime($sen) <= strtotime($sst)) { $err = t('bad_dates'); $sst = $sen = null; }
    $f[] = (int)($_POST['category_id'] ?? 0); $f[] = $sst; $f[] = $sen; $f[] = in_array((int)($_POST['countdown'] ?? 0), [0, 1, 2], true) ? (int)$_POST['countdown'] : 0;
    $f[] = in_array($_POST['buy_mode'] ?? '', ['link', 'whatsapp', 'form'], true) ? $_POST['buy_mode'] : '';
    if ($id) { $f[] = $id; db()->prepare('UPDATE products SET name_en=?,name_ar=?,price=?,reg_price=?,sale_price=?,currency=?,image=?,url=?,btn_en=?,btn_ar=?,sort=?,active=?,category_id=?,sale_start=?,sale_end=?,countdown=?,buy_mode=? WHERE id=?')->execute($f); if (!$err) $msg = t('product_updated'); }
    else { db()->prepare('INSERT INTO products (name_en,name_ar,price,reg_price,sale_price,currency,image,url,btn_en,btn_ar,sort,active,category_id,sale_start,sale_end,countdown,buy_mode) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute($f); if (!$err) $msg = t('product_added'); }
  } elseif ($act === 'currency') {
    set_setting('currency_default', isset(CURRENCIES[$_POST['currency_default'] ?? '']) ? $_POST['currency_default'] : 'SAR');
    set_setting('show_discount', isset($_POST['show_discount']) ? '1' : '0');
    settings(true); $msg = t('cur_saved');
  } elseif ($act === 'remove_image' && $id) {
    $q = db()->prepare('SELECT image FROM products WHERE id=?'); $q->execute([$id]); delete_upload($q->fetchColumn());
    db()->prepare("UPDATE products SET image='' WHERE id=?")->execute([$id]); $msg = t('image_removed');
  } elseif ($act === 'delete' && $id) {
    $q = db()->prepare('SELECT image FROM products WHERE id=?'); $q->execute([$id]); delete_upload($q->fetchColumn());
    db()->prepare('DELETE FROM products WHERE id=?')->execute([$id]); $msg = t('product_deleted');
  } elseif ($act === 'toggle' && $id) { db()->prepare('UPDATE products SET active=1-active WHERE id=?')->execute([$id]); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM products WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query('SELECT * FROM products ORDER BY sort,id')->fetchAll();
$maxSort = $rows ? max(array_column($rows, 'sort')) + 1 : 1;
$catRows = db()->query('SELECT * FROM categories ORDER BY sort,id')->fetchAll(); $s0 = settings(); $defCur = isset(CURRENCIES[$s0['currency_default'] ?? '']) ? $s0['currency_default'] : 'SAR';
require __DIR__ . '/_header.php';
$curImg = $edit['image'] ?? '';
?>
<h1 class="pt"><?= e(t('admin_products')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="currency">
<div class="two"><label><?= e(t('cur_default')) ?><select name="currency_default"><?php foreach (CURRENCIES as $code => $c): ?><option value="<?= $code ?>"<?= $defCur === $code ? ' selected' : '' ?>><?= e(currency_label($code)) ?></option><?php endforeach; ?></select></label>
<label class="checkrow" style="align-self:end"><input type="checkbox" name="show_discount" value="1"<?= flag_on($s0, 'show_discount') ? ' checked' : '' ?>><?= e(t('show_discount')) ?></label></div>
<button class="btn-sm"><?= e(t('save')) ?></button></form>
<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_product') : t('add_product')) ?></h2>
<div class="two"><label><?= e(t('name_en')) ?><input name="name_en" dir="<?= first_dir() ?>" value="<?= e($edit['name_en'] ?? '') ?>" required></label>
<label><?= e(t('name_ar')) ?><input name="name_ar" dir="<?= second_dir() ?>" value="<?= e($edit['name_ar'] ?? '') ?>" required></label></div>
<h3 class="sub"><?= e(t('sec_prices')) ?></h3>
<div class="three"><label><?= e(t('regular_price')) ?><input type="number" step="0.01" min="0" inputmode="decimal" name="reg_price" dir="ltr" value="<?= isset($edit['reg_price']) && $edit['reg_price'] !== null ? e(rtrim(rtrim(number_format((float)$edit['reg_price'], 2, '.', ''), '0'), '.')) : '' ?>" placeholder="30"></label>
<label><?= e(t('sale_price')) ?><input type="number" step="0.01" min="0" inputmode="decimal" name="sale_price" dir="ltr" value="<?= isset($edit['sale_price']) && $edit['sale_price'] !== null ? e(rtrim(rtrim(number_format((float)$edit['sale_price'], 2, '.', ''), '0'), '.')) : '' ?>" placeholder="25"></label>
<label><?= e(t('currency')) ?><select name="currency"><option value=""><?= e(t('use_default')) ?> (<?= e($defCur) ?>)</option><?php foreach (CURRENCIES as $code => $c): ?><option value="<?= $code ?>"<?= ($edit['currency'] ?? '') === $code ? ' selected' : '' ?>><?= e(currency_label($code)) ?></option><?php endforeach; ?></select></label></div>
<div class="two"><label><?= e(t('category')) ?><select name="category_id"><option value="0"><?= e(t('no_category')) ?></option><?php foreach ($catRows as $c): ?><option value="<?= (int)$c['id'] ?>"<?= (int)($edit['category_id'] ?? 0) === (int)$c['id'] ? ' selected' : '' ?>><?= e(pick($c, 'name')) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('buy_mode')) ?><select name="buy_mode"><?php foreach (['' => 'bm_default', 'link' => 'bm_link', 'whatsapp' => 'bm_wa', 'form' => 'bm_form'] as $bk => $bl): ?><option value="<?= $bk ?>"<?= ($edit['buy_mode'] ?? '') === $bk ? ' selected' : '' ?>><?= e(t($bl)) ?></option><?php endforeach; ?></select></label></div>
<div class="two"><label><?= e(t('sale_start')) ?><input type="datetime-local" name="sale_start" dir="ltr" value="<?= !empty($edit['sale_start']) ? e(date('Y-m-d\TH:i', strtotime($edit['sale_start']))) : '' ?>"></label>
<label><?= e(t('sale_end')) ?><input type="datetime-local" name="sale_end" dir="ltr" value="<?= !empty($edit['sale_end']) ? e(date('Y-m-d\TH:i', strtotime($edit['sale_end']))) : '' ?>"></label></div>
<label><?= e(t('cd_mode')) ?><select name="countdown"><?php foreach ([0 => 'cdm_default', 1 => 'cdm_show', 2 => 'cdm_hide'] as $ck => $cl): ?><option value="<?= $ck ?>"<?= (int)($edit['countdown'] ?? 0) === $ck ? ' selected' : '' ?>><?= e(t($cl)) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('price_text')) ?><input name="price" value="<?= e($edit['price'] ?? '') ?>"></label>
<label><?= e(t('product_url')) ?><input name="url" dir="ltr" value="<?= e($edit['url'] ?? '') ?>" placeholder="https://" required></label>
<div class="two"><label><?= e(t('pbtn_en')) ?><input name="btn_en" dir="<?= first_dir() ?>" maxlength="60" value="<?= e($edit['btn_en'] ?? '') ?>" placeholder="Order now"></label>
<label><?= e(t('pbtn_ar')) ?><input name="btn_ar" dir="<?= second_dir() ?>" maxlength="60" value="<?= e($edit['btn_ar'] ?? '') ?>" placeholder="اطلب الآن"></label></div>
<p class="muted" style="margin:-4px 0 12px"><?= e(t('pbtn_hint')) ?></p>
<label><?= e(t('image')) ?><img id="pi" class="thumb-sm" style="margin:6px 0;<?= $curImg === '' ? 'display:none' : '' ?>" src="<?= $curImg === '' ? '' : e(img_src($curImg, '../uploads/')) ?>" alt=""><input type="file" name="image" accept="image/*" data-preview="pi"></label>
<label><?= e(t('image_url')) ?><input name="image_url" dir="ltr" value="<?= is_url($curImg) ? e($curImg) : '' ?>" placeholder="https://"></label>
<?php if ($curImg !== ''): ?><p style="margin-bottom:12px"><button type="submit" form="rmimg" class="btn-sm del"><?= e(t('remove_image')) ?></button></p><?php endif; ?>
<div class="two"><label><?= e(t('order')) ?><input type="number" name="sort" value="<?= (int)($edit['sort'] ?? $maxSort) ?>"></label>
<label><br><input type="checkbox" name="active" value="1"<?= !$edit || $edit['active'] ? ' checked' : '' ?>><?= e(t('visible')) ?></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="products.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>
<?php if ($curImg !== ''): ?><form method="post" id="rmimg"><?= csrf_field() ?><input type="hidden" name="act" value="remove_image"><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"></form><?php endif; ?>
<div class="panel tblwrap"><table class="tbl"><tr><th>#</th><th></th><th><?= e(t('product')) ?></th><th><?= e(t('price')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr class="<?= $r['active'] ? '' : 'off' ?>"><td><?= (int)$r['sort'] ?></td>
<td><?php if ($r['image'] !== ''): ?><img class="thumb-sm" src="<?= e(img_src($r['image'], '../uploads/')) ?>" alt=""><?php endif; ?></td><td><?= e(pick($r, 'name')) ?></td><td><?= e(product_price_text($r, $s0)) ?></td>
<td><div class="row"><a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="toggle"><?= e($r['active'] ? t('hide') : t('show')) ?></button></form>
<form method="post" data-confirm="<?= e(t('confirm_del_product')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
