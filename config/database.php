<?php
require_once __DIR__ . '/config.php';

function db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;
    if ($attempted) return $pdo;
    $attempted = true;

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'ibpvn';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function valid_phone(string $phone): bool
{
    return preg_match('/^[0-9+().\s-]{7,25}$/D', $phone) === 1
        && preg_match_all('/[0-9]/', $phone) >= 7;
}

function asset_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }

    $parts = parse_url($path);
    if ($parts === false) {
        return '';
    }
    if (isset($parts['scheme'])) {
        return in_array(strtolower($parts['scheme']), ['http', 'https'], true) ? $path : '';
    }

    if (str_starts_with($path, '/ibpvn.com/')) {
        $path = substr($path, strlen('/ibpvn.com'));
    }
    if (!str_starts_with($path, '/')) {
        $path = '/' . ltrim($path, '/');
    }
    if (BASE_PATH !== '' && $path !== BASE_PATH && !str_starts_with($path, BASE_PATH . '/')) {
        $path = BASE_PATH . $path;
    }

    return $path;
}
