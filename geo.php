<?php
// Receives the visitor's approximate location (looked up in their browser from their IP) for their own visit.
require __DIR__ . '/includes/bootstrap.php';
$vid = (int)($_COOKIE['kvid'] ?? 0);
$d = json_decode(file_get_contents('php://input'), true);
if ($vid > 0 && is_array($d) && !is_bot()) {
  $clean = function ($v, $n) { return mb_substr(trim(strip_tags((string)($v ?? ''))), 0, $n); };
  $country = $clean($d['country'] ?? '', 80); $cc = strtoupper($clean($d['cc'] ?? '', 2));
  if (!preg_match('/^[A-Z]{2}$/', $cc)) $cc = '';
  if ($country !== '') {
    db()->prepare("UPDATE visits SET country=?, country_code=?, region=?, city=? WHERE id=? AND country=''")
      ->execute([$country, $cc, $clean($d['region'] ?? '', 100), $clean($d['city'] ?? '', 100), $vid]);
  }
}
http_response_code(204);
