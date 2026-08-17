<?php

// ============ Front controller ============
// সব HTTP request এখান দিয়ে যায়। route match করে controller call করে।

require __DIR__ . '/app/autoload.php';
require __DIR__ . '/app/helpers.php';

use App\Router;

// CORS headers — frontend আলাদা domain/port এ থাকলেও request করা যাবে
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// project যেকোনো subfolder এ রাখলেও ঠিকঠাক কাজ করবে
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

$router = new Router($basePath);
require __DIR__ . '/routes.php';

try {
    $router->dispatch();
} catch (PDOException $e) {
    // DB connection বা query error হলে খালি 500 না দিয়ে কিছু context দিই
    json_error('Database error: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    json_error('Server error: ' . $e->getMessage(), 500);
}