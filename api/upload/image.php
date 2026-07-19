<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/connect.php';
require_once __DIR__ . '/../../classes/jwt.php';

$userid = JWTAuth::require_auth();

if (empty($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
$max_size = 10 * 1024 * 1024; // 10MB

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, MP4']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'File too large. Max 10MB']);
    exit;
}

$folder = __DIR__ . "/../../uploads/$userid/";
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
    file_put_contents($folder . "index.php", "");
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = bin2hex(random_bytes(8)) . "." . $ext;
$filepath = $folder . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    $relative_path = "uploads/$userid/$filename";
    echo json_encode([
        'success' => true,
        'url' => $relative_path,
        'type' => $file['type'],
        'size' => $file['size'],
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
}
