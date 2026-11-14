<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
  if ($act === 'save') {
    $src = clean_tag($_POST['source'] ?? ''); $med = clean_tag($_POST['medium'] ?? ''); $cam = clean_tag($_POST['campaign'] ?? '') ?: 'general';
    $name = trim($_POST['name'] ?? ''); if ($name === '') $name = $src;
    if ($src === '') $err = t('tl_need_source');
    elseif ($id) { db()->prepare('UPDATE tracking_links SET name=?,source=?,medium=?,campaign=? WHERE id=?')->execute([$name, $src, $med, $cam, $id]); $msg = t('tl_saved'); }
    else { db()->prepare('INSERT INTO tracking_links (name,source,medium,campaign) VALUES (?,?,?,?)')->execute([$name, $src, $med, $cam]); $msg = t('tl_saved'); }
  } elseif ($act === 'ipset') { set_setting('store_ip', isset($_POST['store_ip']) ? '1' : '0'); settings(true); $msg = t('ip_saved');
  } elseif ($act === 'delete' && $id) { db()->prepare('DELETE FROM tracking_links WHERE id=?')->execute([$id]); $msg = t('tl_deleted'); }
}
$edit = null;
if (isset($_GET['edit'])) { $q = db()->prepare('SELECT * FROM tracking_links WHERE id=?'); $q->execute([(int)$_GET['edit']]); $edit = $q->fetch() ?: null; }
$rows = db()->query('SELECT * FROM tracking_links ORDER BY id')->fetchAll();
$cnt = db()->prepare('SELECT COUNT(*) FROM visits WHERE utm_source=? AND utm_medium=? AND utm_campaign=?');
$site = site_url();
$mediums = ['social' => t('med_social'), 'qr' => t('med_qr'), 'message' => t('med_message'), 'email' => t('med_email'), 'referral' => t('med_referral'), 'banner' => t('med_banner'), 'other' => t('med_other')];
$quick = [['QR code', 'qr', 'qr'], ['WhatsApp', 'whatsapp', 'social'], ['Facebook', 'facebook', 'social'], ['Instagram', 'instagram', 'social'], ['TikTok', 'tiktok', 'social'], ['Snapchat', 'snapchat', 'social'], ['X (Twitter)', 'x', 'social'], ['Telegram', 'telegram', 'social'], ['Email', 'email', 'email']];
require __DIR__ . '/_header.php';
$cur = function ($k, $d = '') use ($edit) { return $edit[$k] ?? $d; };
?>
<h1 class="pt"><?= e(t('tracking')) ?></h1>
<p class="note" style="font-size:14px"><?= e(t('utm_intro')) ?></p>
<p class="note"><?= e(t('utm_why')) ?></p>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="act" value="ipset">
<label class="checkrow"><input type="checkbox" name="store_ip" value="1"<?= flag_on(settings(), 'store_ip') ? ' checked' : '' ?>><?= e(t('store_ip')) ?></label>
<p class="muted"><?= e(t('ip_note')) ?></p><button class="btn-sm"><?= e(t('save')) ?></button></form>
<form method="post" class="panel" id="tlform" autocomplete="off"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int)$cur('id', 0) ?>">
<h2 style="margin-top:0"><?= e($edit ? t('tl_edit') : t('tl_add')) ?></h2>
<?php if (!$edit): ?><p class="muted" style="margin-bottom:6px"><?= e(t('tl_quick')) ?></p>
<div class="row" style="margin-bottom:12px"><?php foreach ($quick as $qk): ?><button type="button" class="btn-sm gray quick" data-name="<?= e($qk[0]) ?>" data-source="<?= e($qk[1]) ?>" data-medium="<?= e($qk[2]) ?>"><?= e($qk[0]) ?></button><?php endforeach; ?></div><?php endif; ?>
<label><?= e(t('tl_name')) ?><input name="name" id="tl_name" maxlength="100" value="<?= e($cur('name')) ?>"></label>
<div class="three"><label><?= e(t('utm_source_name')) ?><input name="source" id="tl_source" dir="ltr" maxlength="60" pattern="[A-Za-z0-9_.\-]{1,60}" required value="<?= e($cur('source')) ?>"></label>
<label><?= e(t('medium')) ?><select name="medium" id="tl_medium"><?php foreach ($mediums as $k => $l): ?><option value="<?= e($k) ?>"<?= $cur('medium', 'social') === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('campaign_name')) ?><input name="campaign" dir="ltr" maxlength="60" pattern="[A-Za-z0-9_.\-]{1,60}" value="<?= e($cur('campaign', 'general')) ?>"></label></div>
<div class="row"><button class="btn-sm"><?= e($edit ? t('save_edit') : t('add')) ?></button><?php if ($edit): ?><a class="btn-sm gray" href="tracking.php"><?= e(t('cancel')) ?></a><?php endif; ?></div></form>

<div class="qrgrid" id="qrlist" data-download="<?= e(t('qr_download')) ?>" data-copy="<?= e(t('qr_copy')) ?>" data-copied="<?= e(t('qr_copied')) ?>" data-fail="<?= e(t('qr_fail')) ?>">
<?php foreach ($rows as $r):
  $url = $site . '?utm_source=' . rawurlencode($r['source']) . '&utm_medium=' . rawurlencode($r['medium']) . '&utm_campaign=' . rawurlencode($r['campaign']);
  $cnt->execute([$r['source'], $r['medium'], $r['campaign']]); $n = (int)$cnt->fetchColumn(); ?>
  <div class="qrcard" data-url="<?= e($url) ?>" data-file="<?= e($r['source']) ?>">
    <h3><?= e($r['name']) ?> <span class="tag"><?= e(t('tl_visits')) ?>: <?= $n ?></span></h3>
    <div class="qr"></div>
    <input readonly value="<?= e($url) ?>">
    <div class="row"><button type="button" class="btn-sm dl"><?= e(t('qr_download')) ?></button><button type="button" class="btn-sm gray cp"><?= e(t('qr_copy')) ?></button>
      <a class="btn-sm" href="?edit=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
      <form method="post" data-confirm="<?= e(t('tl_confirm_del')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm del" name="act" value="delete"><?= e(t('delete')) ?></button></form></div>
  </div>
<?php endforeach; if (!$rows): ?><p class="muted"><?= e(t('tl_none')) ?></p><?php endif; ?>
</div>
<p class="note"><?= e(t('src_note')) ?></p>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="../js/tracking.js"></script>
<?php require __DIR__ . '/_footer.php';
