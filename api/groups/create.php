<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = $userid;

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['group_name']) || empty($data['group_type'])) {
    echo json_encode(['success' => false, 'error' => 'Group name and type required']);
    exit;
}

if (!in_array($data['group_type'], ['Public', 'Private'])) {
    echo json_encode(['success' => false, 'error' => 'Group type must be Public or Private']);
    exit;
}

$group = new Group();
$error = $group->evaluate($data);

if ($error == "") {
    echo json_encode(['success' => true, 'message' => 'Group created']);
} else {
    echo json_encode(['success' => false, 'error' => trim(strip_tags($error))]);
}
