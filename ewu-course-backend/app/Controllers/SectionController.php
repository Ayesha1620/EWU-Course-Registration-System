<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Section;
use PDO;

class SectionController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Section($db);
        $this->rules = ['CourseID', 'FacultyID', 'SemesterID', 'RoomID'];
    }

    // কোর্স/ফ্যাকাল্টি/রুম/সেমিস্টার নাম সহ list — registration catalog হিসাবে কাজ করে
    public function index(): void
    {
        $sql = 'SELECT sec.SectionID, sec.SectionNumber, sec.ScheduleDay, sec.StartTime, sec.EndTime,
                       sec.CourseID, c.CourseCode, c.CourseName, c.Credit,
                       sec.FacultyID, f.FacultyName,
                       sec.RoomID, cl.RoomNumber, cl.Building, cl.Capacity,
                       sec.SemesterID, sem.SemesterName, sem.Year,
                       (SELECT COUNT(*) FROM registration r
                         WHERE r.SectionID = sec.SectionID AND r.Status <> "Dropped") AS Enrolled
                FROM section sec
                JOIN course c    ON c.CourseID = sec.CourseID
                JOIN faculty f   ON f.FacultyID = sec.FacultyID
                JOIN classroom cl ON cl.RoomID = sec.RoomID
                JOIN semester sem ON sem.SemesterID = sec.SemesterID
                WHERE 1 = 1';

        $params  = [];
        $filters = [
            'course'   => 'sec.CourseID',
            'semester' => 'sec.SemesterID',
            'faculty'  => 'sec.FacultyID',
            'room'     => 'sec.RoomID',
        ];

        foreach ($filters as $key => $column) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                $sql .= " AND {$column} = ?";
                $params[] = $_GET[$key];
            }
        }

        $sql .= ' ORDER BY c.CourseCode ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT sec.SectionID, sec.SectionNumber, sec.ScheduleDay, sec.StartTime, sec.EndTime,
                    sec.CourseID, c.CourseCode, c.CourseName, c.Credit,
                    sec.FacultyID, f.FacultyName,
                    sec.RoomID, cl.RoomNumber, cl.Building, cl.Capacity,
                    sec.SemesterID, sem.SemesterName, sem.Year,
                    (SELECT COUNT(*) FROM registration r
                      WHERE r.SectionID = sec.SectionID AND r.Status <> "Dropped") AS Enrolled
             FROM section sec
             JOIN course c     ON c.CourseID = sec.CourseID
             JOIN faculty f    ON f.FacultyID = sec.FacultyID
             JOIN classroom cl ON cl.RoomID = sec.RoomID
             JOIN semester sem ON sem.SemesterID = sec.SemesterID
             WHERE sec.SectionID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}