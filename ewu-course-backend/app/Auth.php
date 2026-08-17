<?php

namespace App;

// Bearer token ভিত্তিক authentication।
// Login করলে একটা token দেওয়া হয়, পরবর্তী request এ
// Authorization: Bearer <token> header দিয়ে পাঠালে user identify হয়।

class Auth
{
    // route-এর role list অনুযায়ী access দেওয়া/নিষেধ করা
    public static function authorize(array $roles): void
    {
        if (count($roles) === 0) {
            return; // public route
        }

        $user = self::user();
        if ($user === null) {
            json_error('Not authenticated. Please login first.', 401);
        }
        if (!in_array($user['role'], $roles, true)) {
            json_error('Access denied for your role.', 403);
        }
    }

    // বর্তমান logged-in user বের করে (token থেকে), না পেলে null
    public static function user(): ?array
    {
        $token = self::rawToken();
        if ($token === null) {
            return null;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM auth_tokens WHERE token = ? AND expires_at > NOW()');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        if ($row['user_type'] === 'student') {
            $stmt = $pdo->prepare(
                'SELECT s.*, d.DepartmentName
                 FROM student s
                 LEFT JOIN department d ON d.DepartmentID = s.DepartmentID
                 WHERE s.StudentID = ?'
            );
            $stmt->execute([$row['reference_id']]);
            $user = $stmt->fetch();
            if (!$user) {
                return null;
            }
            $user['role'] = 'student';
        } else {
            $stmt = $pdo->prepare(
                'SELECT f.*, a.AdvisorID, a.Status AS AdvisorStatus
                 FROM faculty f
                 LEFT JOIN advisor a ON a.FacultyID = f.FacultyID
                 WHERE f.FacultyID = ?'
            );
            $stmt->execute([$row['reference_id']]);
            $user = $stmt->fetch();
            if (!$user) {
                return null;
            }
            // faculty শুধু advisor table-এ থাকলেই advisor role পাবে
            $user['role'] = $user['AdvisorID'] !== null ? 'advisor' : 'faculty';
        }

        unset($user['Password']);
        return $user;
    }

    public static function issueToken(string $userType, int $referenceId): string
    {
        $token = bin2hex(random_bytes(32));
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO auth_tokens (token, user_type, reference_id, expires_at)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$token, $userType, $referenceId, date('Y-m-d H:i:s', strtotime('+24 hours'))]);
        return $token;
    }

    public static function logout(?string $token): void
    {
        if ($token === null || $token === '') {
            return;
        }
        Database::connect()->prepare('DELETE FROM auth_tokens WHERE token = ?')->execute([$token]);
    }

    public static function rawToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($header === '' && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
        }
        if (preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            return $m[1];
        }
        return null;
    }
}