<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$page = basename($_SERVER['SCRIPT_NAME'], '.php');
if ($page === 'slides') $page = 'banners';
$S = settings(); $bc = badge_counts();
$shop = pick($S, 'shop_name');
$logo = e(img_src($S['profile'] ?? '', '../uploads/'));
function nav_item($file, $label, $page, $icon, $badge = 0) { return '<a href="' . $file . '.php"' . ($page === $file ? ' class="on" aria-current="page"' : '') . '><svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#n-' . $icon . '"/></svg><span>' . e($label) . '</span>' . ($badge > 0 ? '<em class="bdg">' . (int)$badge . '</em>' : '') . '</a>'; }
?><!DOCTYPE html><html lang="<?= L() ?>" dir="<?= dir_attr() ?>"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex"><script>document.documentElement.className+=" js"</script><link rel="icon" href="../favicon.php?v=<?= urlencode((string)(($S['favicon'] ?? '') !== '' ? $S['favicon'] : ($S['profile'] ?? ''))) ?>">
<title><?= e(t('admin_panel')) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap">
<link rel="stylesheet" href="../<?= asset_v('css/admin.css') ?>"></head><body><?= admin_pre() ?><?php include __DIR__ . '/../includes/admin_icons.php'; ?>
<aside class="side" id="side">
  <div class="brand"><img class="logo" src="<?= $logo ?>" alt=""><b><?= e($shop) ?></b></div>
  <nav><?= nav_item('index', t('stats'), $page, 'stats') ?><?= nav_item('settings', t('shop_info'), $page, 'shop') ?><?= nav_item('covers', t('cover_images'), $page, 'covers') ?><?= nav_item('layout', t('page_layout'), $page, 'layout') ?><?= nav_item('appearance', t('appearance'), $page, 'look') ?><?= nav_item('headings', t('headings'), $page, 'heads') ?><?= nav_item('banners', t('banners'), $page, 'banners') ?><?= nav_item('hours', t('hours'), $page, 'hours') ?><?= nav_item('interface', t('interface_texts'), $page, 'lang') ?><?= nav_item('categories', t('categories'), $page, 'cats') ?><?= nav_item('products', t('admin_products'), $page, 'products') ?><?= nav_item('links', t('links'), $page, 'links') ?><?= nav_item('reviews', t('reviews'), $page, 'reviews', $bc['rev']) ?><?= nav_item('messages', t('messages'), $page, 'messages', $bc['msg']) ?><?= nav_item('whatsapp', t('whatsapp'), $page, 'wa') ?><?= nav_item('tracking', t('tracking'), $page, 'track') ?><?= nav_item('seo', t('seo'), $page, 'seo') ?><?= nav_item('admins', t('admins'), $page, 'admins') ?></nav>
  <div class="bottom"><a href="../index.php" target="_blank"><svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#n-view"/></svg><span><?= e(t('view_page')) ?></span></a><a class="logout" href="logout.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg><span><?= e(t('logout')) ?></span></a></div>
</aside>
<div class="overlay" id="overlay"></div>
<div class="main">
<header class="bar">
  <button class="burger" id="burger" type="button" aria-label="<?= e(t('menu')) ?>" aria-controls="side" aria-expanded="false"><span></span><span></span><span></span></button>
  <img class="logo" src="<?= $logo ?>" alt=""><span class="ttl"><?= e($shop) ?></span>
  <div class="langsw"><?= lang_switch_html() ?></div>
</header>
<div class="wrap">
