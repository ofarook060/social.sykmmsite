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
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = $userid;

$data = json_decode(file_get_contents('php://input'), true);
$receiver = $data['receiver'] ?? 0;

if (!is_numeric($receiver) || $receiver <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid receiver ID']);
    exit;
}

if (empty($data['message'])) {
    echo json_encode(['success' => false, 'error' => 'Message text required']);
    exit;
}

$messages = new Messages();
$error = $messages->send($data, [], $receiver);

if ($error == "") {
    echo json_encode(['success' => true, 'message' => 'Message sent']);
} else {
    echo json_encode(['success' => false, 'error' => trim(strip_tags($error))]);
}
