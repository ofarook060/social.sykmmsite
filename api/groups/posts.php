<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$groupid = $_GET['groupid'] ?? 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if (!is_numeric($groupid) || $groupid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid group ID']);
    exit;
}

$group = new Group();
$group_data = $group->get_group($groupid);

if (!$group_data) {
    echo json_encode(['success' => false, 'error' => 'Group not found']);
    exit;
}

if ($group_data[0]['group_type'] == 'Private' && !group_access($userid, $group_data[0], 'member')) {
    echo json_encode(['success' => false, 'error' => 'Private group - access denied']);
    exit;
}

$DB = new Database();
$posts = $DB->read("select p.*, u.first_name, u.last_name, u.profile_image, u.tag_name 
                     from posts p join users u on p.userid = u.userid 
                     where p.parent = 0 && p.owner = '$groupid' 
                     order by p.id desc 
                     limit $limit offset $offset");

echo json_encode(['success' => true, 'posts' => $posts ?: []]);
