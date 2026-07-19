<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class JWTAuth
{
    private static $secret = null;
    private static $algo = 'HS256';
    private static $expiry_days = 7;

    private static function getSecret()
    {
        if (self::$secret === null) {
            self::$secret = $_ENV['JWT_SECRET'];
        }
        return self::$secret;
    }

    public static function generate_token($userid, $email)
    {
        $now = time();
        $payload = [
            'iss' => 'syk_social',
            'iat' => $now,
            'exp' => $now + (self::$expiry_days * 24 * 60 * 60),
            'userid' => (int)$userid,
            'email' => $email,
        ];

        return JWT::encode($payload, self::getSecret(), self::$algo);
    }

    public static function validate_token($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), self::$algo));
            return (array)$decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function get_userid_from_request()
    {
        $token = self::extract_token();
        if (!$token) {
            return false;
        }

        $payload = self::validate_token($token);
        if (!$payload) {
            return false;
        }

        return $payload['userid'] ?? false;
    }

    public static function extract_token()
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (preg_match('/Bearer\s+(.+)$/i', $auth, $matches)) {
            return $matches[1];
        }

        return false;
    }

    public static function require_auth()
    {
        $userid = self::get_userid_from_request();
        if (!$userid) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required']);
            exit;
        }
        return $userid;
    }
}
