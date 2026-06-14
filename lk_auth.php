<?php
/**
 * Lightweight Laravel-auth detection for the static landing pages.
 * Decrypts the Laravel session cookie and looks up the (database) session
 * to see whether a user is logged in — without bootstrapping the framework.
 */

if (!function_exists('lk_env')) {
    function lk_env($key, $default = '')
    {
        static $env = null;
        if ($env === null) {
            $env = [];
            $path = __DIR__ . '/.env';
            if (is_file($path)) {
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                        continue;
                    }
                    [$k, $v] = explode('=', $line, 2);
                    $v = trim($v);
                    if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && substr($v, -1) === $v[0]) {
                        $v = substr($v, 1, -1);
                    }
                    $env[trim($k)] = $v;
                }
            }
        }
        return array_key_exists($key, $env) ? $env[$key] : $default;
    }
}

if (!function_exists('lk_logged_in')) {
    /**
     * @return bool true if a valid, non-expired logged-in session exists.
     */
    function lk_logged_in()
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $cached = false;

        $appKey = lk_env('APP_KEY');
        if ($appKey === '') {
            return false;
        }

        $cookieName = lk_env('SESSION_COOKIE', 'living-kost-session');
        if (empty($_COOKIE[$cookieName])) {
            return false;
        }

        $key = (strpos($appKey, 'base64:') === 0) ? base64_decode(substr($appKey, 7)) : $appKey;

        $payload = json_decode(base64_decode($_COOKIE[$cookieName]), true);
        if (!is_array($payload) || !isset($payload['iv'], $payload['value'], $payload['mac'])) {
            return false;
        }

        // Verify MAC, then decrypt (AES-256-CBC), then strip Laravel's cookie prefix.
        $mac = hash_hmac('sha256', $payload['iv'] . $payload['value'], $key);
        if (!hash_equals($mac, (string) $payload['mac'])) {
            return false;
        }

        $decrypted = openssl_decrypt($payload['value'], 'AES-256-CBC', $key, 0, base64_decode($payload['iv']));
        if ($decrypted === false || strlen($decrypted) <= 41) {
            return false;
        }
        // Remove Laravel's CookieValuePrefix: 40-char sha1-hmac + "|" = 41 chars.
        $sessionId = substr($decrypted, 41);

        $lifetime = (int) lk_env('SESSION_LIFETIME', '120');
        $threshold = time() - ($lifetime * 60);

        try {
            $pdo = new PDO(
                'mysql:host=' . lk_env('DB_HOST', 'localhost') . ';dbname=' . lk_env('DB_DATABASE') . ';charset=utf8mb4',
                lk_env('DB_USERNAME'),
                lk_env('DB_PASSWORD'),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, PDO::ATTR_TIMEOUT => 3]
            );
            $stmt = $pdo->prepare('SELECT user_id FROM sessions WHERE id = ? AND user_id IS NOT NULL AND last_activity > ? LIMIT 1');
            $stmt->execute([$sessionId, $threshold]);
            $cached = (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            $cached = false;
        }

        return $cached;
    }
}
