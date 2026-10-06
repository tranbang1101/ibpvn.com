<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    $secureCookie = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$basePath = getenv('APP_BASE_PATH');
if ($basePath === false) {
    $basePath = '/ibpvn.com';
}
$basePath = '/' . trim($basePath, '/');
if ($basePath === '/') {
    $basePath = '';
}
define('BASE_PATH', $basePath);

$baseUrl = getenv('APP_BASE_URL');
if ($baseUrl === false || trim($baseUrl) === '') {
    $scheme = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off'
        ? 'https'
        : 'http';
    $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host . BASE_PATH . '/';
}
define('BASE_URL', rtrim($baseUrl, '/') . '/');

function csrf_token(): string
{
    return $_SESSION['csrf_token'];
}

function csrf_valid(): bool
{
    $submittedToken = $_POST['_csrf'] ?? '';
    return isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && is_string($submittedToken)
        && hash_equals($_SESSION['csrf_token'], $submittedToken);
}

function is_safe_local_redirect(string $destination): bool
{
    if (preg_match('/[\x00-\x1F\x7F\\\\]/', $destination)) {
        return false;
    }
    $parts = parse_url($destination);
    if (
        $parts === false
        || isset($parts['scheme'])
        || isset($parts['host'])
        || isset($parts['user'])
        || isset($parts['pass'])
    ) {
        return false;
    }
    $path = $parts['path'] ?? '';
    if (BASE_PATH === '') {
        return str_starts_with($path, '/') && !str_starts_with($path, '//');
    }
    return $path === BASE_PATH || str_starts_with($path, BASE_PATH . '/');
}
