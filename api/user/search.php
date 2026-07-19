<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$find = $_GET['find'] ?? '';
if (empty($find)) {
    echo json_encode(['success' => false, 'error' => 'Search query required']);
    exit;
}

$find = addslashes($find);
$DB = new Database();

$users = $DB->read("select * from users where type = 'profile' && (first_name like '%$find%' || last_name like '%$find%') limit 20");
$groups = $DB->read("select * from users where type = 'group' && first_name like '%$find%' limit 20");

echo json_encode([
    'success' => true,
    'users' => $users ?: [],
    'groups' => $groups ?: []
]);
