<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$data = json_decode(file_get_contents('php://input'), true);
$groupid = $data['groupid'] ?? 0;
$target_userid = $data['userid'] ?? 0;
$action = $data['action'] ?? 'accept';

if (!is_numeric($groupid) || !is_numeric($target_userid)) {
    echo json_encode(['success' => false, 'error' => 'Invalid IDs']);
    exit;
}

if (!in_array($action, ['accept', 'decline'])) {
    echo json_encode(['success' => false, 'error' => 'Action must be accept or decline']);
    exit;
}

$group = new Group();
$group_data = $group->get_group($groupid);

if (!$group_data) {
    echo json_encode(['success' => false, 'error' => 'Group not found']);
    exit;
}

if (!group_access($userid, $group_data[0], 'moderator')) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

$group->accept_request($groupid, $target_userid, $action);

echo json_encode(['success' => true, 'message' => "Request $action" . "ed"]);
