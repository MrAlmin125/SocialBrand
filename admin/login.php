<?php
require __DIR__ . '/../includes/bootstrap.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (login_blocked()) { $err = t('locked'); }
  else {
    $id = trim($_POST['login'] ?? ''); $ph = preg_replace('/[^\d+]/', '', $id);
    $q = db()->prepare('SELECT * FROM admins WHERE username=? OR email=? OR (phone IS NOT NULL AND phone=?) LIMIT 1');
    $q->execute([$id, strtolower($id), $ph]); $u = $id !== '' ? $q->fetch() : false;
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
      login_ok(); session_regenerate_id(true); $_SESSION['admin'] = $u['id']; header('Location: index.php'); exit;
    }
    login_fail(); sleep(1); $err = t('bad_login');
  }
}
$S = settings();
?><!DOCTYPE html><html lang="<?= L() ?>" dir="<?= dir_attr() ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex"><link rel="icon" href="../favicon.php?v=<?= urlencode((string)(($S['favicon'] ?? '') !== '' ? $S['favicon'] : ($S['profile'] ?? ''))) ?>"><title><?= e(t('login')) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap">
<link rel="stylesheet" href="../<?= asset_v('css/admin.css') ?>"></head><body class="auth"><?= admin_pre() ?><main class="box center">
<div class="langsw"><?= lang_switch_html() ?></div>
<img class="avatar" src="<?= e(img_src($S['profile'] ?? '', '../uploads/')) ?>" alt="">
<div class="shopname"><?= e(pick($S, 'shop_name')) ?></div>
<?php if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<label><?= e(t('login_id')) ?><input name="login" required autocomplete="username"></label>
<label><?= e(t('password_label')) ?><input type="password" name="password" required inputmode="numeric" maxlength="6" autocomplete="current-password"></label>
<button class="btn-primary"><?= e(t('sign_in')) ?></button></form></main><?= footer_html() ?><script src="../<?= asset_v('js/admin.js') ?>"></script></body></html>
