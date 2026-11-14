<?php
date_default_timezone_set('Asia/Riyadh');
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
  session_start();
}
$GLOBALS['cfg'] = require __DIR__ . '/../config.php';
require __DIR__ . '/lang.php';
const UPLOAD_DIR = __DIR__ . '/../uploads/';
const LINK_TYPES = ['store', 'snapchat', 'tiktok', 'whatsapp', 'phone', 'email', 'other'];
const ICON_FOR = ['store' => 'store', 'snapchat' => 'snap', 'tiktok' => 'tt', 'whatsapp' => 'wa', 'phone' => 'tel', 'email' => 'mail', 'other' => 'link'];
const SCHEMA_VERSION = '18';
// Sections of the public page that can be moved (Admin > Page layout). Headings and banners attach before_/after_ any of them.
const SECTIONS = ['links' => 10, 'products' => 20, 'hours' => 30, 'reviews' => 40, 'map' => 50, 'contact' => 60];
const ANIMS = ['none', 'fade', 'slide-up', 'slide-down', 'slide-left', 'slide-right', 'zoom', 'bounce', 'flip', 'typewriter', 'glow', 'pulse', 'shimmer', 'float'];

function cfg($k) { return $GLOBALS['cfg'][$k] ?? null; }
function db() {
  static $p;
  if (!$p) {
    $p = new PDO('mysql:host=' . cfg('db_host') . ';dbname=' . cfg('db_name') . ';charset=utf8mb4',
      cfg('db_user'), cfg('db_pass'),
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    migrate($p);
  }
  return $p;
}
// Creates missing tables/columns and upgrades older versions of the database automatically.
function migrate(PDO $p) {
  try { $v = $p->query("SELECT v FROM settings WHERE k='schema_v'")->fetchColumn(); } catch (Throwable $e) { $v = false; }
  if ($v === SCHEMA_VERSION) return;
  $p->exec("CREATE TABLE IF NOT EXISTS settings (k VARCHAR(50) NOT NULL PRIMARY KEY, v TEXT) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS links (id INT AUTO_INCREMENT PRIMARY KEY, type VARCHAR(20) NOT NULL, title_en VARCHAR(100) NOT NULL DEFAULT '', title_ar VARCHAR(100) NOT NULL DEFAULT '', value VARCHAR(500) NOT NULL, icon VARCHAR(255) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, name_en VARCHAR(150) NOT NULL DEFAULT '', name_ar VARCHAR(150) NOT NULL DEFAULT '', price VARCHAR(50) NOT NULL DEFAULT '', image VARCHAR(500) NOT NULL DEFAULT '', url VARCHAR(500) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS admins (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL DEFAULT '', username VARCHAR(50) NULL, email VARCHAR(120) NULL, phone VARCHAR(30) NULL, password_hash VARCHAR(255) NOT NULL, UNIQUE KEY u_username (username), UNIQUE KEY u_email (email), UNIQUE KEY u_phone (phone)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS visits (id INT AUTO_INCREMENT PRIMARY KEY, visited_at DATETIME NOT NULL, day DATE NOT NULL, ip_hash CHAR(64) NOT NULL, device VARCHAR(20), browser VARCHAR(30), referrer VARCHAR(190), INDEX(day)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS clicks (id INT AUTO_INCREMENT PRIMARY KEY, clicked_at DATETIME NOT NULL, day DATE NOT NULL, kind VARCHAR(10) NOT NULL, item_id INT NOT NULL, label VARCHAR(150), ip_hash CHAR(64), INDEX(day)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS headings (id INT AUTO_INCREMENT PRIMARY KEY, position VARCHAR(20) NOT NULL, text_en VARCHAR(200) NOT NULL DEFAULT '', text_ar VARCHAR(200) NOT NULL DEFAULT '', sub_en VARCHAR(300) NOT NULL DEFAULT '', sub_ar VARCHAR(300) NOT NULL DEFAULT '', url VARCHAR(500) NOT NULL DEFAULT '', align VARCHAR(10) NOT NULL DEFAULT 'center', size VARCHAR(5) NOT NULL DEFAULT 'md', bg_color VARCHAR(7) NOT NULL DEFAULT '', text_color VARCHAR(7) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS sliders (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL DEFAULT '', position VARCHAR(20) NOT NULL DEFAULT 'before_products', autoplay TINYINT NOT NULL DEFAULT 1, interval_s INT NOT NULL DEFAULT 5, ratio VARCHAR(6) NOT NULL DEFAULT '3/1', fit VARCHAR(10) NOT NULL DEFAULT 'cover', arrows TINYINT NOT NULL DEFAULT 1, dots TINYINT NOT NULL DEFAULT 1, sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS slides (id INT AUTO_INCREMENT PRIMARY KEY, slider_id INT NOT NULL, image VARCHAR(500) NOT NULL DEFAULT '', url VARCHAR(500) NOT NULL DEFAULT '', caption_en VARCHAR(200) NOT NULL DEFAULT '', caption_ar VARCHAR(200) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1, INDEX(slider_id)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS hours (day TINYINT NOT NULL PRIMARY KEY, closed TINYINT NOT NULL DEFAULT 0, all_day TINYINT NOT NULL DEFAULT 0, open1 VARCHAR(5) NOT NULL DEFAULT '', close1 VARCHAR(5) NOT NULL DEFAULT '', open2 VARCHAR(5) NOT NULL DEFAULT '', close2 VARCHAR(5) NOT NULL DEFAULT '') DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS tracking_links (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL DEFAULT '', source VARCHAR(60) NOT NULL, medium VARCHAR(60) NOT NULL DEFAULT '', campaign VARCHAR(60) NOT NULL DEFAULT '') DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name_en VARCHAR(100) NOT NULL DEFAULT '', name_ar VARCHAR(100) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS reviews (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, rating TINYINT NOT NULL DEFAULT 5, text TEXT NOT NULL, approved TINYINT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL, ip_hash CHAR(64) NOT NULL DEFAULT '', INDEX(approved)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, phone VARCHAR(40) NOT NULL DEFAULT '', email VARCHAR(120) NOT NULL DEFAULT '', product_id INT NOT NULL DEFAULT 0, product_name VARCHAR(150) NOT NULL DEFAULT '', message TEXT NOT NULL, created_at DATETIME NOT NULL, is_read TINYINT NOT NULL DEFAULT 0, ip_hash CHAR(64) NOT NULL DEFAULT '', lang VARCHAR(5) NOT NULL DEFAULT '', INDEX(is_read)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS online (sid CHAR(32) NOT NULL PRIMARY KEY, last_seen DATETIME NOT NULL, device VARCHAR(20), INDEX(last_seen)) DEFAULT CHARSET=utf8mb4");
  $p->exec("CREATE TABLE IF NOT EXISTS login_fails (ip_hash CHAR(64) NOT NULL PRIMARY KEY, tries INT NOT NULL DEFAULT 0, last_try DATETIME NOT NULL) DEFAULT CHARSET=utf8mb4");
  $has = function ($t, $c) use ($p) { return (bool)$p->query("SHOW COLUMNS FROM `$t` LIKE '$c'")->fetch(); };
  $add = function ($t, $c, $def) use ($p, $has) { if (!$has($t, $c)) $p->exec("ALTER TABLE `$t` ADD COLUMN `$c` $def"); };
  // upgrade from the first version (single-language columns)
  $add('links', 'title_en', "VARCHAR(100) NOT NULL DEFAULT ''"); $add('links', 'title_ar', "VARCHAR(100) NOT NULL DEFAULT ''"); $add('links', 'icon', "VARCHAR(255) NOT NULL DEFAULT ''");
  if ($has('links', 'title')) {
    $p->exec("UPDATE links SET title_en=IF(title_en='',title,title_en), title_ar=IF(title_ar='',title,title_ar)");
    $p->exec("ALTER TABLE links DROP COLUMN title");
  }
  $add('products', 'name_en', "VARCHAR(150) NOT NULL DEFAULT ''"); $add('products', 'name_ar', "VARCHAR(150) NOT NULL DEFAULT ''");
  if ($has('products', 'name')) {
    $p->exec("UPDATE products SET name_en=IF(name_en='',name,name_en), name_ar=IF(name_ar='',name,name_ar)");
    $p->exec("ALTER TABLE products DROP COLUMN name");
  }
  $p->exec("ALTER TABLE products MODIFY image VARCHAR(500) NOT NULL DEFAULT ''");
  $add('admins', 'name', "VARCHAR(100) NOT NULL DEFAULT ''"); $add('admins', 'email', "VARCHAR(120) NULL"); $add('admins', 'phone', "VARCHAR(30) NULL");
  $p->exec("ALTER TABLE admins MODIFY username VARCHAR(50) NULL");
  // v5: visitor location + traffic source
  $add('visits', 'country', "VARCHAR(80) NOT NULL DEFAULT ''"); $add('visits', 'country_code', "CHAR(2) NOT NULL DEFAULT ''");
  $add('visits', 'region', "VARCHAR(100) NOT NULL DEFAULT ''"); $add('visits', 'city', "VARCHAR(100) NOT NULL DEFAULT ''");
  $add('visits', 'source', "VARCHAR(20) NOT NULL DEFAULT ''"); $add('visits', 'source_detail', "VARCHAR(190) NOT NULL DEFAULT ''");
  $add('online', 'country', "VARCHAR(80) NOT NULL DEFAULT ''"); $add('online', 'country_code', "CHAR(2) NOT NULL DEFAULT ''"); $add('online', 'city', "VARCHAR(100) NOT NULL DEFAULT ''");
  $p->exec("UPDATE visits SET source=IF(referrer IS NULL OR referrer='','direct','referral'), source_detail=IFNULL(referrer,'') WHERE source=''");
  // v11: UTM tracking
  $add('visits', 'utm_source', "VARCHAR(60) NOT NULL DEFAULT ''"); $add('visits', 'utm_medium', "VARCHAR(60) NOT NULL DEFAULT ''"); $add('visits', 'utm_campaign', "VARCHAR(60) NOT NULL DEFAULT ''");
  // v10: per-product button text
  $add('products', 'btn_en', "VARCHAR(60) NOT NULL DEFAULT ''"); $add('products', 'btn_ar', "VARCHAR(60) NOT NULL DEFAULT ''");
  // v6: per-button colors
  $add('links', 'bg_color', "VARCHAR(7) NOT NULL DEFAULT ''"); $add('links', 'text_color', "VARCHAR(7) NOT NULL DEFAULT ''");
  foreach (['ADD UNIQUE KEY u_email (email)', 'ADD UNIQUE KEY u_phone (phone)'] as $x) { try { $p->exec("ALTER TABLE admins $x"); } catch (Throwable $e) {} }
  $get = function ($k) use ($p) { $q = $p->prepare('SELECT v FROM settings WHERE k=?'); $q->execute([$k]); return $q->fetchColumn(); };
  $set = function ($k, $v) use ($p) { $p->prepare('INSERT INTO settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)')->execute([$k, $v]); };
  foreach (['shop_name', 'tagline'] as $b) {
    $old = $get($b);
    if ($old !== false) { foreach (['_en', '_ar'] as $sfx) if ($get($b . $sfx) === false) $set($b . $sfx, $old); }
  }
  foreach (['map_coords' => '25.8694,43.4973', 'map_name_en' => '', 'map_name_ar' => '', 'map_link' => '', 'store_url' => ''] as $k => $d) {
    if ($get($k) === false) $set($k, $d);
  }
  $defs = [
    'btn_mode' => 'brand', 'btn_bg' => '#A47735', 'btn_text' => '#FFFFFF',
    'headline_enabled' => '1', 'headline_text_en' => 'Welcome to Kaif Al Diyafa', 'headline_text_ar' => 'أهلاً بكم في كيف الضيافة', 'headline_url' => '', 'headline_bg' => '#A47735', 'headline_fg' => '#FFFFFF', 'headline_scroll' => '0',
    'preloader_enabled' => '1',
    'popup_enabled' => '1', 'popup_title_en' => 'Welcome!', 'popup_title_ar' => 'أهلاً وسهلاً بك',
    'popup_text_en' => 'Thank you for visiting. Browse our products and follow us on social media.', 'popup_text_ar' => 'شكراً لزيارتك. تصفح منتجاتنا وتابعنا على وسائل التواصل.',
    'popup_btn_en' => 'Start browsing', 'popup_btn_ar' => 'ابدأ التصفح', 'popup_btn_url' => '', 'popup_freq' => 'always', 'popup_delay' => '1',
    'footer_enabled' => '1', 'footer_text_en' => 'Developed by', 'footer_text_ar' => 'تطوير', 'footer_link_en' => 'Mr. Amin', 'footer_link_ar' => 'Mr. Amin', 'footer_url' => 'https://amin.free.nf',
    'heading_links_on' => '0', 'heading_links_en' => '', 'heading_links_ar' => '',
    'heading_products_on' => '1', 'heading_products_en' => 'Our Products', 'heading_products_ar' => 'منتجاتنا',
    'heading_map_on' => '1', 'heading_map_en' => 'Our location', 'heading_map_ar' => 'موقعنا',
    'admin_preloader_enabled' => '1',
    'buy_mode' => 'link', 'wa_number' => '', 'wa_msg_en' => 'Hello, I want to order: {name} - {price}', 'wa_msg_ar' => 'مرحباً، أريد أن أطلب: {name} - {price}', 'catalog_search' => '1',
    'reviews_enabled' => '1', 'reviews_position' => 'before_map', 'reviews_submit' => '1', 'reviews_limit' => '8',
    'contact_enabled' => '1', 'contact_position' => 'bottom', 'contact_title_en' => 'Contact us', 'contact_title_ar' => 'تواصل معنا', 'notify_enabled' => '0', 'notify_email' => '',
    'seo_title_en' => '', 'seo_title_ar' => '', 'seo_desc_en' => '', 'seo_desc_ar' => '', 'seo_index' => '1',
    'pwa_enabled' => '1', 'pwa_install' => '1', 'pwa_color' => '#A47735', 'pwa_short_en' => '', 'pwa_short_ar' => '',
    'anim_repeat_cover' => '0', 'anim_repeat_logo' => '0', 'anim_repeat_name' => '0', 'anim_repeat_tagline' => '0',
    'cover_main' => '1', 'cover_autoplay' => '1', 'cover_interval' => '4', 'cover_effect' => 'slide', 'cover_fit' => 'contain',
    'wa_float' => '1', 'wa_float_side' => 'end', 'wa_float_anim' => 'pulse', 'wa_float_label_en' => '', 'wa_float_label_ar' => '', 'wa_float_msg_en' => 'Hello, I would like to ask about your products.', 'wa_float_msg_ar' => 'مرحباً، أود الاستفسار عن منتجاتكم.', 'store_ip' => '1',
    'anim_enabled' => '1', 'anim_cover' => 'fade', 'anim_logo' => 'zoom', 'anim_name' => 'slide-up', 'anim_tagline' => 'fade', 'countdown_default' => '1',
    'lang1' => 'en', 'lang_default' => 'second', 'map_kicker_en' => '', 'map_kicker_ar' => '',
    'currency_default' => 'SAR', 'show_discount' => '1',
    'hours_enabled' => '1', 'hours_position' => 'before_map', 'hours_title_en' => 'Opening hours', 'hours_title_ar' => 'مواعيد العمل', 'hours_format' => '12', 'hours_start' => '6', 'hours_badge' => '1',
    'buy_enabled' => '1', 'buy_text_en' => 'Buy now', 'buy_text_ar' => 'اشتري الآن', 'buy_bg' => '#A47735', 'buy_fg' => '#FFFFFF',
  ];
  foreach ($defs as $k => $d) { if ($get($k) === false) $set($k, $d); }
  // v8: headings table. First time only, turn the old single headline/headings into rows.
  if ($get('headings_seeded') === false) {
    if ((int)$p->query('SELECT COUNT(*) FROM headings')->fetchColumn() === 0) {
      $ins = $p->prepare('INSERT INTO headings (position,text_en,text_ar,url,sort) VALUES (?,?,?,?,?)');
      $te = (string)$get('headline_text_en'); $ta = (string)$get('headline_text_ar');
      if ((string)$get('headline_enabled') === '1' && ($te !== '' || $ta !== '')) $ins->execute(['top_bar', $te, $ta, (string)$get('headline_url'), 1]);
      foreach (['links' => 'before_links', 'products' => 'before_products'] as $k => $pos) {
        if ((string)$get("heading_{$k}_on") === '1') $ins->execute([$pos, (string)$get("heading_{$k}_en"), (string)$get("heading_{$k}_ar"), '', 1]);
      }
    }
    $set('headings_seeded', '1');
  }
  // v14: the default "Our location" heading is now inside the map box, remove the duplicate heading row (once)
  if ($get('map_kicker_done') === false) {
    $p->exec("DELETE FROM headings WHERE position='before_map' AND text_en='Our location' AND text_ar='موقعنا'");
    $set('map_kicker_done', '1');
  }
  // v13: regular / sale price + currency per product (old text prices are converted once)
  if (!$has('products', 'reg_price')) {
    $add('products', 'reg_price', 'DECIMAL(12,2) NULL'); $add('products', 'sale_price', 'DECIMAL(12,2) NULL'); $add('products', 'currency', "VARCHAR(5) NOT NULL DEFAULT ''");
    $map = ['SAR' => 'SAR', 'SR' => 'SAR', 'ر.س' => 'SAR', '﷼' => 'SAR', 'AED' => 'AED', 'د.إ' => 'AED', 'USD' => 'USD', '$' => 'USD', 'BDT' => 'BDT', '৳' => 'BDT', 'TK' => 'BDT', 'EUR' => 'EUR', '€' => 'EUR', 'GBP' => 'GBP', '£' => 'GBP'];
    $upd = $p->prepare('UPDATE products SET reg_price=?, currency=?, price=? WHERE id=?');
    foreach ($p->query('SELECT id,price FROM products')->fetchAll() as $r) {
      if (preg_match('/^\s*([^\d\s]{0,4})?\s*([\d]+(?:[.,][\d]+)?)\s*([^\d\s]{0,4})?\s*$/u', (string)$r['price'], $m)) {
        $tok = strtoupper(trim(($m[1] ?? '') !== '' ? $m[1] : ($m[3] ?? ''))); $cur = $map[$tok] ?? ($map[trim(($m[1] ?? '') !== '' ? $m[1] : ($m[3] ?? ''))] ?? '');
        $upd->execute([(float)str_replace(',', '.', $m[2]), $cur, '', $r['id']]);
      }
    }
  }
  // v12: opening hours demo (first time only)
  if ((int)$p->query('SELECT COUNT(*) FROM hours')->fetchColumn() === 0) {
    $ih = $p->prepare('INSERT INTO hours (day,closed,all_day,open1,close1) VALUES (?,0,0,?,?)');
    for ($d = 0; $d <= 6; $d++) $ih->execute([$d, $d === 5 ? '16:00' : '09:00', '23:00']);
  }
  // v18: cover slider, visitor IP
  $p->exec("CREATE TABLE IF NOT EXISTS cover_slides (id INT AUTO_INCREMENT PRIMARY KEY, image VARCHAR(500) NOT NULL DEFAULT '', sort INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1) DEFAULT CHARSET=utf8mb4");
  $add('visits', 'ip', "VARCHAR(45) NOT NULL DEFAULT ''"); $add('online', 'ip', "VARCHAR(45) NOT NULL DEFAULT ''");
  // v17: device / OS, animations, sections order, countdown default
  $add('visits', 'os', "VARCHAR(16) NOT NULL DEFAULT ''"); $add('online', 'os', "VARCHAR(16) NOT NULL DEFAULT ''");
  if ($get('order_links') === false) {
    $mp = ['after_profile' => 5, 'before_links' => 5, 'before_products' => 15, 'before_map' => 45, 'bottom' => 100];
    $set('order_links', '10'); $set('order_products', '20'); $set('order_map', '50');
    foreach (['hours' => 0, 'reviews' => 1, 'contact' => 2] as $k => $off) { $pos = (string)$get($k . '_position'); $set('order_' . $k, (string)(($mp[$pos] ?? ($k === 'contact' ? 100 : 45)) + $off)); }
  }
  // v16: categories, scheduled sales, WhatsApp / form orders
  $add('products', 'category_id', 'INT NOT NULL DEFAULT 0'); $add('products', 'sale_start', 'DATETIME NULL'); $add('products', 'sale_end', 'DATETIME NULL');
  $add('products', 'countdown', 'TINYINT NOT NULL DEFAULT 0'); $add('products', 'buy_mode', "VARCHAR(10) NOT NULL DEFAULT ''");
  // v15: language pair (lang2 comes from the old "second language" setting) + saved tracking links (first time only)
  if ($get('lang2') === false) $set('lang2', (string)($get('second_lang') ?: 'ar'));
  if ($get('tracking_seeded') === false) {
    if ((int)$p->query('SELECT COUNT(*) FROM tracking_links')->fetchColumn() === 0) {
      $it = $p->prepare('INSERT INTO tracking_links (name,source,medium,campaign) VALUES (?,?,?,?)');
      foreach ([['QR code', 'qr', 'qr', 'general'], ['WhatsApp', 'whatsapp', 'social', 'general'], ['Facebook', 'facebook', 'social', 'general'], ['Instagram', 'instagram', 'social', 'general']] as $r) $it->execute($r);
    }
    $set('tracking_seeded', '1');
  }
  // v16: demo categories and reviews (first time only)
  if ($get('catalog_seeded') === false) {
    if ((int)$p->query('SELECT COUNT(*) FROM categories')->fetchColumn() === 0) {
      $ic = $p->prepare('INSERT INTO categories (name_en,name_ar,sort) VALUES (?,?,?)'); $ids = [];
      foreach ([['Coffee', 'قهوة'], ['Dates', 'تمور'], ['Attar', 'عطارة']] as $i => $c) { $ic->execute([$c[0], $c[1], $i + 1]); $ids[] = (int)$p->lastInsertId(); }
      $k = 0; foreach ($p->query('SELECT id FROM products ORDER BY sort,id')->fetchAll(PDO::FETCH_COLUMN) as $pid) { $p->prepare('UPDATE products SET category_id=? WHERE id=? AND category_id=0')->execute([$ids[$k % 3], $pid]); $k++; }
    }
    if ((int)$p->query('SELECT COUNT(*) FROM reviews')->fetchColumn() === 0) {
      $ir = $p->prepare('INSERT INTO reviews (name,rating,text,approved,created_at) VALUES (?,?,?,1,?)');
      $ir->execute(['Ahmed', 5, 'Excellent quality and very fast service!', date('Y-m-d H:i:s')]);
      $ir->execute(['Sara', 5, 'The coffee and the dates are amazing, highly recommended.', date('Y-m-d H:i:s', time() - 60)]);
    }
    $set('catalog_seeded', '1');
  }
  $set('schema_v', SCHEMA_VERSION);
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function settings($fresh = false) {
  static $s;
  if ($s === null || $fresh) { $s = []; foreach (db()->query('SELECT k,v FROM settings') as $r) $s[$r['k']] = $r['v']; }
  return $s;
}
function set_setting($k, $v) {
  db()->prepare('INSERT INTO settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)')->execute([$k, $v]);
}
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_check() { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); die('Invalid request'); } }
function client_ip() {
  foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR'] as $h) {
    if (!empty($_SERVER[$h])) { $v = trim(explode(',', $_SERVER[$h])[0]); if (filter_var($v, FILTER_VALIDATE_IP)) return $v; }
  }
  return (string)($_SERVER['REMOTE_ADDR'] ?? '');
}
function visitor_ip() { return flag_on(settings(), 'store_ip') ? client_ip() : ''; }
function ip_hash() { return hash('sha256', client_ip() . cfg('salt')); }
function is_bot() { $ua = $_SERVER['HTTP_USER_AGENT'] ?? ''; return $ua === '' || preg_match('/bot|crawl|spider|slurp|facebookexternalhit|facebot|preview|monitor|whatsapp\\/|embedly|curl|wget|python-requests/i', $ua); }
function detect_os() {
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  if (preg_match('/iPhone|iPod/i', $ua)) return 'iphone';
  if (preg_match('/iPad/i', $ua)) return 'ipad';
  if (preg_match('/Android/i', $ua)) return preg_match('/Mobile/i', $ua) ? 'android' : 'android_tablet';
  if (preg_match('/CrOS/i', $ua)) return 'chromeos';
  if (preg_match('/Windows/i', $ua)) return 'windows';
  if (preg_match('/Macintosh|Mac OS X/i', $ua)) return 'mac';
  if (preg_match('/Linux|X11/i', $ua)) return 'linux';
  return 'other';
}
// Readable device name: "Android phone", "iPhone", "Windows PC" ... (older visits without OS fall back to Mobile / Desktop / Tablet)
function os_label($os, $device = '') {
  if ($os !== '' && isset(STR['en']['os_' . $os])) return t('os_' . $os);
  return in_array($device, ['mobile', 'desktop', 'tablet'], true) ? t($device) : t('unknown');
}
function device_type() {
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  return preg_match('/ipad|tablet/i', $ua) ? 'tablet' : (preg_match('/mobile|iphone|android/i', $ua) ? 'mobile' : 'desktop');
}
function link_href($type, $value) {
  $v = trim($value);
  switch ($type) {
    case 'whatsapp': return 'https://wa.me/' . preg_replace('/\D/', '', $v);
    case 'phone':    return 'tel:' . preg_replace('/[^\d+]/', '', $v);
    case 'email':    return 'mailto:' . $v;
    case 'snapchat': return preg_match('#^https?://#i', $v) ? $v : 'https://www.snapchat.com/add/' . ltrim($v, '@');
    case 'tiktok':   return preg_match('#^https?://#i', $v) ? $v : 'https://www.tiktok.com/@' . ltrim($v, '@');
    default:         return $v;
  }
}
function safe_url($u) { return preg_match('#^(https?://|tel:|mailto:)#i', $u) ? $u : '#'; }
function is_url($v) { return (bool)preg_match('#^https?://#i', (string)$v); }
// Image is either an uploaded file name (in /uploads) or a full http(s) link.
function img_src($v, $base = 'uploads/') { $v = (string)$v; if ($v === '') return ''; return is_url($v) ? $v : $base . rawurlencode($v); }
function save_image($f, $prefix) {
  if (empty($f['tmp_name']) || $f['error'] !== UPLOAD_ERR_OK || $f['size'] > 4 * 1024 * 1024) return null;
  $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
  $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/x-icon' => 'ico', 'image/vnd.microsoft.icon' => 'ico'][$mime] ?? null;
  if (!$ext) return null;
  $name = $prefix . '-' . bin2hex(random_bytes(5)) . '.' . $ext;
  return move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $name) ? $name : null;
}
function delete_upload($name) {
  $name = (string)$name;
  if ($name === '' || is_url($name)) return;
  $name = basename($name);
  if (is_file(UPLOAD_DIR . $name)) @unlink(UPLOAD_DIR . $name);
}
// "lat,lng" -> [lat,lng] or null
function parse_coords($s) {
  if (!preg_match('/^\s*(-?\d{1,2}(?:\.\d+)?)\s*[,\s]\s*(-?\d{1,3}(?:\.\d+)?)\s*$/', (string)$s, $m)) return null;
  $la = (float)$m[1]; $lo = (float)$m[2];
  return ($la >= -90 && $la <= 90 && $lo >= -180 && $lo <= 180) ? [$la, $lo] : null;
}
// Cleans a UTM value (lowercase letters, numbers, - _ .)
function clean_tag($v) { return substr(preg_replace('/[^a-z0-9_.\-]/', '', strtolower(trim((string)$v))), 0, 60); }
// Where did the visitor come from?
// 1) tracking links: ?utm_source=..&utm_medium=..&utm_campaign=..  (or the older ?src=name)
// 2) the referrer website  3) the in-app browser (Facebook, Instagram, Snapchat, TikTok, X)  4) direct
// Returns [category, detail, utm_source, utm_medium, utm_campaign]
function classify_source() {
  $socialNames = ['facebook', 'instagram', 'tiktok', 'snapchat', 'whatsapp', 'x', 'twitter', 'youtube', 'telegram', 'linkedin', 'pinterest', 'reddit', 'threads'];
  $us = clean_tag($_GET['utm_source'] ?? ''); $um = clean_tag($_GET['utm_medium'] ?? ''); $uc = clean_tag($_GET['utm_campaign'] ?? '');
  $legacy = clean_tag($_GET['src'] ?? '');
  if ($us === '' && $legacy !== '') { $us = $legacy; if ($um === '' && strpos($legacy, 'qr') === 0) $um = 'qr'; }
  if ($us !== '') {
    $cat = 'campaign';
    if ($um === 'qr' || strpos($us, 'qr') === 0) $cat = 'qr';
    elseif (in_array($um, ['email', 'e-mail', 'newsletter'], true) || $us === 'email') $cat = 'email';
    elseif (in_array($um, ['social', 'social-media', 'paid-social', 'sm'], true) || in_array($us, $socialNames, true)) $cat = 'social';
    return [$cat, $us, $us, $um, $uc];
  }
  $host = preg_replace('/^www\./', '', strtolower((string)parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST)));
  $self = preg_replace('/^www\./', '', strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '')));
  if ($host !== '' && $host !== $self) {
    if (preg_match('/(^|\.)(google|bing|yahoo|duckduckgo|baidu|yandex|ecosia)\./', $host, $m)) return ['search', $m[2], '', '', ''];
    $social = ['facebook' => 'facebook', 'fb.com' => 'facebook', 'instagram' => 'instagram', 'tiktok' => 'tiktok', 'snapchat' => 'snapchat', 'twitter' => 'x', 't.co' => 'x', 'x.com' => 'x',
               'youtube' => 'youtube', 'youtu.be' => 'youtube', 'linkedin' => 'linkedin', 'pinterest' => 'pinterest', 'telegram' => 'telegram', 't.me' => 'telegram', 'whatsapp' => 'whatsapp', 'wa.me' => 'whatsapp', 'reddit' => 'reddit'];
    foreach ($social as $k => $n) { if (strpos($host, $k) !== false) return ['social', $n, '', '', '']; }
    return ['referral', substr($host, 0, 190), '', '', ''];
  }
  // No referrer (chat apps hide it). In-app browsers still identify themselves in the user agent.
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  foreach (['/FBAN|FBAV|FB_IAB/' => 'facebook', '/Instagram/' => 'instagram', '/Snapchat/' => 'snapchat', '/musical_ly|TikTok|Bytedance/i' => 'tiktok', '/Twitter/' => 'x', '/Telegram/i' => 'telegram'] as $re => $n) {
    if (preg_match($re, $ua)) return ['social', $n, '', '', ''];
  }
  return ['direct', '', '', '', ''];
}
// Logs a new visit. A visitor counts once per 30 minutes, but arriving through a different tracking link counts again.
// Returns the visit id, or 0 if nothing was logged.
function log_visit() {
  if (is_bot()) return 0;
  list($src, $det, $us, $um, $uc) = classify_source();
  $key = $us !== '' ? substr(md5($us . '|' . $um . '|' . $uc), 0, 8) : '1';
  if (isset($_COOKIE['kv']) && ($key === '1' || $_COOKIE['kv'] === $key)) return 0;
  setcookie('kv', $key, time() + 1800, '/', '', false, true);
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? ''; $browser = 'other';
  foreach (['Edg' => 'Edge', 'OPR' => 'Opera', 'Chrome' => 'Chrome', 'CriOS' => 'Chrome', 'Firefox' => 'Firefox', 'Safari' => 'Safari'] as $k => $n) {
    if (stripos($ua, $k) !== false) { $browser = $n; break; }
  }
  $ref = substr((string)parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST), 0, 190);
  db()->prepare('INSERT INTO visits (visited_at,day,ip_hash,device,browser,referrer,source,source_detail,utm_source,utm_medium,utm_campaign,os,ip) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
    ->execute([date('Y-m-d H:i:s'), date('Y-m-d'), ip_hash(), device_type(), $browser, $ref, $src, $det, $us, $um, $uc, detect_os(), visitor_ip()]);
  $id = (int)db()->lastInsertId();
  setcookie('kvid', (string)$id, time() + 1800, '/', '', false, true);
  return $id;
}
function online_count() {
  $q = db()->prepare('SELECT COUNT(*) FROM online WHERE last_seen >= ?');
  $q->execute([date('Y-m-d H:i:s', time() - 75)]);
  return (int)$q->fetchColumn();
}
// Login throttling: 5 wrong attempts => blocked for 10 minutes (important because passwords are short PINs)
function login_blocked() {
  $q = db()->prepare('SELECT tries,last_try FROM login_fails WHERE ip_hash=?'); $q->execute([ip_hash()]); $r = $q->fetch();
  if (!$r) return false;
  if (strtotime($r['last_try']) < time() - 600) { login_ok(); return false; }
  return (int)$r['tries'] >= 5;
}
function login_fail() {
  db()->prepare('INSERT INTO login_fails (ip_hash,tries,last_try) VALUES (?,1,?) ON DUPLICATE KEY UPDATE tries=tries+1,last_try=VALUES(last_try)')->execute([ip_hash(), date('Y-m-d H:i:s')]);
}
function login_ok() { db()->prepare('DELETE FROM login_fails WHERE ip_hash=?')->execute([ip_hash()]); }
function valid_pin($p) { return (bool)preg_match('/^\d{4,6}$/', (string)$p); }

// Absolute site URL (needed for link previews: social apps require full image links)
function base_url() {
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
  $host = preg_replace('/[^a-z0-9.\-:]/i', '', $_SERVER['HTTP_HOST'] ?? '');
  $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
  return ($https ? 'https' : 'http') . '://' . $host . $dir . '/';
}
function abs_img($v) { $v = (string)$v; if ($v === '') return ''; return is_url($v) ? $v : base_url() . 'uploads/' . rawurlencode($v); }

// Country flag emoji from a 2-letter code (as HTML entities)
function flag_html($cc) {
  $cc = strtoupper((string)$cc);
  if (!preg_match('/^[A-Z]{2}$/', $cc)) return '';
  return '&#' . (127397 + ord($cc[0])) . ';&#' . (127397 + ord($cc[1])) . ';';
}
function site_url() { return preg_replace('#admin/$#', '', base_url()); }

function hex_or($v, $d = '') { $v = trim((string)$v); return preg_match('/^#[0-9a-fA-F]{6}$/', $v) ? $v : $d; }
function flag_on($s, $k) { return ($s[$k] ?? '0') === '1'; }

// Footer used on every page (public, admin, login). It is always shown and can not be changed from the admin.
function footer_html() {
  return '<footer class="foot">' . e(t('developed_by')) . ' <a href="https://amin.free.nf" target="_blank" rel="noopener">Mr. Amin</a></footer>';
}

const HEADING_POSITIONS = ['top_bar', 'after_profile', 'before_links', 'after_links', 'before_products', 'after_products', 'before_hours', 'after_hours', 'before_reviews', 'after_reviews', 'before_map', 'after_map', 'before_contact', 'after_contact', 'bottom'];
// Top bars (colored strips above the cover)
function render_bars($list, $s) {
  foreach ($list as $h) {
    $u = trim($h['url']); $tag = $u !== '' ? 'a' : 'div';
    $bg = hex_or($h['bg_color'] ?? '', hex_or($s['headline_bg'] ?? '', '#A47735')); $fg = hex_or($h['text_color'] ?? '', hex_or($s['headline_fg'] ?? '', '#FFFFFF'));
    echo '<' . $tag . ' class="headline' . (flag_on($s, 'headline_scroll') ? ' scroll' : '') . '"' . ($tag === 'a' ? ' href="' . e(safe_url($u)) . '"' : '') . ' style="background:' . e($bg) . ';color:' . e($fg) . '"><span class="hl-track"><span>' . e(pick($h, 'text')) . '</span></span></' . $tag . ">\n";
  }
}
// Text color that stays readable on a given background color
function contrast_color($hex) {
  $h = ltrim($hex, '#'); $r = hexdec(substr($h, 0, 2)); $g = hexdec(substr($h, 2, 2)); $b = hexdec(substr($h, 4, 2));
  return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160 ? '#241B12' : '#FFFFFF';
}
// Normal headings placed around the sections of the page (text color and background color are both optional)
function render_headings($list) {
  foreach ($list as $h) {
    $al = in_array($h['align'], ['start', 'center', 'end'], true) ? $h['align'] : 'center'; $sz = in_array($h['size'], ['sm', 'md', 'lg', 'xl', 'xxl'], true) ? $h['size'] : 'md';
    $fg = hex_or($h['text_color'] ?? ''); $bg = hex_or($h['bg_color'] ?? '');
    if ($bg !== '' && $fg === '') $fg = contrast_color($bg);
    $st = ($bg !== '' ? 'background:' . $bg . ';' : '') . ($fg !== '' ? 'color:' . $fg . ';' : '');
    $t = e(pick($h, 'text')); $u = trim($h['url']); $sub = pick($h, 'sub');
    echo '<div class="hdwrap hd-' . $al . ' hd-' . $sz . ($bg !== '' ? ' has-bg' : '') . '"' . ($st !== '' ? ' style="' . e($st) . '"' : '') . '><h2 class="hd-title">' . ($u !== '' ? '<a href="' . e(safe_url($u)) . '" style="color:inherit">' . $t . '</a>' : $t) . '</h2>' . ($sub !== '' ? '<p class="hd-sub"' . ($fg !== '' ? ' style="color:inherit;opacity:.85"' : '') . '>' . e($sub) . '</p>' : '') . "</div>\n";
  }
}
function preloader_html($src) { return '<div id="preloader" aria-hidden="true"><div class="pl"><span class="ring"></span><img src="' . e($src) . '" alt=""></div></div>'; }
function admin_pre($rel = '../') {
  $s = settings();
  if (!flag_on($s, 'admin_preloader_enabled')) return '';
  return preloader_html(!empty($s['profile']) ? img_src($s['profile'], $rel . 'uploads/') : $rel . 'assets/logo.png');
}

const BANNER_POSITIONS = ['top', 'after_profile', 'before_links', 'after_links', 'before_products', 'after_products', 'before_hours', 'after_hours', 'before_reviews', 'after_reviews', 'before_map', 'after_map', 'before_contact', 'after_contact', 'bottom'];
const RATIOS = ['3/1' => 'r_3_1', '2/1' => 'r_2_1', '16/9' => 'r_16_9', '4/3' => 'r_4_3', '1/1' => 'r_1_1'];
// Banner sliders placed at one position of the page ($list = sliders, each with a 'slides' array)
function render_sliders($list) {
  foreach ($list as $sl) {
    if (empty($sl['slides'])) continue;
    $ratio = isset(RATIOS[$sl['ratio']]) ? $sl['ratio'] : '3/1'; $fit = $sl['fit'] === 'contain' ? 'contain' : 'cover'; $n = count($sl['slides']);
    echo '<section class="bslider fit-' . $fit . '" style="--ratio:' . e($ratio) . '" data-autoplay="' . ((int)$sl['autoplay'] && $n > 1 ? 1 : 0) . '" data-interval="' . (int)$sl['interval_s'] . '" data-dots="' . ((int)$sl['dots'] && $n > 1 ? 1 : 0) . '">';
    echo '<div class="bs-viewport"><div class="bs-track">';
    foreach ($sl['slides'] as $sd) {
      $cap = pick($sd, 'caption'); $img = '<img src="' . e(img_src($sd['image'])) . '" alt="' . e($cap) . '" loading="lazy">' . ($cap !== '' ? '<span class="bs-cap">' . e($cap) . '</span>' : '');
      echo trim($sd['url']) !== '' ? '<a class="bs-slide" href="track.php?k=b&amp;id=' . (int)$sd['id'] . '">' . $img . '</a>' : '<div class="bs-slide">' . $img . '</div>';
    }
    echo '</div></div>';
    if ((int)$sl['arrows'] && $n > 1) {
      $ch = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>';
      echo '<button type="button" class="bs-nav prev" aria-label="' . e(t('prev')) . '">' . $ch . '</button><button type="button" class="bs-nav next" aria-label="' . e(t('next')) . '">' . $ch . '</button>';
    }
    echo '<div class="bs-dots"></div></section>' . "\n";
  }
}

// ---- Saudi riyal symbol in prices: "25 SAR" / "25 SR" / "25 ر.س" / "25 ﷼" become the new official symbol (assets/sar.svg)
function price_html($p) {
  $sym = '<span class="sar" role="img" aria-label="SAR"></span>';
  $tok = '(?:SAR|SR|ر\.س|﷼|\x{20C1}|\{SAR\})';
  if (preg_match('/^\s*(' . $tok . ')?\s*([\d.,٠-٩]+)\s*(' . $tok . ')?\s*$/iu', (string)$p, $m) && (($m[1] ?? '') !== '' || ($m[3] ?? '') !== '')) {
    return '<span class="pr" dir="ltr">' . $sym . ' ' . e($m[2]) . '</span>';   // symbol to the left of the number (SAMA rule)
  }
  return preg_replace('/(?<![A-Za-z])' . $tok . '(?![A-Za-z])/iu', $sym, e($p));
}

// ---- Opening hours
function hours_rows() { static $r; if ($r === null) { $r = []; foreach (db()->query('SELECT * FROM hours')->fetchAll() as $x) $r[(int)$x['day']] = $x; } return $r; }
function fmt_time($t, $fmt) {
  if (!preg_match('/^(\d{2}):(\d{2})$/', $t, $m)) return '';
  if ($fmt === '24') return $t;
  $h = (int)$m[1]; $ap = L() === 'ar' ? ($h < 12 ? 'ص' : 'م') : ($h < 12 ? 'AM' : 'PM'); $h12 = $h % 12; if ($h12 === 0) $h12 = 12;
  return $h12 . ':' . $m[2] . ' ' . $ap;
}
function hours_periods($row) {
  if (!$row || (int)$row['closed']) return [];
  if ((int)$row['all_day']) return [['00:00', '24:00']];
  $o = [];
  foreach ([['open1', 'close1'], ['open2', 'close2']] as $k) if ($row[$k[0]] !== '' && $row[$k[1]] !== '') $o[] = [$row[$k[0]], $row[$k[1]]];
  return $o;
}
function t_min($t) { $p = explode(':', $t); return (int)$p[0] * 60 + (int)$p[1]; }
function hours_open_now($rows) {
  $dow = (int)date('w'); $now = (int)date('G') * 60 + (int)date('i');
  foreach (hours_periods($rows[$dow] ?? null) as $p) {
    $o = t_min($p[0]); $c = t_min($p[1]);
    if ($c > $o ? ($now >= $o && $now < $c) : $now >= $o) return true;       // closing after midnight: open until the end of today
  }
  foreach (hours_periods($rows[($dow + 6) % 7] ?? null) as $p) {            // yesterday's shift that ends after midnight
    $o = t_min($p[0]); $c = t_min($p[1]);
    if ($c <= $o && $now < $c) return true;
  }
  return false;
}
function hours_html($s) {
  if (!flag_on($s, 'hours_enabled')) return '';
  $rows = hours_rows(); if (!$rows) return '';
  $fmt = ($s['hours_format'] ?? '12') === '24' ? '24' : '12'; $start = ((int)($s['hours_start'] ?? 6)) % 7; $today = (int)date('w');
  $h = '<section class="hours"><div class="hours-head"><span class="mi"><svg><use href="#i-clock"/></svg></span><h2>' . e(pick($s, 'hours_title')) . '</h2>';
  if (flag_on($s, 'hours_badge')) { $open = hours_open_now($rows); $h .= '<span class="hbadge' . ($open ? '' : ' off') . '">' . e(t($open ? 'open_now' : 'closed_now')) . '</span>'; }
  $h .= '</div><ul>';
  for ($i = 0; $i < 7; $i++) {
    $d = ($start + $i) % 7; $per = hours_periods($rows[$d] ?? null);
    if (!$per) $v = '<span class="hc">' . e(t('closed')) . '</span>';
    elseif ($per[0][0] === '00:00' && $per[0][1] === '24:00') $v = e(t('open_24h'));
    else { $parts = []; foreach ($per as $p) $parts[] = '<bdi>' . e(fmt_time($p[0], $fmt)) . '</bdi> – <bdi>' . e(fmt_time($p[1], $fmt)) . '</bdi>'; $v = implode('<br>', $parts); }
    $h .= '<li' . ($d === $today ? ' class="today"' : '') . '><span>' . e(t('day_' . $d)) . '</span><span class="ht">' . $v . '</span></li>';
  }
  return $h . '</ul></section>' . "\n";
}

// ---- Currencies (symbol, placement, names). SAR uses the new official riyal symbol image.
const CURRENCIES = [
  'SAR' => ['sym' => 'SAR', 'pos' => 'before', 'en' => 'Saudi riyal', 'ar' => 'ريال سعودي'],
  'AED' => ['sym' => 'AED', 'sym_ar' => 'د.إ', 'pos' => 'after', 'en' => 'UAE dirham', 'ar' => 'درهم إماراتي'],
  'BDT' => ['sym' => '৳', 'pos' => 'before', 'en' => 'Bangladeshi taka', 'ar' => 'تاكا بنغلاديشي'],
  'USD' => ['sym' => '$', 'pos' => 'before', 'en' => 'US dollar', 'ar' => 'دولار أمريكي'],
  'EUR' => ['sym' => '€', 'pos' => 'before', 'en' => 'Euro', 'ar' => 'يورو'],
  'GBP' => ['sym' => '£', 'pos' => 'before', 'en' => 'British pound', 'ar' => 'جنيه إسترليني'],
  'KWD' => ['sym' => 'KWD', 'sym_ar' => 'د.ك', 'pos' => 'after', 'en' => 'Kuwaiti dinar', 'ar' => 'دينار كويتي'],
  'QAR' => ['sym' => 'QAR', 'sym_ar' => 'ر.ق', 'pos' => 'after', 'en' => 'Qatari riyal', 'ar' => 'ريال قطري'],
  'BHD' => ['sym' => 'BD', 'sym_ar' => 'د.ب', 'pos' => 'before', 'pos_ar' => 'after', 'en' => 'Bahraini dinar', 'ar' => 'دينار بحريني'],
  'OMR' => ['sym' => 'OMR', 'sym_ar' => 'ر.ع.', 'pos' => 'after', 'en' => 'Omani rial', 'ar' => 'ريال عماني'],
  'EGP' => ['sym' => 'EGP', 'sym_ar' => 'ج.م', 'pos' => 'after', 'en' => 'Egyptian pound', 'ar' => 'جنيه مصري'],
  'JOD' => ['sym' => 'JOD', 'sym_ar' => 'د.أ', 'pos' => 'after', 'en' => 'Jordanian dinar', 'ar' => 'دينار أردني'],
  'INR' => ['sym' => '₹', 'pos' => 'before', 'en' => 'Indian rupee', 'ar' => 'روبية هندية'],
  'PKR' => ['sym' => 'Rs', 'pos' => 'before', 'en' => 'Pakistani rupee', 'ar' => 'روبية باكستانية'],
  'TRY' => ['sym' => '₺', 'pos' => 'before', 'en' => 'Turkish lira', 'ar' => 'ليرة تركية'],
  'MYR' => ['sym' => 'RM', 'pos' => 'before', 'en' => 'Malaysian ringgit', 'ar' => 'رينغيت ماليزي'],
  'IDR' => ['sym' => 'Rp', 'pos' => 'before', 'en' => 'Indonesian rupiah', 'ar' => 'روبية إندونيسية'],
];
function currency_label($code) { $c = CURRENCIES[$code] ?? null; return $c ? $code . ' — ' . $c[L()] . ' (' . ($c['sym']) . ')' : $code; }
function fmt_amount($a) { $a = (float)$a; return number_format($a, abs($a - round($a)) < 0.005 ? 0 : 2, '.', ','); }
// One amount with its currency symbol, e.g. "৳ 250" or "25 AED" (always read left-to-right; SAR uses the new riyal symbol)
function money_html($amt, $cur) {
  $c = CURRENCIES[$cur] ?? null; $n = e(fmt_amount($amt));
  if (!$c) return '<span class="pr" dir="ltr">' . $n . '</span>';
  $sym = $cur === 'SAR' ? '<span class="sar" role="img" aria-label="SAR"></span>' : e((L() === 'ar' && isset($c['sym_ar'])) ? $c['sym_ar'] : $c['sym']);
  $pos = (L() === 'ar' && isset($c['pos_ar'])) ? $c['pos_ar'] : $c['pos'];
  return '<span class="pr" dir="ltr">' . ($pos === 'before' ? $sym . ' ' . $n : $n . ' ' . $sym) . '</span>';
}
function money_text($amt, $cur) { return fmt_amount($amt) . ($cur !== '' ? ' ' . $cur : ''); }
function product_currency($p, $s) { $c = (string)($p['currency'] ?? ''); if (!isset(CURRENCIES[$c])) $c = isset(CURRENCIES[$s['currency_default'] ?? '']) ? $s['currency_default'] : 'SAR'; return $c; }
// Price block of a product: [html, discount percent]. Sale price shows with the regular price struck through.
// A sale is on when there is a valid sale price lower than the regular price and "now" is inside the optional start / end dates
function sale_active($p) {
  $reg = $p['reg_price'] ?? null; $sale = $p['sale_price'] ?? null;
  if ($reg === null || $reg === '' || $sale === null || $sale === '' || (float)$sale <= 0 || (float)$sale >= (float)$reg) return false;
  $n = time();
  if (!empty($p['sale_start']) && strtotime($p['sale_start']) > $n) return false;
  if (!empty($p['sale_end']) && strtotime($p['sale_end']) < $n) return false;
  return true;
}
// Price block of a product: [html, discount percent, countdown html]
function product_price($p, $s) {
  $cur = product_currency($p, $s); $reg = $p['reg_price'] ?? null;
  if ($reg !== null && $reg !== '') {
    $reg = (float)$reg;
    if (sale_active($p)) {
      $sale = (float)$p['sale_price']; $cd = '';
      $cdm = (int)($p['countdown'] ?? 0);   // 0 = follow the default, 1 = show, 2 = hide
      if (!empty($p['sale_end']) && ($cdm === 1 || ($cdm === 0 && flag_on($s, 'countdown_default')))) {
        $end = (int)strtotime($p['sale_end']); $left = max(0, $end - time()); $dd = intdiv($left, 86400);
        $init = ($dd > 0 ? $dd . t('cd_d') . ' ' : '') . sprintf('%02d:%02d:%02d', intdiv($left % 86400, 3600), intdiv($left % 3600, 60), $left % 60);
        $cd = '<div class="cd" data-end="' . $end . '" data-u="' . e(t('cd_d')) . '"><span class="cd-l">' . e(t('sale_ends_in')) . '</span> <b class="cd-t">' . e($init) . '</b></div>';
      }
      return ['<span class="price-now">' . money_html($sale, $cur) . '</span> <del class="price-old">' . money_html($reg, $cur) . '</del>', (int)round(($reg - $sale) / $reg * 100), $cd];
    }
    return ['<span class="price-now">' . money_html($reg, $cur) . '</span>', 0, ''];
  }
  return [trim((string)($p['price'] ?? '')) !== '' ? price_html($p['price']) : '', 0, ''];   // custom price text (e.g. "Free", "Ask for price")
}
function product_price_text($p, $s) {
  $cur = product_currency($p, $s); $reg = $p['reg_price'] ?? null; $sale = $p['sale_price'] ?? null;
  if ($reg !== null && $reg !== '') {
    if (sale_active($p)) return money_text($sale, $cur) . '  (was ' . money_text($reg, $cur) . ')';
    return money_text($reg, $cur) . (($sale !== null && $sale !== '') ? '  [' . t('sale_off') . ': ' . money_text($sale, $cur) . ']' : '');
  }
  return (string)($p['price'] ?? '');
}
// Plain text price (for the WhatsApp order message)
function product_price_plain($p, $s) {
  $cur = product_currency($p, $s); $reg = $p['reg_price'] ?? null;
  if ($reg !== null && $reg !== '') return money_text(sale_active($p) ? $p['sale_price'] : $reg, $cur);
  return (string)($p['price'] ?? '');
}


// ---- Orders: WhatsApp / order form
function wa_number($s) {
  $n = preg_replace('/\D/', '', (string)($s['wa_number'] ?? ''));
  if ($n === '') { static $fb; if ($fb === null) $fb = preg_replace('/\D/', '', (string)db()->query("SELECT value FROM links WHERE type='whatsapp' AND active=1 ORDER BY sort,id LIMIT 1")->fetchColumn()); $n = $fb; }
  return $n;
}
function product_mode($p, $s) {
  $ok = ['link', 'whatsapp', 'form']; $m = $p['buy_mode'] ?? '';
  if (!in_array($m, $ok, true)) $m = $s['buy_mode'] ?? 'link';
  return in_array($m, $ok, true) ? $m : 'link';
}
function wa_order_url($p, $s) {
  $n = wa_number($s); if ($n === '') return '';
  $tpl = pick($s, 'wa_msg'); if ($tpl === '') $tpl = 'Hello, I want to order: {name} - {price}';
  $msg = strtr($tpl, ['{name}' => pick($p, 'name'), '{price}' => product_price_plain($p, $s), '{shop}' => pick($s, 'shop_name')]);
  return 'https://wa.me/' . $n . '?text=' . rawurlencode($msg);
}
function badge_counts() {
  try { return ['rev' => (int)db()->query('SELECT COUNT(*) FROM reviews WHERE approved=0')->fetchColumn(), 'msg' => (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read=0')->fetchColumn()]; }
  catch (Throwable $e) { return ['rev' => 0, 'msg' => 0]; }
}
function stars_text($n) { $n = max(0, min(5, (int)$n)); return str_repeat('★', $n) . str_repeat('☆', 5 - $n); }
function ip_recent($table, $seconds) {
  $q = db()->prepare("SELECT COUNT(*) FROM `$table` WHERE ip_hash=? AND created_at>=?"); $q->execute([ip_hash(), date('Y-m-d H:i:s', time() - $seconds)]); return (int)$q->fetchColumn();
}

// ---- Customer reviews section
function reviews_html($s) {
  if (!flag_on($s, 'reviews_enabled')) return '';
  $lim = max(1, min(30, (int)($s['reviews_limit'] ?? 8)));
  $rows = db()->query('SELECT * FROM reviews WHERE approved=1 ORDER BY id DESC LIMIT ' . $lim)->fetchAll();
  $submit = flag_on($s, 'reviews_submit');
  if (!$rows && !$submit) return '';
  $h = '<section class="reviews"><h2 class="rv-title">' . e(t('reviews_title')) . '</h2>';
  if ($rows) {
    $agg = db()->query('SELECT COUNT(*) c, AVG(rating) a FROM reviews WHERE approved=1')->fetch();
    $h .= '<p class="rv-avg"><span class="rv-stars" aria-hidden="true">' . stars_text(round((float)$agg['a'])) . '</span> <b>' . number_format((float)$agg['a'], 1) . '</b> <span class="muted">(' . (int)$agg['c'] . ')</span></p><div class="rv-track">';
    foreach ($rows as $r) $h .= '<figure class="rv-card"><div class="rv-stars" role="img" aria-label="' . (int)$r['rating'] . '/5">' . stars_text($r['rating']) . '</div><blockquote>' . e($r['text']) . '</blockquote><figcaption>' . e($r['name']) . '</figcaption></figure>';
    $h .= '</div>';
  }
  if ($submit) {
    $h .= '<div class="rv-actions"><button type="button" class="rv-open">' . e(t('write_review')) . '</button></div>'
      . '<form class="rv-form pform" hidden data-url="review.php" data-ok="' . e(t('review_thanks')) . '" data-err="' . e(t('review_err')) . '" data-rate="' . e(t('rate_err')) . '">'
      . '<input name="name" required maxlength="80" placeholder="' . e(t('your_name')) . '" aria-label="' . e(t('your_name')) . '">'
      . '<select name="rating" aria-label="' . e(t('rating_label')) . '">';
    for ($i = 5; $i >= 1; $i--) $h .= '<option value="' . $i . '">' . stars_text($i) . '</option>';
    $h .= '</select><textarea name="text" required maxlength="500" rows="3" placeholder="' . e(t('your_review')) . '" aria-label="' . e(t('your_review')) . '"></textarea>'
      . '<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true"><button type="submit" class="pbtn">' . e(t('send')) . '</button><p class="pmsg" role="status"></p></form>';
  }
  return $h . '</section>' . "\n";
}

// ---- Contact / order form
function contact_html($s, $products) {
  if (!flag_on($s, 'contact_enabled')) return '';
  $title = pick($s, 'contact_title'); if ($title === '') $title = t('contact_title');
  $h = '<section class="contact" id="contact"><h2 class="rv-title">' . e($title) . '</h2><form class="ct-form pform" data-url="contact.php" data-ok="' . e(t('c_thanks')) . '" data-err="' . e(t('c_err')) . '" data-need="' . e(t('c_need_contact')) . '" data-rate="' . e(t('rate_err')) . '">'
    . '<input name="name" required maxlength="100" placeholder="' . e(t('c_name')) . '" aria-label="' . e(t('c_name')) . '" autocomplete="name">'
    . '<div class="pf2"><input name="phone" maxlength="40" inputmode="tel" dir="ltr" placeholder="' . e(t('c_phone')) . '" aria-label="' . e(t('c_phone')) . '" autocomplete="tel"><input name="email" type="email" maxlength="120" dir="ltr" placeholder="' . e(t('c_email')) . '" aria-label="' . e(t('c_email')) . '" autocomplete="email"></div>';
  if ($products) {
    $h .= '<select name="product" id="ct_product" aria-label="' . e(t('c_product')) . '"><option value="0">' . e(t('c_product_none')) . '</option>';
    foreach ($products as $p) $h .= '<option value="' . (int)$p['id'] . '">' . e(pick($p, 'name')) . '</option>';
    $h .= '</select>';
  }
  return $h . '<textarea name="message" required maxlength="1000" rows="4" placeholder="' . e(t('c_message')) . '" aria-label="' . e(t('c_message')) . '"></textarea>'
    . '<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true"><button type="submit" class="pbtn">' . e(t('c_send')) . '</button><p class="pmsg" role="status"></p></form></section>' . "\n";
}

// ---- Structured data for Google (business name, location, phone, opening hours, social profiles)
function jsonld_html($s) {
  $o = ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => pick($s, 'shop_name'), 'url' => base_url()];
  $d = trim(pick($s, 'seo_desc')); if ($d === '') $d = trim(pick($s, 'tagline')); if ($d !== '') $o['description'] = $d;
  $img = abs_img($s['og_image'] ?? '') ?: (abs_img($s['cover'] ?? '') ?: abs_img($s['profile'] ?? '')); if ($img) $o['image'] = $img;
  $co = parse_coords($s['map_coords'] ?? ''); if ($co) $o['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $co[0], 'longitude' => $co[1]];
  $same = [];
  foreach (db()->query('SELECT type,value FROM links WHERE active=1')->fetchAll() as $l) {
    if ($l['type'] === 'phone' && empty($o['telephone'])) $o['telephone'] = preg_replace('/[^\d+]/', '', $l['value']);
    if (in_array($l['type'], ['tiktok', 'snapchat'], true)) $same[] = link_href($l['type'], $l['value']);
  }
  if ($same) $o['sameAs'] = $same;
  $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; $spec = [];
  foreach (hours_rows() as $dn => $row) foreach (hours_periods($row) as $per) $spec[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $days[$dn], 'opens' => $per[0], 'closes' => $per[1] === '24:00' ? '23:59' : $per[1]];
  if ($spec) $o['openingHoursSpecification'] = $spec;
  return '<script type="application/ld+json">' . json_encode($o, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>';
}

// file + "?v=<time>" so a changed CSS/JS file is never served from an old cache
function asset_v($rel) { return $rel . '?v=' . (int)@filemtime(__DIR__ . '/../' . $rel); }

try { L(); } catch (Throwable $e) {}
