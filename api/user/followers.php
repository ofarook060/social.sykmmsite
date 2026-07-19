<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/post.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

$user_id = $_GET['user_id'] ?? $userid;

$DB = new Database();
$sql = "select likes from likes where type = 'user' && contentid = '$user_id' limit 1";
$result = $DB->read($sql);

$followers = [];
if (is_array($result)) {
    $likes = json_decode($result[0]['likes'], true);
    if (is_array($likes)) {
        foreach ($likes as $like) {
            $uid = $like['userid'];
            $row = $DB->read("select userid, first_name, last_name, profile_image, tag_name, type, online from users where userid = '$uid' limit 1");
            if ($row) $followers[] = $row[0];
        }
    }
}

echo json_encode(['success' => true, 'followers' => $followers]);
