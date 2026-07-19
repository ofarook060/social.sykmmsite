<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/messages.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = $userid;

$other_userid = $_GET['userid'] ?? 0;
if (!is_numeric($other_userid) || $other_userid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

$messages = new Messages();
$raw = $messages->read($other_userid);

$result = [];
if (is_array($raw)) {
    foreach ($raw as $msg) {
        $result[] = [
            'id' => $msg['id'],
            'sender' => $msg['sender'],
            'receiver' => $msg['receiver'],
            'message' => $msg['message'],
            'file' => $msg['file'],
            'seen' => $msg['seen'],
            'date' => $msg['date'],
        ];
    }
}

echo json_encode(['success' => true, 'messages' => $result]);
