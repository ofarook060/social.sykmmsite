<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$data = json_decode(file_get_contents('php://input'), true);
$postid = $data['postid'] ?? 0;

if (!is_numeric($postid) || $postid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
    exit;
}

$post = new Post();
if (!$post->i_own_post($postid, $userid)) {
    echo json_encode(['success' => false, 'error' => 'Not authorized to edit this post']);
    exit;
}

$error = $post->edit_post($data, []);

if ($error == "") {
    echo json_encode(['success' => true, 'message' => 'Post updated']);
} else {
    echo json_encode(['success' => false, 'error' => $error]);
}
