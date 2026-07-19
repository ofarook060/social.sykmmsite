<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? 0;

if (!is_numeric($notification_id) || $notification_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid notification ID']);
    exit;
}

$DB = new Database();
$check = $DB->read("select id from notification_seen where userid = '$userid' && notification_id = '$notification_id' limit 1");
if (!is_array($check)) {
    $DB->save("insert into notification_seen (userid, notification_id) values ('$userid', '$notification_id')");
}

echo json_encode(['success' => true, 'message' => 'Marked as seen']);
