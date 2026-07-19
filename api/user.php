<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/connect.php';
require_once __DIR__ . '/../classes/user.php';
require_once __DIR__ . '/../classes/login.php';
require_once __DIR__ . '/../classes/jwt.php';

session_start();

$jwt_userid = JWTAuth::get_userid_from_request();
if ($jwt_userid) {
    $_SESSION['mybook_userid'] = $jwt_userid;
    $user = new User();
    $user_data = $user->get_data($jwt_userid);
    if (!$user_data) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
} else {
    $login = new Login();
    $user_data = $login->check_login($_SESSION['mybook_userid'] ?? 0, false);
    if (!$user_data) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }
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
