<?php
// Heartbeat from the public page (every 30s) used for the live "online now" counter + live locations.
require __DIR__ . '/includes/bootstrap.php';
if (is_bot()) { http_response_code(204); exit; }
$sid = $_COOKIE['ksid'] ?? '';
if (!preg_match('/^[a-f0-9]{32}$/', $sid)) { $sid = bin2hex(random_bytes(16)); setcookie('ksid', $sid, time() + 86400 * 30, '/', '', false, true); }
$country = $cc = $city = '';
$vid = (int)($_COOKIE['kvid'] ?? 0);
if ($vid > 0) {
  $q = db()->prepare('SELECT country,country_code,city FROM visits WHERE id=?'); $q->execute([$vid]);
  if ($g = $q->fetch()) { $country = $g['country']; $cc = $g['country_code']; $city = $g['city']; }
}
db()->prepare("INSERT INTO online (sid,last_seen,device,country,country_code,city,os,ip) VALUES (?,?,?,?,?,?,?,?)
  ON DUPLICATE KEY UPDATE last_seen=VALUES(last_seen), os=VALUES(os), ip=VALUES(ip), country=IF(VALUES(country)='',country,VALUES(country)), country_code=IF(VALUES(country_code)='',country_code,VALUES(country_code)), city=IF(VALUES(city)='',city,VALUES(city))")
  ->execute([$sid, date('Y-m-d H:i:s'), device_type(), $country, $cc, $city, detect_os(), visitor_ip()]);
if (random_int(1, 20) === 1) db()->prepare('DELETE FROM online WHERE last_seen < ?')->execute([date('Y-m-d H:i:s', time() - 3600)]);
http_response_code(204);
