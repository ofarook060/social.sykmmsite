<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/login.php';
require_once __DIR__ . '/../../classes/jwt.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

$login = new Login();
$error = $login->evaluate($data);

if ($error == "") {
    $userid = $_SESSION['mybook_userid'];

    $DB = new Database();
    $row = $DB->read("select email from users where userid = '$userid' limit 1");
    $email = $row ? $row[0]['email'] : $data['email'];

    $token = JWTAuth::generate_token($userid, $email);

    echo json_encode([
        'success' => true,
        'user_id' => $userid,
        'token' => $token,
        'message' => 'Login successful'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $error]);
}
