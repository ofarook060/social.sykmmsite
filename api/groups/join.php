<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$data = json_decode(file_get_contents('php://input'), true);
$groupid = $data['groupid'] ?? 0;

if (!is_numeric($groupid) || $groupid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid group ID']);
    exit;
}

$group = new Group();
$group->join_group($groupid, $userid);

echo json_encode(['success' => true, 'message' => 'Join request sent']);
