<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// First, if the requested path points to an actual file under the project root
// (e.g., /public/assets/... when docroot is the project root), let the built-in
// server serve it directly.
$fullPath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($fullPath)) {
    return false;
}

// Fallback emulation of Apache's mod_rewrite: route everything else through
// Laravel's front controller.
require_once __DIR__.'/public/index.php';
