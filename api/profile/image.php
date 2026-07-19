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

$type = $_POST['type'] ?? 'profile';
if (!in_array($type, ['profile', 'cover'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid type']);
    exit;
}

$file = $_FILES['file'];
if ($file['type'] !== 'image/jpeg') {
    echo json_encode(['success' => false, 'error' => 'Only JPEG images allowed']);
    exit;
}

$folder = __DIR__ . "/../../uploads/$userid/";
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$filename = bin2hex(random_bytes(8)) . ".jpg";
$filepath = $folder . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    $relative_path = "uploads/$userid/$filename";
    $field = $type === 'profile' ? 'profile_image' : 'cover_image';

    $DB = new Database();
    $DB->save("update users set $field = '$relative_path' where userid = '$userid' limit 1");

    echo json_encode(['success' => true, 'url' => $relative_path]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
}
