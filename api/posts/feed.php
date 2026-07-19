<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$DB = new Database();

$user = new User();
$following = $user->get_following($userid, 'user');
$follow_ids = [$userid];
if (is_array($following)) {
    $follow_ids = array_merge($follow_ids, array_column($following, 'userid'));
}

$ids_str = "'" . implode("','", $follow_ids) . "'";
$query = "select p.*, u.first_name, u.last_name, u.profile_image, u.tag_name 
          from posts p 
          join users u on p.userid = u.userid 
          where p.parent = 0 && p.userid in ($ids_str) 
          order by p.id desc 
          limit $limit offset $offset";
$posts = $DB->read($query);

echo json_encode(['success' => true, 'posts' => $posts ?: []]);
