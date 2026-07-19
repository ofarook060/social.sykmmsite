<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/messages.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();
$_SESSION['mybook_userid'] = $userid;

$messages = new Messages();
$threads = $messages->read_threads();

$result = [];
if (is_array($threads)) {
    $DB = new Database();
    foreach ($threads as $thread) {
        $other_userid = $thread['sender'] == $userid ? $thread['receiver'] : $thread['sender'];
        $user = $DB->read("select userid, first_name, last_name, profile_image, online from users where userid = '$other_userid' limit 1");
        
        $unread = $DB->read("select count(*) as cnt from messages where receiver = '$userid' && msgid = '{$thread['msgid']}' && seen = 0 && deleted_receiver = 0");
        $unread_count = is_array($unread) ? $unread[0]['cnt'] : 0;

        $result[] = [
            'msgid' => $thread['msgid'],
            'message' => $thread['message'],
            'file' => $thread['file'],
            'date' => $thread['date'],
            'seen' => $thread['seen'],
            'other_user' => $user ? $user[0] : null,
            'unread_count' => (int)$unread_count,
        ];
    }
}

echo json_encode(['success' => true, 'threads' => $result]);
