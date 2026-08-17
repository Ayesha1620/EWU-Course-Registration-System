<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Faculty;
use PDO;

class FacultyController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Faculty($db);
        $this->rules = ['FacultyName'];
    }

    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT f.*, d.DepartmentName, a.AdvisorID, a.Status AS AdvisorStatus
             FROM faculty f
             LEFT JOIN department d ON d.DepartmentID = f.DepartmentID
             LEFT JOIN advisor a ON a.FacultyID = f.FacultyID
             ORDER BY f.FacultyID'
        );
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT f.*, d.DepartmentName, a.AdvisorID, a.Status AS AdvisorStatus
             FROM faculty f
             LEFT JOIN department d ON d.DepartmentID = f.DepartmentID
             LEFT JOIN advisor a ON a.FacultyID = f.FacultyID
             WHERE f.FacultyID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}