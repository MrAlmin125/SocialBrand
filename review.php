<?php
// Visitor review (saved as "waiting for approval")
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function out($ok, $e = '') { echo json_encode(['ok' => $ok, 'e' => $e]); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'method');
$s = settings();
if (!flag_on($s, 'reviews_enabled') || !flag_on($s, 'reviews_submit') || is_bot()) out(false, 'off');
if (trim($_POST['website'] ?? '') !== '') out(true);                    // honeypot: bots get a fake "ok"
$name = mb_substr(trim(strip_tags((string)($_POST['name'] ?? ''))), 0, 80); $text = mb_substr(trim(strip_tags((string)($_POST['text'] ?? ''))), 0, 500);
$rate = max(1, min(5, (int)($_POST['rating'] ?? 5)));
if ($name === '' || mb_strlen($text) < 3) out(false, 'invalid');
if (ip_recent('reviews', 86400) >= 3) out(false, 'rate');               // at most 3 reviews per visitor per day
db()->prepare('INSERT INTO reviews (name,rating,text,approved,created_at,ip_hash) VALUES (?,?,?,0,?,?)')->execute([$name, $rate, $text, date('Y-m-d H:i:s'), ip_hash()]);
out(true);
