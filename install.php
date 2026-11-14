<?php
require __DIR__ . '/includes/bootstrap.php';
$pdo = db();   // creates / upgrades all tables automatically
$msg = ''; $done = false;
$exists = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
if ($exists) {
  $msg = t('install_exists');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? ''); $p = $_POST['password'] ?? '';
  if ($u === '' || !valid_pin($p)) { $msg = t('pin_invalid'); }
  else {
    $pdo->prepare('INSERT INTO admins (username,password_hash) VALUES (?,?)')->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
    foreach (['shop_name_en' => 'Kaif Al Diyafa Coffee', 'shop_name_ar' => 'كيف الضيافة للقهوة', 'tagline_en' => 'Coffee . Attar . Dates', 'tagline_ar' => 'قهوة ، عطارة ، تمور',
              'cover' => 'cover-photo.jpg', 'profile' => 'profile.png', 'store_url' => 'https://example.com'] as $k => $v) set_setting($k, $v);
    if ($pdo->query('SELECT COUNT(*) FROM links')->fetchColumn() == 0) {
      $d = [['store','Order from our store','متجر كيف الضيافة للطلب','https://example.com'],['snapchat','Snapchat','سناب شات','username'],['tiktok','TikTok','تيك توك','username'],
            ['whatsapp','WhatsApp','واتساب','966500000000'],['phone','Call us','اتصل بنا','+966500000000'],['email','Email','بريد إلكتروني','info@example.com']];
      foreach ($d as $i => $r) $pdo->prepare('INSERT INTO links (type,title_en,title_ar,value,sort) VALUES (?,?,?,?,?)')->execute([$r[0], $r[1], $r[2], $r[3], $i + 1]);
    }
    if ($pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() == 0) {
      $reg = [25, 35, 45, 60]; $sale = [null, 30, null, 50];
      for ($i = 1; $i <= 4; $i++) $pdo->prepare('INSERT INTO products (name_en,name_ar,reg_price,sale_price,currency,url,sort,sale_end) VALUES (?,?,?,?,?,?,?,?)')->execute(["Product Name $i", "اسم المنتج $i", $reg[$i - 1], $sale[$i - 1], 'SAR', 'https://example.com', $i, $sale[$i - 1] ? date('Y-m-d H:i:s', strtotime('+14 days')) : null]);
      $cids = $pdo->query('SELECT id FROM categories ORDER BY sort,id')->fetchAll(PDO::FETCH_COLUMN);   // demo categories get the demo products
      if ($cids) { $k = 0; foreach ($pdo->query('SELECT id FROM products ORDER BY sort,id')->fetchAll(PDO::FETCH_COLUMN) as $pid) { $pdo->prepare('UPDATE products SET category_id=? WHERE id=?')->execute([$cids[$k % count($cids)], $pid]); $k++; } }
    }
    $done = true;
  }
}
?><!DOCTYPE html><html lang="<?= L() ?>" dir="<?= dir_attr() ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e(t('install_title')) ?></title><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap"><link rel="stylesheet" href="<?= asset_v('css/admin.css') ?>"></head><body class="auth"><?= admin_pre('') ?><main class="box">
<div class="langsw"><?= lang_switch_html() ?></div>
<h1><?= e(t('install_title')) ?></h1>
<?php if ($done): ?><p class="ok"><?= e(t('install_done')) ?> <a href="admin/login.php"><?= e(t('install_login')) ?></a></p>
<?php else: ?>
<?php if ($msg): ?><p class="err"><?= e($msg) ?></p><?php endif; ?>
<?php if (!$exists): ?>
<form method="post"><label><?= e(t('username')) ?><input name="username" required autocomplete="username"></label>
<label><?= e(t('password_label')) ?> <span class="muted">(<?= e(t('pin_hint')) ?>)</span><input type="password" name="password" required inputmode="numeric" pattern="\d{4,6}" maxlength="6" autocomplete="new-password"></label>
<button class="btn-primary"><?= e(t('install_btn')) ?></button></form>
<?php endif; endif; ?></main><?= footer_html() ?><script src="<?= asset_v('js/admin.js') ?>"></script></body></html>
