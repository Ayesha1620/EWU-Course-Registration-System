<?php

namespace App\Controllers;

use App\Auth;
use App\Controller;

class AuthController extends Controller
{
    // POST /api/auth/login  body: { "email": "...", "password": "...", "role": "student|faculty" }
    public function login(): void
    {
        $data     = $this->input();
        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role     = strtolower(trim($data['role'] ?? 'student'));

        if ($email === '' || $password === '') {
            json_error('Email and password are required', 422);
        }
        if (!in_array($role, ['student', 'faculty'], true)) {
            json_error('Role must be either "student" or "faculty"', 422);
        }

        if ($role === 'student') {
            $stmt = $this->db->prepare('SELECT * FROM student WHERE Email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['Password'] ?? '')) {
                json_error('Invalid email or password', 401);
            }
            $user['role'] = 'student';
            $token = Auth::issueToken('student', (int)$user['StudentID']);
        } else {
            $stmt = $this->db->prepare(
                'SELECT f.*, a.AdvisorID, a.Status AS AdvisorStatus
                 FROM faculty f
                 LEFT JOIN advisor a ON a.FacultyID = f.FacultyID
                 WHERE f.Email = ?'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['Password'] ?? '')) {
                json_error('Invalid email or password', 401);
            }
            $user['role'] = $user['AdvisorID'] !== null ? 'advisor' : 'faculty';
            $token = Auth::issueToken('faculty', (int)$user['FacultyID']);
        }

        unset($user['Password']);
        json_success([
            'token' => $token,
            'user'  => $user,
        ], 'Login successful');
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        Auth::logout(Auth::rawToken());
        json_success([], 'Logged out');
    }

    // GET /api/auth/me — বর্তমান user এর তথ্য
    public function me(): void
    {
        json_success(Auth::user());
    }
}