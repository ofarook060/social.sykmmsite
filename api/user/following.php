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

$user = new User();
$following = $user->get_following($userid, 'user');

$result = [];
if (is_array($following)) {
    $DB = new Database();
    foreach ($following as $f) {
        $uid = $f['userid'];
        $row = $DB->read("select userid, first_name, last_name, profile_image, tag_name, type, online from users where userid = '$uid' limit 1");
        if ($row) $result[] = $row[0];
    }
}

echo json_encode(['success' => true, 'following' => $result]);
