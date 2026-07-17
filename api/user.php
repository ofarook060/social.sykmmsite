<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../classes/connect.php';
require_once '../classes/user.php';
require_once '../classes/login.php';

session_start();

$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid'] ?? 0, false);

if (!$user_data) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user = new User();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action == 'friends') {
        $friends = $user->get_friends($user_data['userid']);
        echo json_encode(['success' => true, 'friends' => $friends]);
    } elseif ($action == 'following') {
        $following = $user->get_following($user_data['userid'], 'user');
        echo json_encode(['success' => true, 'following' => $following]);
    } else {
        echo json_encode(['success' => true, 'user' => $user_data]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    if ($action == 'follow') {
        $target_id = $data['user_id'] ?? 0;
        $user->follow_user($target_id, 'user', $user_data['userid']);
        echo json_encode(['success' => true, 'message' => 'Follow action completed']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
}
