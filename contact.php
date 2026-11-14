<?php
// Contact / order form: saved in Admin > Messages, optional email alert
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function out($ok, $e = '') { echo json_encode(['ok' => $ok, 'e' => $e]); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'method');
$s = settings();
if (!flag_on($s, 'contact_enabled') || is_bot()) out(false, 'off');
if (trim($_POST['website'] ?? '') !== '') out(true);                    // honeypot
$cl = function ($v, $n) { return mb_substr(trim(strip_tags((string)$v)), 0, $n); };
$name = $cl($_POST['name'] ?? '', 100); $phone = $cl($_POST['phone'] ?? '', 40); $email = $cl($_POST['email'] ?? '', 120); $msg = $cl($_POST['message'] ?? '', 1000);
if ($name === '' || $msg === '') out(false, 'invalid');
if ($phone === '' && $email === '') out(false, 'need');
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) out(false, 'invalid');
if (ip_recent('messages', 3600) >= 5) out(false, 'rate');               // at most 5 messages per visitor per hour
$pid = (int)($_POST['product'] ?? 0); $pname = '';
if ($pid > 0) { $q = db()->prepare('SELECT name_en,name_ar FROM products WHERE id=?'); $q->execute([$pid]); if ($r = $q->fetch()) $pname = $r['name_en'] ?: $r['name_ar']; else $pid = 0; }
db()->prepare('INSERT INTO messages (name,phone,email,product_id,product_name,message,created_at,ip_hash,lang) VALUES (?,?,?,?,?,?,?,?,?)')->execute([$name, $phone, $email, $pid, $pname, $msg, date('Y-m-d H:i:s'), ip_hash(), L()]);
$to = trim($s['notify_email'] ?? '');
if (flag_on($s, 'notify_enabled') && filter_var($to, FILTER_VALIDATE_EMAIL)) {   // may be blocked by the host; the admin inbox always has the message
  $host = preg_replace('/[^a-z0-9.\-]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
  $body = "New message from your website\n\nName: $name\nPhone: $phone\nEmail: $email\nProduct: $pname\n\n$msg\n";
  $head = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nFrom: noreply@$host\r\n" . ($email !== '' ? "Reply-To: $email\r\n" : '');
  @mail($to, '=?UTF-8?B?' . base64_encode('New message: ' . $name) . '?=', $body, $head);
}
out(true);
