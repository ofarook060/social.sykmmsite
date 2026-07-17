<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $content_id = $data['content_id'] ?? 0;
    $type = $data['type'] ?? 'post';
    
    $post->like_post($content_id, $type, $user_data['userid']);
    $likes = $post->get_likes($content_id, $type);
    
    echo json_encode(['success' => true, 'likes' => $likes]);
}
