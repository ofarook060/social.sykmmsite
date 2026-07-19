<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$postid = $_GET['postid'] ?? 0;
if (!is_numeric($postid) || $postid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
    exit;
}

$DB = new Database();
$post = $DB->read("select p.*, u.first_name, u.last_name, u.profile_image, u.tag_name 
                    from posts p join users u on p.userid = u.userid 
                    where p.postid = '$postid' limit 1");

if (!$post) {
    echo json_encode(['success' => false, 'error' => 'Post not found']);
    exit;
}

$likes = $DB->read("select likes from likes where type = 'post' && contentid = '$postid' limit 1");
$like_data = [];
if (is_array($likes)) {
    $like_data = json_decode($likes[0]['likes'], true) ?: [];
}

$liked_by_me = false;
foreach ($like_data as $l) {
    if ($l['userid'] == $userid) {
        $liked_by_me = true;
        break;
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * 10;
$comments = $DB->read("select c.*, u.first_name, u.last_name, u.profile_image, u.tag_name 
                        from posts c join users u on c.userid = u.userid 
                        where c.parent = '$postid' 
                        order by c.id asc 
                        limit 10 offset $offset");

echo json_encode([
    'success' => true,
    'post' => $post[0],
    'likes' => count($like_data),
    'liked_by_me' => $liked_by_me,
    'comments' => $comments ?: []
]);
