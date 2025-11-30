<?php
/**
 * Simple environment loader and helper.
 */

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value === false ? $default : $value;
    }
}

if (!function_exists('loadEnv')) {
    function loadEnv(string $baseDir): void
    {
        $envFile = rtrim($baseDir, '/\\') . '/.env';
        if (!is_readable($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $trimmed[0] === '#') {
                continue;
            }
            if (strpos($trimmed, '=') === false) {
                continue;
            }
            [$name, $value] = explode('=', $trimmed, 2);
            $name = trim($name);
            $value = trim($value);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Auto-load .env from project root.
loadEnv(__DIR__ . '/..');
