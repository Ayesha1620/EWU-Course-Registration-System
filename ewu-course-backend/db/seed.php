<?php

// সব student ও faculty-র default password সেট করে দেওয়ার স্ক্রিপ্ট।
// চালাতে (project root থেকে):   php db/seed.php
// Default password: 123456
// (alter_login.sql run করার পরেই শুধু এইটা চালাবে)

require __DIR__ . '/../app/autoload.php';

use App\Database;

try {
    $pdo = Database::connect();
} catch (Throwable $e) {
    fwrite(STDERR, "Database connect failed: {$e->getMessage()}\n");
    exit(1);
}

$password = '123456';
$hash     = password_hash($password, PASSWORD_DEFAULT);

$studentCount = 0;
foreach ($pdo->query('SELECT StudentID FROM student')->fetchAll(PDO::FETCH_COLUMN) as $id) {
    $pdo->prepare('UPDATE student SET Password = ? WHERE StudentID = ?')->execute([$hash, $id]);
    $studentCount++;
}

$facultyCount = 0;
foreach ($pdo->query('SELECT FacultyID FROM faculty')->fetchAll(PDO::FETCH_COLUMN) as $id) {
    $pdo->prepare('UPDATE faculty SET Password = ? WHERE FacultyID = ?')->execute([$hash, $id]);
    $facultyCount++;
}

echo "Done. student: {$studentCount}, faculty: {$facultyCount}\n";
echo "Everyone's password is: {$password}\n";