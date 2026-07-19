<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $postid = $_GET['postid'] ?? 0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * 10;

    $DB = new Database();
    $comments = $DB->read("select c.*, u.first_name, u.last_name, u.profile_image, u.tag_name 
                            from posts c join users u on c.userid = u.userid 
                            where c.parent = '$postid' 
                            order by c.id asc 
                            limit 10 offset $offset");

    echo json_encode(['success' => true, 'comments' => $comments ?: []]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data['parent'] = $data['parent'] ?? 0;

    if (empty($data['post']) || $data['parent'] <= 0) {
        echo json_encode(['success' => false, 'error' => 'Post text and parent ID required']);
        exit;
    }

    $post = new Post();
    $error = $post->create_post($userid, $data, []);

    if ($error == "") {
        echo json_encode(['success' => true, 'message' => 'Comment posted']);
    } else {
        echo json_encode(['success' => false, 'error' => $error]);
    }
}
