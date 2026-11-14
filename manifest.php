<?php
require __DIR__ . '/includes/bootstrap.php';
$s = settings();
if (!flag_on($s, 'pwa_enabled')) { http_response_code(404); exit; }
header('Content-Type: application/manifest+json; charset=utf-8'); header('Cache-Control: public, max-age=3600');
$name = pick($s, 'shop_name'); $short = pick($s, 'pwa_short'); if ($short === '') $short = mb_substr($name, 0, 12);
$v = urlencode((string)(($s['favicon'] ?? '') !== '' ? $s['favicon'] : ($s['profile'] ?? '')));
echo json_encode([
  'name' => $name, 'short_name' => $short, 'description' => pick($s, 'tagline'),
  'start_url' => './?utm_source=homescreen&utm_medium=pwa&utm_campaign=app', 'scope' => './', 'display' => 'standalone',
  'background_color' => '#FBF7EF', 'theme_color' => hex_or($s['pwa_color'] ?? '', '#A47735'), 'lang' => L(), 'dir' => dir_attr(),
  'icons' => [['src' => 'pwa-icon.php?s=192&v=' . $v, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'], ['src' => 'pwa-icon.php?s=512&v=' . $v, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any']],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
