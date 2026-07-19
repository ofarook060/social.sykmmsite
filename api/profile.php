<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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
    $user_id = $_GET['user_id'] ?? $user_data['userid'];
    $profile_data = $user->get_data($user_id);
    
    if ($profile_data) {
        echo json_encode(['success' => true, 'user' => $profile_data]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
}
