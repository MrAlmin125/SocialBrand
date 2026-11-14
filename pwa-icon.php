<?php
// App icon (180 / 192 / 512 px PNG) made from the favicon or the profile photo
require __DIR__ . '/includes/bootstrap.php';
$size = (int)($_GET['s'] ?? 192); if (!in_array($size, [180, 192, 512], true)) $size = 192;
$s = settings(); $f = (string)($s['favicon'] ?? ''); if ($f === '') $f = (string)($s['profile'] ?? '');
$path = ($f !== '' && !is_url($f) && is_file(UPLOAD_DIR . basename($f))) ? UPLOAD_DIR . basename($f) : __DIR__ . '/assets/logo.png';
header('Cache-Control: public, max-age=86400');
if (function_exists('imagecreatefromstring') && function_exists('imagepng')) {
  $src = @imagecreatefromstring((string)file_get_contents($path));
  if ($src) {
    $w = imagesx($src); $h = imagesy($src); $c = imagecreatetruecolor($size, $size);
    imagefilledrectangle($c, 0, 0, $size, $size, imagecolorallocate($c, 251, 247, 239));
    $box = (int)round($size * 0.8); $r = min($box / $w, $box / $h); $nw = max(1, (int)round($w * $r)); $nh = max(1, (int)round($h * $r));
    imagecopyresampled($c, $src, (int)(($size - $nw) / 2), (int)(($size - $nh) / 2), 0, 0, $nw, $nh, $w, $h);
    header('Content-Type: image/png'); imagepng($c); exit;
  }
}
header('Content-Type: ' . (new finfo(FILEINFO_MIME_TYPE))->file($path)); readfile($path);
