<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../classes/connect.php';
require_once '../classes/login.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

$login = new Login();
$error = $login->evaluate($data);

if ($error == "") {
    echo json_encode([
        'success' => true,
        'user_id' => $_SESSION['mybook_userid'],
        'message' => 'Login successful'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $error]);
}
