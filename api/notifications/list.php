<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/functions.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = (int)$userid;

$DB = new Database();

$follow = [];
$i_follow = $DB->read("select * from content_i_follow where disabled = 0 && userid = '$userid' limit 100");
if (is_array($i_follow)) {
    $follow = array_column($i_follow, 'contentid');
}

if (count($follow) > 0) {
    $str = "'" . implode("','", $follow) . "'";
    $query = "select n.*, u.first_name, u.last_name, u.profile_image 
              from notifications n 
              join users u on n.userid = u.userid 
              where (n.userid != '$userid' && n.content_owner = '$userid') || (n.contentid in ($str)) 
              order by n.id desc 
              limit 30";
} else {
    $query = "select n.*, u.first_name, u.last_name, u.profile_image 
              from notifications n 
              join users u on n.userid = u.userid 
              where n.userid != '$userid' && n.content_owner = '$userid' 
              order by n.id desc 
              limit 30";
}

$data = $DB->read($query);
$result = [];
if (is_array($data)) {
    foreach ($data as $row) {
        $seen_check = $DB->read("select id from notification_seen where userid = '$userid' && notification_id = '{$row['id']}' limit 1");
        $row['seen'] = is_array($seen_check);
        $result[] = $row;
    }
}

echo json_encode(['success' => true, 'notifications' => $result]);
