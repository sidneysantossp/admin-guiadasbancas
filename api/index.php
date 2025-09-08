<?php
// Redirect to Laravel application
// Set the correct path to Laravel's public directory
$laravelPublicPath = __DIR__ . '/../public';

// Change to Laravel's public directory
chdir($laravelPublicPath);

// Include Laravel's main index.php
require_once $laravelPublicPath . '/index.php';
