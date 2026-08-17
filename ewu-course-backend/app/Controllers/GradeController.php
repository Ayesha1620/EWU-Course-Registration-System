<?php

namespace App\Controllers;

use App\Auth;
use App\Controller;
use App\Models\Grade;
use PDO;

class GradeController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Grade($db);
        $this->rules = ['RegistrationID', 'GradeLetter'];
    }

    // student নিজের grade, faculty/advisor সব বা নির্দিষ্ট student-এর grade দেখে
    public function index(): void
    {
        $user = Auth::user();

        $sql = 'SELECT g.GradeID, g.QuizMark, g.MidMark, g.FinalMark, g.GradeLetter, g.GradePoint,
                       r.RegistrationID, s.StudentID, s.StudentName,
                       c.CourseCode, c.CourseName, c.Credit,
                       sem.SemesterName, sem.Year
                FROM grade g
                JOIN registration r ON r.RegistrationID = g.RegistrationID
                JOIN student s      ON s.StudentID = r.StudentID
                JOIN section sec    ON sec.SectionID = r.SectionID
                JOIN course c       ON c.CourseID = sec.CourseID
                JOIN semester sem   ON sem.SemesterID = sec.SemesterID';

        $params = [];
        if ($user['role'] === 'student') {
            $sql .= ' WHERE r.StudentID = ?';
            $params[] = $user['StudentID'];
        } elseif (isset($_GET['student']) && $_GET['student'] !== '') {
            $sql .= ' WHERE r.StudentID = ?';
            $params[] = $_GET['student'];
        }

        $sql .= ' ORDER BY sem.Year DESC, sem.SemesterID DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT g.GradeID, g.QuizMark, g.MidMark, g.FinalMark, g.GradeLetter, g.GradePoint,
                    r.RegistrationID, s.StudentID, s.StudentName,
                    c.CourseCode, c.CourseName, sem.SemesterName, sem.Year
             FROM grade g
             JOIN registration r ON r.RegistrationID = g.RegistrationID
             JOIN student s      ON s.StudentID = r.StudentID
             JOIN section sec    ON sec.SectionID = r.SectionID
             JOIN course c       ON c.CourseID = sec.CourseID
             JOIN semester sem   ON sem.SemesterID = sec.SemesterID
             WHERE g.GradeID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }
}