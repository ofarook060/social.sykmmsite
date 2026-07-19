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
require_once __DIR__ . '/../../classes/jwt.php';

$token = JWTAuth::extract_token();
if (!$token) {
    echo json_encode(['success' => false, 'error' => 'No token provided']);
    exit;
}

$payload = JWTAuth::validate_token($token);
if (!$payload) {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
    exit;
}

$new_token = JWTAuth::generate_token($payload['userid'], $payload['email']);

echo json_encode([
    'success' => true,
    'token' => $new_token,
    'user_id' => $payload['userid'],
    'message' => 'Token refreshed'
]);
