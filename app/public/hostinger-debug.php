<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$publicDirectory = __DIR__;
$checks = [
    'public/index.php' => $publicDirectory . DIRECTORY_SEPARATOR . 'index.php',
    'public/.htaccess' => $publicDirectory . DIRECTORY_SEPARATOR . '.htaccess',
    'app/Config/Paths.php' => dirname($publicDirectory) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Paths.php',
    'vendor/autoload.php' => dirname($publicDirectory) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
    'writable directory' => dirname($publicDirectory) . DIRECTORY_SEPARATOR . 'writable',
];

$formatStatus = static function (string $path, bool $directory = false): string {
    $exists = $directory ? is_dir($path) : is_file($path);
    if (! $exists) {
        return 'MISSING';
    }

    if (! is_readable($path)) {
        return 'NOT READABLE';
    }

    if ($directory && ! is_writable($path)) {
        return 'READABLE, NOT WRITABLE';
    }

    return $directory ? 'OK, WRITABLE' : 'OK';
};

echo "KKN MONITORING - HOSTINGER DEBUG\n";
echo "Delete app/public/hostinger-debug.php after debugging.\n\n";

echo "[REQUEST]\n";
echo 'HTTP status: 200' . PHP_EOL;
echo 'URL: ' . ((string) ($_SERVER['REQUEST_SCHEME'] ?? 'http')) . '://' . ((string) ($_SERVER['HTTP_HOST'] ?? 'unknown')) . ((string) ($_SERVER['REQUEST_URI'] ?? '/')) . PHP_EOL;
echo 'Method: ' . ((string) ($_SERVER['REQUEST_METHOD'] ?? 'unknown')) . PHP_EOL;
echo 'Script: ' . ((string) ($_SERVER['SCRIPT_FILENAME'] ?? 'unknown')) . PHP_EOL;
echo 'Document root: ' . ((string) ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown')) . PHP_EOL;
echo 'Current directory: ' . getcwd() . PHP_EOL;

echo "\n[SERVER]\n";
echo 'PHP version: ' . PHP_VERSION . PHP_EOL;
echo 'Server software: ' . ((string) ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown')) . PHP_EOL;
echo 'HTTPS: ' . ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'on' : 'off') . PHP_EOL;
echo 'mod_rewrite listed: ' . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules(), true) ? 'yes' : 'unknown/not listed') . PHP_EOL;

echo "\n[FILES]\n";
foreach ($checks as $label => $path) {
    $isDirectory = $label === 'writable directory';
    echo $label . ': ' . $formatStatus($path, $isDirectory) . ' (' . $path . ')' . PHP_EOL;
}

echo "\n[PHP EXTENSIONS]\n";
foreach (['mysqli', 'pdo_mysql', 'intl', 'mbstring', 'curl', 'fileinfo', 'gd', 'zip'] as $extension) {
    echo $extension . ': ' . (extension_loaded($extension) ? 'loaded' : 'MISSING') . PHP_EOL;
}

echo "\n[PERMISSIONS]\n";
echo 'public directory: ' . substr(sprintf('%o', fileperms($publicDirectory)), -4) . PHP_EOL;
echo 'debug file: ' . substr(sprintf('%o', fileperms(__FILE__)), -4) . PHP_EOL;

echo "\n[INTERPRETATION]\n";
echo "If this URL also returns 403, the domain document root or hosting permission is wrong.\n";
echo "The document root must contain index.php, .htaccess, and the public assets.\n";
echo "If app/Config/Paths.php or vendor/autoload.php is MISSING, fix the two-folder upload layout.\n";
