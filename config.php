<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'eticaretdb');
define('DB_USER', 'root');
define('DB_PASS', '');
if (!defined('SITE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = $scriptName ? rtrim(str_replace('\\', '/', dirname($scriptName)), '/') : '';
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
    if (str_ends_with($basePath, '/admin')) {
        $basePath = substr($basePath, 0, -6);
    }
    define('SITE_URL', $scheme . '://' . $host . $basePath);
}
define('SITE_NAME', 'Élégance');
