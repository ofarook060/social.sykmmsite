<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../classes/connect.php';
require_once '../classes/post.php';
require_once '../classes/login.php';

session_start();

$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid'] ?? 0, false);

if (!$user_data) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$post = new Post();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'] ?? $user_data['userid'];
    $post_type = $_GET['type'] ?? 'profile';
    $posts = $post->get_posts($user_id, $post_type);
    
    echo json_encode(['success' => true, 'posts' => $posts]);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $error = $post->create_post($user_data['userid'], $data, []);
    
    if ($error == "") {
        echo json_encode(['success' => true, 'message' => 'Post created']);
    } else {
        echo json_encode(['success' => false, 'error' => $error]);
    }
}
