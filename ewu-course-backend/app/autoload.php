<?php

// ছোট্ট autoloader — class ব্যবহার করলেই ফাইলটা নিজে নিজে load হবে।
// App\Database       => app/Database.php
// App\Controllers\X  => app/Controllers/X.php
// App\Models\X       => app/Models/X.php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});