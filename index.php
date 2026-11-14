<?php
require __DIR__ . '/includes/bootstrap.php';
$newVisit = log_visit();
$s = settings();
$links = db()->query('SELECT * FROM links WHERE active=1 ORDER BY sort,id')->fetchAll();
$products = db()->query('SELECT * FROM products WHERE active=1 ORDER BY sort,id')->fetchAll();
$name = pick($s, 'shop_name'); $tag = pick($s, 'tagline');
$coords = parse_coords($s['map_coords'] ?? '');
$mapName = pick($s, 'map_name'); if ($mapName === '') $mapName = $name;
$base = base_url();
$first = first_lang(); $second = second_lang();
$pageUrl = $base . (L() !== default_lang() ? '?lang=' . L() : '');
$ogImg = abs_img($s['og_image'] ?? '') ?: (abs_img($s['cover'] ?? '') ?: abs_img($s['profile'] ?? ''));
$favSrc = 'favicon.php?v=' . urlencode((string)(($s['favicon'] ?? '') !== '' ? $s['favicon'] : ($s['profile'] ?? '')));
$desc = trim($name . ' — ' . $tag);
$layout = ($s['links_layout'] ?? 'list') === 'grid' ? 'grid' : 'list';
$embed = trim($s['map_embed'] ?? '');
$mapUrl = !empty($s['map_link']) ? $s['map_link'] : ($coords ? 'https://www.google.com/maps?q=' . $coords[0] . ',' . $coords[1] : '');
$logo = img_src($s['profile'] ?? '');
// button colors
$btnCustom = ($s['btn_mode'] ?? 'brand') === 'custom';
$gBg = hex_or($s['btn_bg'] ?? '', '#A47735'); $gFg = hex_or($s['btn_text'] ?? '', '#FFFFFF');
// welcome popup
$pTitle = pick($s, 'popup_title'); $pText = pick($s, 'popup_text'); $pBtn = pick($s, 'popup_btn');
$popOn = flag_on($s, 'popup_enabled') && ($pTitle !== '' || $pText !== '');
$preOn = flag_on($s, 'preloader_enabled');
// SEO (Admin > SEO) and app install (PWA)
$seoTitle = trim(pick($s, 'seo_title')); if ($seoTitle === '') $seoTitle = $name;
$seoDesc = trim(pick($s, 'seo_desc')); if ($seoDesc === '') $seoDesc = $desc;
$pwaOn = flag_on($s, 'pwa_enabled'); $pwaColor = hex_or($s['pwa_color'] ?? '', '#A47735'); $favVer = (string)(($s['favicon'] ?? '') !== '' ? $s['favicon'] : ($s['profile'] ?? ''));
// catalog filters (search + categories) and forms
$catOn = flag_on($s, 'catalog_search') && $products;
$cats = $catOn ? db()->query('SELECT c.* FROM categories c WHERE c.active=1 AND EXISTS (SELECT 1 FROM products p WHERE p.category_id=c.id AND p.active=1) ORDER BY c.sort,c.id')->fetchAll() : [];
$contactOn = flag_on($s, 'contact_enabled');
// product button (Buy now / Order now) - text is editable in Admin > Appearance and per product
$buyOn = flag_on($s, 'buy_enabled'); $buyText = pick($s, 'buy_text');
$buyStyle = 'background:' . hex_or($s['buy_bg'] ?? '', '#A47735') . ';color:' . hex_or($s['buy_fg'] ?? '', '#FFFFFF');
// banner sliders (Admin > Banners): grouped by position
$slidersByPos = [];
$sl = db()->query('SELECT * FROM sliders WHERE active=1 ORDER BY sort,id')->fetchAll();
if ($sl) {
  $bySlider = [];
  foreach (db()->query('SELECT * FROM slides WHERE active=1 ORDER BY sort,id')->fetchAll() as $sd) $bySlider[$sd['slider_id']][] = $sd;
  foreach ($sl as $x) { $x['slides'] = $bySlider[$x['id']] ?? []; if ($x['slides']) $slidersByPos[$x['position']][] = $x; }
}
// headings (Admin > Headings): grouped by position
$byPos = [];
foreach (db()->query('SELECT * FROM headings WHERE active=1 ORDER BY sort,id')->fetchAll() as $h) { if (pick($h, 'text') !== '') $byPos[$h['position']][] = $h; }
// ordered sections (Admin > Page layout): headings and banners can be attached above / below ANY section
$ordered = array_keys(SECTIONS);
usort($ordered, function ($a, $b) use ($s) { $x = (int)($s['order_' . $a] ?? SECTIONS[$a]); $y = (int)($s['order_' . $b] ?? SECTIONS[$b]); return $x <=> $y ?: strcmp($a, $b); });
$slot = function ($pos) use ($byPos, $slidersByPos) { render_headings($byPos[$pos] ?? []); render_sliders($slidersByPos[$pos] ?? []); };
// animations of the cover / logo / shop name / slogan (Admin > Appearance)
$animOn = flag_on($s, 'anim_enabled');
$an = function ($k) use ($s, $animOn) { $v = $s['anim_' . $k] ?? 'none'; return ($animOn && $v !== 'none' && in_array($v, ANIMS, true)) ? ' an an-' . $v . (flag_on($s, 'anim_repeat_' . $k) ? ' an-rep' : '') : ''; };
// cover images: the main cover + extra slides (Admin > Cover images)
$coverImgs = [];
if (!empty($s['cover']) && flag_on($s, 'cover_main')) $coverImgs[] = $s['cover'];
foreach (db()->query('SELECT image FROM cover_slides WHERE active=1 ORDER BY sort,id')->fetchAll(PDO::FETCH_COLUMN) as $ci) $coverImgs[] = $ci;
if (!$coverImgs && !empty($s['cover'])) $coverImgs[] = $s['cover'];
$coverFx = ($s['cover_effect'] ?? 'slide') === 'fade' ? 'fade' : 'slide'; $coverFit = ($s['cover_fit'] ?? 'contain') === 'cover' ? 'fill' : 'whole';
// floating WhatsApp button
$wfNum = wa_number($s); $wfOn = flag_on($s, 'wa_float') && $wfNum !== ''; $wfSide = ($s['wa_float_side'] ?? 'end') === 'start' ? 'start' : 'end';
$wfAnim = in_array($s['wa_float_anim'] ?? 'pulse', ['none', 'pulse', 'ripple', 'bounce', 'shake', 'float'], true) ? $s['wa_float_anim'] : 'pulse'; $wfLabel = pick($s, 'wa_float_label');
?><!DOCTYPE html>
<html lang="<?= L() ?>" dir="<?= dir_attr() ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="<?= e($pwaOn ? $pwaColor : '#F3EBDA') ?>">
<title><?= e($seoTitle) ?></title>
<meta name="description" content="<?= e($seoDesc) ?>">
<?php if (!flag_on($s, 'seo_index')): ?><meta name="robots" content="noindex,nofollow">
<?php endif; ?>
<link rel="icon" href="<?= e($favSrc) ?>">
<link rel="apple-touch-icon" href="pwa-icon.php?s=180&amp;v=<?= urlencode($favVer) ?>">
<link rel="canonical" href="<?= e($pageUrl) ?>">
<link rel="alternate" hreflang="<?= e($first) ?>" href="<?= e($base . '?lang=' . $first) ?>">
<link rel="alternate" hreflang="<?= e($second) ?>" href="<?= e($base . '?lang=' . $second) ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($base) ?>">
<?php if ($pwaOn): ?><link rel="manifest" href="manifest.php">
<meta name="mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-title" content="<?= e($name) ?>">
<?php endif; ?>
<!-- Link preview (Open Graph + Twitter) -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($name) ?>">
<meta property="og:title" content="<?= e($seoTitle) ?>">
<meta property="og:description" content="<?= e($seoDesc) ?>">
<meta property="og:url" content="<?= e($pageUrl) ?>">
<meta property="og:locale" content="<?= e(LANGS[L()]['loc']) ?>">
<meta property="og:locale:alternate" content="<?= e(LANGS[L() === $first ? $second : $first]['loc']) ?>">
<?php if ($ogImg): ?><meta property="og:image" content="<?= e($ogImg) ?>">
<meta property="og:image:alt" content="<?= e($name) ?>">
<meta name="twitter:image" content="<?= e($ogImg) ?>">
<?php endif; ?>
<meta name="twitter:card" content="<?= $ogImg ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= e($seoTitle) ?>">
<meta name="twitter:description" content="<?= e($seoDesc) ?>">
<?= jsonld_html($s) ?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800<?php foreach (array_unique([LANGS[$first]['font'], LANGS[$second]['font']]) as $gf) echo $gf !== '' ? '&family=' . $gf : ''; ?>&display=swap">
<script>document.documentElement.className+=" js"</script>
<link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
</head>
<body class="<?= trim(($preOn ? 'has-pre ' : '') . ($wfOn && $wfSide === 'start' ? 'wf-start' : '')) ?>">
<?php include __DIR__ . '/includes/sprite.php'; ?>
<?php if ($preOn): ?>
<div id="preloader" aria-hidden="true"><div class="pl"><span class="ring"></span><img src="<?= e($logo) ?>" alt=""></div></div>
<?php endif; ?>
<?php ob_start(); ?>
  <div class="linkshead">
  <div class="layoutsw" role="group" aria-label="<?= e(t('links_layout')) ?>">
    <button type="button" data-mode="list" aria-label="<?= e(t('lay_list')) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    <button type="button" data-mode="grid" aria-label="<?= e(t('lay_grid')) ?>"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg></button>
  </div></div>
  <ul class="links<?= $layout === 'grid' ? ' cols2' : '' ?>" id="links" data-default="<?= $layout ?>">
<?php foreach ($links as $l):
  $ic = ICON_FOR[$l['type']] ?? 'link';
  $bg = hex_or($l['bg_color'] ?? ''); $fg = hex_or($l['text_color'] ?? '');
  if ($bg === '' && $fg === '' && $btnCustom) { $bg = $gBg; $fg = $gFg; }
  $st = ($bg !== '' ? 'background:' . $bg . ';' : '') . ($fg !== '' ? 'color:' . $fg . ';' : '');
?>
    <li><a class="btn <?= e($ic) ?>" href="track.php?k=l&amp;id=<?= (int)$l['id'] ?>"<?= $st !== '' ? ' style="' . e($st) . '"' : '' ?>><span class="ic"><?php if ($l['icon'] !== ''): ?><img src="<?= e(img_src($l['icon'])) ?>" alt=""><?php else: ?><svg><use href="#i-<?= e($ic) ?>"/></svg><?php endif; ?></span><span class="t"><?= e(pick($l, 'title')) ?></span></a></li>
<?php endforeach; ?>
  </ul>
<?php $sec['links'] = ob_get_clean(); ob_start(); ?>
<?php if ($products): ?>
<?php if ($catOn): ?>
  <div class="catbar">
    <div class="search"><input type="search" id="psearch" placeholder="<?= e(t('search_ph')) ?>" aria-label="<?= e(t('search_ph')) ?>" autocomplete="off"></div>
<?php if ($cats): ?>    <div class="chips" id="pchips" role="group"><button type="button" class="chip on" data-cat="0"><?= e(t('cat_all')) ?></button><?php foreach ($cats as $c): ?><button type="button" class="chip" data-cat="<?= (int)$c['id'] ?>"><?= e(pick($c, 'name')) ?></button><?php endforeach; ?></div>
<?php endif; ?>
  </div>
<?php endif; ?>
  <section class="grid" id="pgrid">
<?php foreach ($products as $p):
  $pn = pick($p, 'name'); $pid = (int)$p['id'];
  $bt = pick($p, 'btn'); if ($bt === '') $bt = $buyText;
  list($ph, $disc, $cd) = product_price($p, $s);
  $mode = product_mode($p, $s);
  if ($mode === 'whatsapp' && wa_order_url($p, $s) !== '') { $go = 'track.php?k=w&amp;id=' . $pid; $attr = ' target="_blank" rel="noopener"'; }
  elseif ($mode === 'form' && $contactOn) { $go = '#contact'; $attr = ' data-order="' . $pid . '"'; }
  elseif (trim($p['url']) !== '') { $go = 'track.php?k=p&amp;id=' . $pid; $attr = ''; }
  else { $go = ''; $attr = ''; }
  $has = $go !== '';
  $hay = mb_strtolower($p['name_en'] . ' ' . $p['name_ar'] . ' ' . $p['price'], 'UTF-8');
?>
    <div class="card" data-cat="<?= (int)$p['category_id'] ?>" data-s="<?= e($hay) ?>">
      <<?= $has ? 'a href="' . $go . '"' . $attr : 'div' ?> class="card-main">
        <div class="thumb"><?php if ($p['image'] !== ''): ?><img src="<?= e(img_src($p['image'])) ?>" alt="<?= e($pn) ?>" loading="lazy"><?php endif; ?><?php if ($disc > 0 && flag_on($s, 'show_discount')): ?><span class="disc">-<?= $disc ?>%</span><?php endif; ?></div>
        <div class="info"><h3 title="<?= e($pn) ?>"><?= e($pn) ?></h3><?php if ($ph !== ''): ?><p class="price"><?= $ph ?></p><?php endif; ?><?= $cd ?></div>
      </<?= $has ? 'a' : 'div' ?>>
<?php if ($buyOn && $has && $bt !== ''): ?>      <a class="buy" href="<?= $go ?>"<?= $attr ?> style="<?= e($buyStyle) ?>"><?= e($bt) ?></a>
<?php endif; ?>
    </div>
<?php endforeach; ?>
  </section>
  <p class="nores" id="nores" hidden><?= e(t('no_results')) ?></p>
<?php if (!empty($s['store_url'])): ?>  <a class="more" href="<?= e(safe_url($s['store_url'])) ?>"><?= e(t('all_products')) ?></a>
<?php endif; endif; ?>
<?php $sec['products'] = ob_get_clean();
$sec['hours'] = hours_html($s); $sec['reviews'] = reviews_html($s); $sec['contact'] = contact_html($s, $products);
ob_start(); ?>
<?php if ($coords || $mapUrl || $embed): $dirUrl = $coords ? 'https://www.google.com/maps/dir/?api=1&destination=' . $coords[0] . ',' . $coords[1] : ''; ?>
  <section class="map">
    <div class="maphead"><span class="mi"><svg><use href="#i-pin"/></svg></span><div><?php $kick = pick($s, 'map_kicker'); if ($kick === '') $kick = t('location'); ?><span class="kick"><?= e($kick) ?></span><h2><?= e($mapName) ?></h2></div></div>
<?php if ($embed || $coords):
  $gm = $embed ? $embed . (strpos($embed, 'hl=') === false ? '&hl=' . L() : '') : 'https://maps.google.com/maps?q=' . $coords[0] . ',' . $coords[1] . '&z=17&hl=' . L() . '&output=embed'; ?>
    <div class="mapbox">
      <iframe title="<?= e(t('map_title')) ?>" src="<?= e($gm) ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
      <div class="maplabel"><span class="dot"></span><span><?= e($mapName) ?></span></div>
    </div>
<?php endif; ?>
    <div class="mapbtns">
<?php if ($mapUrl): ?>      <a class="mb primary" href="track.php?k=m&amp;id=1"><?= e(t('open_maps')) ?></a>
<?php endif; if ($dirUrl): ?>      <a class="mb" href="track.php?k=m&amp;id=2"><?= e(t('directions')) ?></a>
<?php endif; ?>    </div>
  </section>
<?php endif; ?>
<?php $sec['map'] = ob_get_clean(); ?>
<main class="page">
<?php render_bars($byPos['top_bar'] ?? [], $s); render_sliders($slidersByPos['top'] ?? []); ?>
  <div class="coverwrap">
    <div class="langsw" role="group" aria-label="Language"><?= lang_switch_html() ?></div>
    <div class="cover cv-<?= $coverFit ?> cv-<?= $coverFx ?><?= $an('cover') ?>" id="cover" data-auto="<?= flag_on($s, 'cover_autoplay') && count($coverImgs) > 1 ? 1 : 0 ?>" data-interval="<?= max(2, min(30, (int)($s['cover_interval'] ?? 4))) ?>">
      <div class="cv-track"><?php foreach ($coverImgs as $ci => $cimg): ?><div class="cv-slide<?= $ci === 0 ? ' on' : '' ?>"><img src="<?= e(img_src($cimg)) ?>" alt="<?= e($name) ?>"<?= $ci > 0 ? ' loading="lazy"' : '' ?>></div><?php endforeach; ?></div>
<?php if (count($coverImgs) > 1): ?>      <div class="cv-dots"></div>
<?php endif; ?>
    </div>
  </div>
  <img class="profile<?= $an('logo') ?>" src="<?= e($logo) ?>" alt="">
  <h1 class="<?= trim($an('name')) ?>"><?= e($name) ?></h1>
  <p class="desc<?= $an('tagline') ?>"><?= e($tag) ?></p>
<?php $slot('after_profile'); foreach ($ordered as $k) { $slot('before_' . $k); echo $sec[$k]; $slot('after_' . $k); } $slot('bottom'); ?>
</main>

<?php if ($pwaOn && flag_on($s, 'pwa_install')): ?>
<div class="pwa" id="pwa" hidden><button type="button" class="pwa-add" id="pwaAdd"><?= e(t('pwa_add')) ?></button><button type="button" class="pwa-x" id="pwaX" aria-label="<?= e(t('close')) ?>">&times;</button><p class="pwa-ios" id="pwaIos" hidden><?= e(t('pwa_ios')) ?></p></div>
<?php endif; ?>
<?php if ($wfOn): ?>
<a class="wfloat wf-<?= $wfAnim ?> wf-<?= $wfSide ?><?= $wfLabel === '' ? ' wf-icon' : '' ?>" href="track.php?k=f" target="_blank" rel="noopener" aria-label="WhatsApp"><svg aria-hidden="true"><use href="#i-wa"/></svg><?php if ($wfLabel !== ''): ?><span><?= e($wfLabel) ?></span><?php endif; ?></a>
<?php endif; ?>
<?= footer_html() ?>

<?php if ($popOn): ?>
<div class="wp" id="wpop" hidden role="dialog" aria-modal="true" aria-labelledby="wp-t" data-freq="<?= e($s['popup_freq'] ?? 'day') ?>" data-delay="<?= (int)($s['popup_delay'] ?? 1) ?>" data-key="<?= e(substr(md5($pTitle . $pText . $pBtn), 0, 10)) ?>">
  <div class="wp-card">
    <button type="button" class="wp-x" data-close aria-label="<?= e(t('close')) ?>">&times;</button>
    <img class="wp-logo" src="<?= e($logo) ?>" alt="">
<?php if ($pTitle !== ''): ?>    <h2 id="wp-t"><?= e($pTitle) ?></h2>
<?php endif; if ($pText !== ''): ?>    <p><?= e($pText) ?></p>
<?php endif; if ($pBtn !== ''): if (!empty($s['popup_btn_url'])): ?>    <a class="wp-btn" href="<?= e(safe_url($s['popup_btn_url'])) ?>"><?= e($pBtn) ?></a>
<?php else: ?>    <button type="button" class="wp-btn" data-close><?= e($pBtn) ?></button>
<?php endif; endif; ?>
  </div>
</div>
<?php endif; ?>

<?php if ($newVisit): ?><script>window.KV_GEO=1;</script><?php endif; ?>
<script src="<?= asset_v('js/main.js') ?>"></script>
</body>
</html>
