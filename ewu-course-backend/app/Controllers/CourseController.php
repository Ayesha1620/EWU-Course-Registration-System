<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Course;
use PDO;

class CourseController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Course($db);
        $this->rules = ['CourseCode', 'CourseName', 'Credit'];
    }

    // Enrollment count সহ course list (student-এর জন্য দরকারি তথ্য)
    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT c.*, d.DepartmentName,
                    (SELECT COUNT(*) FROM section s
                      JOIN registration r ON r.SectionID = s.SectionID
                      WHERE s.CourseID = c.CourseID AND r.Status <> "Dropped") AS TotalEnrolled
             FROM course c
             LEFT JOIN department d ON d.DepartmentID = c.DepartmentID
             ORDER BY c.CourseCode'
        );
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, d.DepartmentName
             FROM course c
             LEFT JOIN department d ON d.DepartmentID = c.DepartmentID
             WHERE c.CourseID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}