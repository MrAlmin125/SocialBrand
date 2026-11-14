<?php
// Logs a click then redirects to the stored URL. The target always comes from the database (no open redirect).
require __DIR__ . '/includes/bootstrap.php';
$kind = $_GET['k'] ?? ''; $id = (int)($_GET['id'] ?? 0); $url = ''; $label = '';
if ($kind === 'l') {
  $q = db()->prepare('SELECT type,title_en,title_ar,value FROM links WHERE id=? AND active=1'); $q->execute([$id]); $r = $q->fetch();
  if ($r) { $url = link_href($r['type'], $r['value']); $label = $r['title_en'] ?: $r['title_ar']; }
} elseif ($kind === 'p') {
  $q = db()->prepare('SELECT name_en,name_ar,url FROM products WHERE id=? AND active=1'); $q->execute([$id]); $r = $q->fetch();
  if ($r) { $url = $r['url']; $label = $r['name_en'] ?: $r['name_ar']; }
} elseif ($kind === 'f') {
  $st = settings(); $n = wa_number($st);
  if ($n !== '') { $msg = pick($st, 'wa_float_msg'); $url = 'https://wa.me/' . $n . ($msg !== '' ? '?text=' . rawurlencode($msg) : ''); $label = 'WhatsApp floating button'; }
} elseif ($kind === 'w') {
  $q = db()->prepare('SELECT * FROM products WHERE id=? AND active=1'); $q->execute([$id]); $r = $q->fetch();
  if ($r) { $url = wa_order_url($r, settings()); $label = 'WhatsApp order: ' . ($r['name_en'] ?: $r['name_ar']); }
} elseif ($kind === 'm') {
  $st = settings(); $co = parse_coords($st['map_coords'] ?? '');
  if ($id === 2 && $co) { $url = 'https://www.google.com/maps/dir/?api=1&destination=' . $co[0] . ',' . $co[1]; $label = 'Map: Directions'; }
  else { $url = !empty($st['map_link']) ? $st['map_link'] : ($co ? 'https://www.google.com/maps?q=' . $co[0] . ',' . $co[1] : ''); $label = 'Map: Open in Google Maps'; }
} elseif ($kind === 'b') {
  $q = db()->prepare('SELECT s.url,s.caption_en,s.caption_ar,r.name FROM slides s JOIN sliders r ON r.id=s.slider_id WHERE s.id=? AND s.active=1 AND r.active=1'); $q->execute([$id]); $r = $q->fetch();
  if ($r) { $url = trim($r['url']); $label = trim(($r['name'] ?: 'Banner') . ($r['caption_en'] !== '' ? ': ' . $r['caption_en'] : ($r['caption_ar'] !== '' ? ': ' . $r['caption_ar'] : ''))); }
}
if ($url === '') { header('Location: index.php'); exit; }
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if ($ua !== '' && !preg_match('/bot|crawl|spider/i', $ua)) {
  db()->prepare('INSERT INTO clicks (clicked_at,day,kind,item_id,label,ip_hash) VALUES (?,?,?,?,?,?)')
    ->execute([date('Y-m-d H:i:s'), date('Y-m-d'), $kind, $id, $label, ip_hash()]);
}
header('Location: ' . safe_url($url)); exit;
