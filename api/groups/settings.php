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

if (!group_access($userid, $group_data[0], 'admin')) {
    echo json_encode(['success' => false, 'error' => 'Admin only']);
    exit;
}

$DB = new Database();
$updates = [];
if (!empty($data['group_name'])) $updates[] = "first_name = '" . addslashes($data['group_name']) . "'";
if (!empty($data['group_type']) && in_array($data['group_type'], ['Public', 'Private'])) $updates[] = "group_type = '" . addslashes($data['group_type']) . "'";
if (isset($data['about'])) $updates[] = "about = '" . addslashes($data['about']) . "'";

if (empty($updates)) {
    echo json_encode(['success' => false, 'error' => 'No fields to update']);
    exit;
}

$sql = "update users set " . implode(", ", $updates) . " where userid = '$groupid' limit 1";
$DB->save($sql);

echo json_encode(['success' => true, 'message' => 'Group settings updated']);
