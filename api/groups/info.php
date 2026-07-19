<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$groupid = $_GET['groupid'] ?? 0;
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

$group_data = $group_data[0];
$is_member = group_access($userid, $group_data, 'member');
$my_role = $group->get_member_role($groupid, $userid);

$DB = new Database();
$member_count = $DB->read("select count(*) as cnt from group_members where groupid = '$groupid' && disabled = 0");
$member_count = is_array($member_count) ? $member_count[0]['cnt'] : 0;

$post_count = $DB->read("select count(*) as cnt from posts where owner = '$groupid' && parent = 0");
$post_count = is_array($post_count) ? $post_count[0]['cnt'] : 0;

echo json_encode([
    'success' => true,
    'group' => $group_data,
    'is_member' => $is_member,
    'my_role' => $my_role,
    'member_count' => (int)$member_count,
    'post_count' => (int)$post_count,
]);
