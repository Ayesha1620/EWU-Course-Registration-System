<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Student;
use PDO;

class StudentController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Student($db);
        $this->rules = ['StudentName', 'Email'];
    }

    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT s.*, d.DepartmentName, f.FacultyName AS AdvisorName
             FROM student s
             LEFT JOIN department d ON d.DepartmentID = s.DepartmentID
             LEFT JOIN advisor a ON a.AdvisorID = s.AdvisorID
             LEFT JOIN faculty f ON f.FacultyID = a.FacultyID
             ORDER BY s.StudentID'
        );
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, d.DepartmentName, f.FacultyName AS AdvisorName
             FROM student s
             LEFT JOIN department d ON d.DepartmentID = s.DepartmentID
             LEFT JOIN advisor a ON a.AdvisorID = s.AdvisorID
             LEFT JOIN faculty f ON f.FacultyID = a.FacultyID
             WHERE s.StudentID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}