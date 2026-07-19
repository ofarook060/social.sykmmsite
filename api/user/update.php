<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/settings.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = [];

$allowed = ['first_name', 'last_name', 'gender', 'email', 'about'];
$updates = [];
foreach ($allowed as $field) {
    if (!empty($data[$field])) {
        $updates[$field] = addslashes($data[$field]);
    }
}

if (!empty($data['password']) && !empty($data['password2'])) {
    if ($data['password'] === $data['password2'] && strlen($data['password']) < 30) {
        $updates['password'] = hash("sha1", $data['password']);
    }
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'error' => 'No fields to update']);
    exit;
}

$DB = new Database();
$sql = "update users set ";
foreach ($updates as $key => $value) {
    $sql .= "$key = '$value',";
}
$sql = rtrim($sql, ",");
$sql .= " where userid = '$userid' limit 1";
$DB->save($sql);

echo json_encode(['success' => true, 'message' => 'Profile updated']);
