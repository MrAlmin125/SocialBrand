<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin'])) { http_response_code(403); exit; }
header('Content-Type: application/json'); header('Cache-Control: no-store');
$q = db()->prepare('SELECT country,country_code,city,device,os,ip FROM online WHERE last_seen >= ? ORDER BY last_seen DESC LIMIT 30');
$q->execute([date('Y-m-d H:i:s', time() - 75)]);
$vis = [];
foreach ($q->fetchAll() as $r) { $r['label'] = os_label($r['os'], $r['device']); $vis[] = $r; }
echo json_encode(['online' => online_count(), 'visitors' => $vis], JSON_UNESCAPED_UNICODE);
