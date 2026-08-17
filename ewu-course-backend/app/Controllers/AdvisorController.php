<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Advisor;
use PDO;

class AdvisorController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Advisor($db);
        $this->rules = ['FacultyID'];
    }

    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT a.*, f.FacultyName, f.Email, f.DepartmentID,
                    (SELECT COUNT(*) FROM student s WHERE s.AdvisorID = a.AdvisorID) AS StudentCount
             FROM advisor a
             JOIN faculty f ON f.FacultyID = a.FacultyID
             ORDER BY a.AdvisorID'
        );
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, f.FacultyName, f.Email, f.DepartmentID,
                    (SELECT COUNT(*) FROM student s WHERE s.AdvisorID = a.AdvisorID) AS StudentCount
             FROM advisor a
             JOIN faculty f ON f.FacultyID = a.FacultyID
             WHERE a.AdvisorID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}