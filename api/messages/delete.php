<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/messages.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = $userid;

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? 'message';
$id = $data['id'] ?? 0;

if (!is_numeric($id) || $id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

$messages = new Messages();

if ($type === 'thread') {
    $messages->delete_one_thread($id);
} else {
    $messages->delete_one($id);
}

echo json_encode(['success' => true, 'message' => 'Deleted']);
