<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$msg = ''; $s = settings();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  foreach (['headline_scroll', 'preloader_enabled', 'admin_preloader_enabled', 'popup_enabled', 'buy_enabled', 'catalog_search', 'pwa_enabled', 'pwa_install', 'anim_enabled', 'countdown_default'] as $k) set_setting($k, isset($_POST[$k]) ? '1' : '0');
  foreach (['btn_bg' => '#A47735', 'btn_text' => '#FFFFFF', 'headline_bg' => '#A47735', 'headline_fg' => '#FFFFFF', 'buy_bg' => '#A47735', 'buy_fg' => '#FFFFFF', 'pwa_color' => '#A47735'] as $k => $d) set_setting($k, hex_or($_POST[$k] ?? '', $d));
  set_setting('btn_mode', ($_POST['btn_mode'] ?? '') === 'custom' ? 'custom' : 'brand');
  set_setting('popup_freq', in_array($_POST['popup_freq'] ?? '', ['always', 'session', 'day', 'once'], true) ? $_POST['popup_freq'] : 'day');
  set_setting('popup_delay', (string)max(0, min(30, (int)($_POST['popup_delay'] ?? 1))));
  foreach (['popup_title_en', 'popup_title_ar', 'popup_text_en', 'popup_text_ar', 'popup_btn_en', 'popup_btn_ar', 'popup_btn_url', 'buy_text_en', 'buy_text_ar', 'wa_msg_en', 'wa_msg_ar', 'pwa_short_en', 'pwa_short_ar'] as $k) set_setting($k, trim($_POST[$k] ?? ''));
  foreach (['cover', 'logo', 'name', 'tagline'] as $ak) set_setting('anim_' . $ak, in_array($_POST['anim_' . $ak] ?? '', ANIMS, true) ? $_POST['anim_' . $ak] : 'none');
  foreach (['cover', 'logo', 'name', 'tagline'] as $ak) set_setting('anim_repeat_' . $ak, isset($_POST['anim_repeat_' . $ak]) ? '1' : '0');
  set_setting('buy_mode', in_array($_POST['buy_mode'] ?? '', ['link', 'whatsapp', 'form'], true) ? $_POST['buy_mode'] : 'link');
  set_setting('wa_number', preg_replace('/\D/', '', (string)($_POST['wa_number'] ?? '')));
  $s = settings(true); $msg = t('appearance_saved');
}
require __DIR__ . '/_header.php';
$chk = function ($k) use ($s) { return flag_on($s, $k) ? ' checked' : ''; };
$v = function ($k) use ($s) { return e($s[$k] ?? ''); };
?>
<h1 class="pt"><?= e(t('appearance')) ?></h1>
<?php if ($msg): ?><p class="ok"><?= e($msg) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>

<h2><?= e(t('sec_buttons')) ?></h2>
<div class="panel">
<label><?= e(t('btn_mode')) ?><select name="btn_mode"><option value="brand"<?= ($s['btn_mode'] ?? 'brand') !== 'custom' ? ' selected' : '' ?>><?= e(t('btn_mode_brand')) ?></option><option value="custom"<?= ($s['btn_mode'] ?? '') === 'custom' ? ' selected' : '' ?>><?= e(t('btn_mode_custom')) ?></option></select></label>
<div class="two"><label><?= e(t('btn_bg')) ?><input type="color" name="btn_bg" value="<?= e(hex_or($s['btn_bg'] ?? '', '#A47735')) ?>"></label>
<label><?= e(t('btn_text')) ?><input type="color" name="btn_text" value="<?= e(hex_or($s['btn_text'] ?? '', '#FFFFFF')) ?>"></label></div></div>

<h2><?= e(t('sec_buy')) ?></h2>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="buy_enabled" value="1"<?= $chk('buy_enabled') ?>><?= e(t('buy_enabled')) ?></label>
<div class="two"><label><?= e(t('buy_text_en')) ?><input name="buy_text_en" dir="<?= first_dir() ?>" value="<?= $v('buy_text_en') ?>" placeholder="Buy now"></label>
<label><?= e(t('buy_text_ar')) ?><input name="buy_text_ar" dir="<?= second_dir() ?>" value="<?= $v('buy_text_ar') ?>" placeholder="اشتري الآن"></label></div>
<div class="two"><label><?= e(t('buy_bg')) ?><input type="color" name="buy_bg" value="<?= e(hex_or($s['buy_bg'] ?? '', '#A47735')) ?>"></label>
<label><?= e(t('buy_fg')) ?><input type="color" name="buy_fg" value="<?= e(hex_or($s['buy_fg'] ?? '', '#FFFFFF')) ?>"></label></div></div>

<h2><?= e(t('sec_orders')) ?></h2>
<div class="panel">
<label><?= e(t('buy_mode_default')) ?><select name="buy_mode"><?php foreach (['link' => 'bm_link', 'whatsapp' => 'bm_wa', 'form' => 'bm_form'] as $bk => $bl): ?><option value="<?= $bk ?>"<?= ($s['buy_mode'] ?? 'link') === $bk ? ' selected' : '' ?>><?= e(t($bl)) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('wa_number')) ?><input name="wa_number" dir="ltr" inputmode="numeric" value="<?= $v('wa_number') ?>" placeholder="9665XXXXXXXX"></label>
<div class="two"><label><?= e(t('wa_msg_en')) ?><input name="wa_msg_en" dir="<?= first_dir() ?>" value="<?= $v('wa_msg_en') ?>"></label>
<label><?= e(t('wa_msg_ar')) ?><input name="wa_msg_ar" dir="<?= second_dir() ?>" value="<?= $v('wa_msg_ar') ?>"></label></div>
<p class="muted"><?= e(t('wa_msg_hint')) ?></p></div>

<h2><?= e(t('sec_anim')) ?></h2>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="anim_enabled" value="1"<?= $chk('anim_enabled') ?>><?= e(t('anim_enabled')) ?></label>
<?php foreach (['cover', 'logo', 'name', 'tagline'] as $ak): ?><div class="two"><label><?= e(t('anim_' . $ak)) ?><select name="anim_<?= $ak ?>"><?php foreach (ANIMS as $av): ?><option value="<?= $av ?>"<?= ($s['anim_' . $ak] ?? 'none') === $av ? ' selected' : '' ?>><?= e(t('an_' . str_replace('-', '_', $av))) ?></option><?php endforeach; ?></select></label>
<label class="checkrow" style="align-self:end"><input type="checkbox" name="anim_repeat_<?= $ak ?>" value="1"<?= $chk('anim_repeat_' . $ak) ?>><?= e(t('anim_repeat')) ?></label></div><?php endforeach; ?></div>

<h2><?= e(t('sec_catalog')) ?></h2>
<div class="panel"><label class="checkrow"><input type="checkbox" name="countdown_default" value="1"<?= $chk('countdown_default') ?>><?= e(t('countdown_default')) ?></label>
<label class="checkrow" style="margin:0"><input type="checkbox" name="catalog_search" value="1"<?= $chk('catalog_search') ?>><?= e(t('catalog_search')) ?></label></div>

<h2><?= e(t('sec_pwa')) ?></h2>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="pwa_enabled" value="1"<?= $chk('pwa_enabled') ?>><?= e(t('pwa_enabled')) ?></label>
<label class="checkrow"><input type="checkbox" name="pwa_install" value="1"<?= $chk('pwa_install') ?>><?= e(t('pwa_install')) ?></label>
<label><?= e(t('pwa_color')) ?><input type="color" name="pwa_color" value="<?= e(hex_or($s['pwa_color'] ?? '', '#A47735')) ?>"></label>
<div class="two"><label><?= e(t('pwa_short_en')) ?><input name="pwa_short_en" dir="<?= first_dir() ?>" maxlength="20" value="<?= $v('pwa_short_en') ?>"></label>
<label><?= e(t('pwa_short_ar')) ?><input name="pwa_short_ar" dir="<?= second_dir() ?>" maxlength="20" value="<?= $v('pwa_short_ar') ?>"></label></div></div>

<h2><?= e(t('sec_topbar')) ?></h2>
<div class="panel">
<div class="two"><label><?= e(t('headline_bg')) ?><input type="color" name="headline_bg" value="<?= e(hex_or($s['headline_bg'] ?? '', '#A47735')) ?>"></label>
<label><?= e(t('headline_fg')) ?><input type="color" name="headline_fg" value="<?= e(hex_or($s['headline_fg'] ?? '', '#FFFFFF')) ?>"></label></div>
<label class="checkrow"><input type="checkbox" name="headline_scroll" value="1"<?= $chk('headline_scroll') ?>><?= e(t('headline_scroll')) ?></label>
<p class="muted"><?= e(t('hd_intro')) ?></p></div>

<h2><?= e(t('sec_preloader')) ?></h2>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="preloader_enabled" value="1"<?= $chk('preloader_enabled') ?>><?= e(t('preloader_enabled')) ?></label>
<label class="checkrow" style="margin:0"><input type="checkbox" name="admin_preloader_enabled" value="1"<?= $chk('admin_preloader_enabled') ?>><?= e(t('admin_preloader')) ?></label></div>

<h2><?= e(t('sec_popup')) ?></h2>
<div class="panel">
<label class="checkrow"><input type="checkbox" name="popup_enabled" value="1"<?= $chk('popup_enabled') ?>><?= e(t('popup_enabled')) ?></label>
<div class="two"><label><?= e(t('popup_title_en')) ?><input name="popup_title_en" dir="<?= first_dir() ?>" value="<?= $v('popup_title_en') ?>"></label>
<label><?= e(t('popup_title_ar')) ?><input name="popup_title_ar" dir="<?= second_dir() ?>" value="<?= $v('popup_title_ar') ?>"></label></div>
<div class="two"><label><?= e(t('popup_text_en')) ?><input name="popup_text_en" dir="<?= first_dir() ?>" value="<?= $v('popup_text_en') ?>"></label>
<label><?= e(t('popup_text_ar')) ?><input name="popup_text_ar" dir="<?= second_dir() ?>" value="<?= $v('popup_text_ar') ?>"></label></div>
<div class="two"><label><?= e(t('popup_btn_en')) ?><input name="popup_btn_en" dir="<?= first_dir() ?>" value="<?= $v('popup_btn_en') ?>"></label>
<label><?= e(t('popup_btn_ar')) ?><input name="popup_btn_ar" dir="<?= second_dir() ?>" value="<?= $v('popup_btn_ar') ?>"></label></div>
<label><?= e(t('popup_btn_url')) ?><input name="popup_btn_url" dir="ltr" value="<?= $v('popup_btn_url') ?>" placeholder="https://"></label>
<div class="two"><label><?= e(t('popup_freq')) ?><select name="popup_freq"><?php foreach (['always', 'session', 'day', 'once'] as $f): ?><option value="<?= $f ?>"<?= ($s['popup_freq'] ?? 'day') === $f ? ' selected' : '' ?>><?= e(t('pf_' . $f)) ?></option><?php endforeach; ?></select></label>
<label><?= e(t('popup_delay')) ?><input type="number" name="popup_delay" min="0" max="30" value="<?= e($s['popup_delay'] ?? '1') ?>"></label></div></div>

<button class="btn-primary"><?= e(t('save')) ?></button></form>
<?php require __DIR__ . '/_footer.php';
