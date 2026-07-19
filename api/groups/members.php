<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/group.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$groupid = $_GET['groupid'] ?? 0;
if (!is_numeric($groupid) || $groupid <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid group ID']);
    exit;
}

$group = new Group();
$members = $group->get_members($groupid);

$result = [];
if (is_array($members)) {
    $DB = new Database();
    foreach ($members as $m) {
        $uid = $m['userid'];
        $user = $DB->read("select userid, first_name, last_name, profile_image, tag_name, online from users where userid = '$uid' limit 1");
        if ($user) {
            $entry = $user[0];
            $entry['role'] = $m['role'] ?? 'admin';
            $result[] = $entry;
        }
    }
}

echo json_encode(['success' => true, 'members' => $result]);
