<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = ''; $me = (int)$_SESSION['admin'];
$dup = function ($col, $val, $id) {                       // is this username/email/phone already used by another admin?
  if ($val === null) return false;
  $q = db()->prepare("SELECT COUNT(*) FROM admins WHERE `$col`=? AND id<>?"); $q->execute([$val, $id]); return $q->fetchColumn() > 0;
};
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $name = trim($_POST['name'] ?? '');
    $user = trim($_POST['username'] ?? ''); $user = $user === '' ? null : $user;
    $mail = strtolower(trim($_POST['email'] ?? '')); $mail = $mail === '' ? null : $mail;
    $ph = preg_replace('/[^\d+]/', '', trim($_POST['phone'] ?? '')); $ph = $ph === '' ? null : $ph;
    $pw = $_POST['password'] ?? '';
    if ($user === null && $mail === null && $ph === null) $err = t('need_identifier');
    elseif ($mail !== null && !filter_var($mail, FILTER_VALIDATE_EMAIL)) $err = t('email');
    elseif ((!$id || $pw !== '') && !valid_pin($pw)) $err = t('pin_invalid');
    elseif ($dup('username', $user, $id) || $dup('email', $mail, $id) || $dup('phone', $ph, $id)) $err = t('dup_admin');
    else {
      if ($id) {
        db()->prepare('UPDATE admins SET name=?,username=?,email=?,phone=? WHERE id=?')->execute([$name, $user, $mail, $ph, $id]);
        if ($pw !== '') db()->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
        $msg = t('admin_updated');
      } else {
        db()->prepare('INSERT INTO admins (name,username,email,phone,password_hash) VALUES (?,?,?,?,?)')->execute([$name, $user, $mail, $ph, password_hash($pw, PASSWORD_DEFAULT)]);
        $msg = t('admin_added');
      }
    }
  } elseif ($act === 'delete' && $id) {
    if ($id === $me) $err = t('cant_delete_self');
    elseif (db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() <= 1) $err = t('cant_delete_last');
    else { db()->prepare('DELETE FROM admins WHERE id=?')->execute([$id]); $msg = t('admin_deleted'); }
  }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM admins WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query('SELECT id,name,username,email,phone FROM admins ORDER BY id')->fetchAll();
require __DIR__ . '/_header.php';
?>
<h1 class="pt"><?= e(t('admins')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="panel" autocomplete="off"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('edit_admin') : t('add_admin')) ?></h2>
<label><?= e(t('full_name')) ?><input name="name" value="<?= e($edit['name'] ?? '') ?>"></label>
<div class="two"><label><?= e(t('username')) ?><input name="username" dir="ltr" value="<?= e($edit['username'] ?? '') ?>"></label>
<label><?= e(t('email')) ?><input type="email" name="email" dir="ltr" value="<?= e($edit['email'] ?? '') ?>"></label></div>
<div class="two"><label><?= e(t('phone')) ?><input name="phone" dir="ltr" inputmode="tel" value="<?= e($edit['phone'] ?? '') ?>" placeholder="+966..."></label>
<label><?= e(t('password_label')) ?> <span class="muted hint">(<?= e($edit ? t('leave_blank') : t('pin_hint')) ?>)</span><input type="password" name="password" inputmode="numeric" pattern="\d{4,6}" maxlength="6" autocomplete="new-password"<?= $edit ? '' : ' required' ?>></label></div>
<p class="muted" style="margin-bottom:10px"><?= e(t('login_id')) ?> · <?= e(t('pin_hint')) ?></p>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="admins.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>
<div class="panel tblwrap"><table class="tbl"><tr><th><?= e(t('full_name')) ?></th><th><?= e(t('username')) ?></th><th><?= e(t('email')) ?></th><th><?= e(t('phone')) ?></th><th></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['name']) ?> <?= (int)$r['id'] === $me ? '<span class="muted">' . e(t('you')) . '</span>' : '' ?></td><td dir="ltr"><?= e($r['username']) ?></td><td dir="ltr"><?= e($r['email']) ?></td><td dir="ltr"><?= e($r['phone']) ?></td>
<td><div class="row"><a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
<?php if ((int)$r['id'] !== $me): ?><form method="post" data-confirm="<?= e(t('confirm_del_admin')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form><?php endif; ?></div></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__ . '/_footer.php';
