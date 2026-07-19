<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/messages.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

echo json_encode(['step' => 'includes_done']);

session_start();
echo json_encode(['step' => 'session_started']);

$userid = JWTAuth::get_userid_from_request();
echo json_encode(['step' => 'jwt_done', 'userid' => $userid]);

if (!$userid) {
    echo json_encode(['error' => 'no userid']);
    exit;
}

$_SESSION['mybook_userid'] = (int)$userid;
echo json_encode(['step' => 'session_set']);

try {
    $messages = new Messages();
    echo json_encode(['step' => 'messages_created']);
    
    $threads = $messages->read_threads();
    echo json_encode(['step' => 'threads_read', 'count' => is_array($threads) ? count($threads) : 'false']);
    
    echo json_encode(['success' => true, 'threads' => $threads ?: []]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
