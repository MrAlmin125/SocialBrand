<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = ''; $posOk = ['after_profile', 'before_links', 'before_products', 'before_map', 'bottom'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'settings') {
    foreach (['contact_enabled', 'notify_enabled'] as $k) set_setting($k, isset($_POST[$k]) ? '1' : '0');
    foreach (['contact_title_en', 'contact_title_ar'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
    $em = trim($_POST['notify_email'] ?? ''); set_setting('notify_email', filter_var($em, FILTER_VALIDATE_EMAIL) ? $em : '');
    settings(true); $msg = t('saved');
  } elseif ($act === 'read' && $id) { db()->prepare('UPDATE messages SET is_read=1-is_read WHERE id=?')->execute([$id]);
  } elseif ($act === 'readall') { db()->exec('UPDATE messages SET is_read=1');
  } elseif ($act === 'delete' && $id) { db()->prepare('DELETE FROM messages WHERE id=?')->execute([$id]); $msg = t('msg_deleted'); }
}
$pp = 15; $total = (int)db()->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$pages = max(1, (int)ceil($total / $pp)); $pg = max(1, min($pages, (int)($_GET['pg'] ?? 1))); $off = ($pg - 1) * $pp;
$rows = db()->query('SELECT * FROM messages ORDER BY id DESC LIMIT ' . $pp . ' OFFSET ' . $off)->fetchAll();
$s = settings();
require __DIR__ . '/_header.php';
$chk = function ($k) use ($s) { return flag_on($s, $k) ? ' checked' : ''; };
?>
<h1 class="pt"><?= e(t('messages')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="settings">
<label class="checkrow"><input type="checkbox" name="contact_enabled" value="1"<?= $chk('contact_enabled') ?>><?= e(t('contact_enabled')) ?></label>
<div class="two"><label><?= e(t('contact_title_en')) ?><input name="contact_title_en" dir="<?= first_dir() ?>" value="<?= e($s['contact_title_en'] ?? '') ?>"></label>
<label><?= e(t('contact_title_ar')) ?><input name="contact_title_ar" dir="<?= second_dir() ?>" value="<?= e($s['contact_title_ar'] ?? '') ?>"></label></div>
<p class="muted"><?= e(t('pl_note')) ?> <a href="layout.php"><?= e(t('page_layout')) ?></a></p>
<label class="checkrow"><input type="checkbox" name="notify_enabled" value="1"<?= $chk('notify_enabled') ?>><?= e(t('notify_enabled')) ?></label>
<label><?= e(t('notify_email')) ?><input type="email" name="notify_email" dir="ltr" value="<?= e($s['notify_email'] ?? '') ?>"></label>
<p class="muted"><?= e(t('notify_note')) ?></p>
<button class="btn-sm"><?= e(t('save')) ?></button></form>

<h2><?= e(t('msg_inbox')) ?> (<?= $total ?>)</h2>
<?php if ($total): ?><form method="post" style="margin-bottom:10px"><?= csrf_field() ?><button class="btn-sm gray" name="act" value="readall"><?= e(t('mark_all_read')) ?></button></form><?php endif; ?>
<?php foreach ($rows as $r): $ph = preg_replace('/[^\d+]/', '', $r['phone']); $wa = preg_replace('/\D/', '', $r['phone']); ?>
<div class="panel msgbox<?= $r['is_read'] ? '' : ' unread' ?>">
  <div class="meta"><b><?= e($r['name']) ?></b><?php if (!$r['is_read']): ?><span class="bdg"><?= e(t('msg_new')) ?></span><?php endif; ?><span class="muted" dir="ltr"><?= e($r['created_at']) ?></span>
    <?php if ($r['product_name'] !== ''): ?><span class="tag"><?= e(t('msg_product')) ?>: <?= e($r['product_name']) ?></span><?php endif; ?></div>
  <div class="muted" dir="ltr"><?= e($r['phone']) ?><?= $r['phone'] !== '' && $r['email'] !== '' ? ' · ' : '' ?><?= e($r['email']) ?></div>
  <div class="body"><?= e($r['message']) ?></div>
  <div class="row">
    <?php if ($wa !== ''): ?><a class="btn-sm" target="_blank" rel="noopener" href="https://wa.me/<?= e($wa) ?>"><?= e(t('reply_wa')) ?></a><a class="btn-sm gray" href="tel:<?= e($ph) ?>"><?= e(t('reply_call')) ?></a><?php endif; ?>
    <?php if ($r['email'] !== ''): ?><a class="btn-sm gray" href="mailto:<?= e($r['email']) ?>"><?= e(t('reply_mail')) ?></a><?php endif; ?>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm gray" name="act" value="read"><?= e($r['is_read'] ? t('mark_unread') : t('mark_read')) ?></button></form>
    <form method="post" data-confirm="<?= e(t('confirm_del_msg')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form>
  </div>
</div>
<?php endforeach; if (!$rows): ?><p class="muted"><?= e(t('no_messages')) ?></p><?php endif; ?>
<div class="pager"><?php if ($pg > 1): ?><a href="?pg=<?= $pg - 1 ?>">&laquo; <?= e(t('prev')) ?></a><?php endif; ?><span class="dots"><?= e(sprintf(t('page_x_of'), $pg, $pages)) ?></span><?php if ($pg < $pages): ?><a href="?pg=<?= $pg + 1 ?>"><?= e(t('next')) ?> &raquo;</a><?php endif; ?></div>
<?php require __DIR__ . '/_footer.php';
