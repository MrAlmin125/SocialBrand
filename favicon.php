<?php
// Serves the favicon chosen in Admin > Shop info (falls back to the profile photo, then the default icon).
require __DIR__ . '/includes/bootstrap.php';
$s = settings();
$f = (string)($s['favicon'] ?? ''); if ($f === '') $f = (string)($s['profile'] ?? '');
$path = ($f !== '' && !is_url($f) && is_file(UPLOAD_DIR . basename($f))) ? UPLOAD_DIR . basename($f) : __DIR__ . '/assets/favicon-32.png';
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
header('Content-Length: ' . filesize($path));
readfile($path);
