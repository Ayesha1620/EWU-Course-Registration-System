<?php

namespace App;

use PDO;

class Database
{
    // একবার connection হলে পুরো request জুড়ে reuse করা হয়
    private static $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                // error হলে exception throw করবে (try/catch করা সহজ)
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // result সবসময় assoc array হিসাবে return হবে
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // real prepared statement ব্যবহার হবে (SQL injection এর আশঙ্কা কম)
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$pdo;
    }
}