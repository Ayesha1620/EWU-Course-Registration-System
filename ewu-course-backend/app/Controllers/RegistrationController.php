<?php

namespace App\Controllers;

use App\Auth;
use App\Controller;
use App\Models\Approval;
use App\Models\Registration;
use PDO;

class RegistrationController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Registration($db);
    }

    // index — student নিজেরটা, advisor তার advisee-দেরটা, faculty সব দেখে
    public function index(): void
    {
        $user = Auth::user();

        $sql = 'SELECT r.RegistrationID, r.RegistrationDate, r.Status AS RegStatus, r.DropDate,
                       sec.SectionID, sec.SectionNumber, sec.ScheduleDay, sec.StartTime, sec.EndTime,
                       c.CourseCode, c.CourseName, c.Credit,
                       f.FacultyName,
                       cl.RoomNumber, cl.Building,
                       sem.SemesterName, sem.Year,
                       s.StudentID, s.StudentName,
                       g.GradeLetter, g.GradePoint,
                       ap.ApprovalStatus
                FROM registration r
                JOIN student s   ON s.StudentID = r.StudentID
                JOIN section sec ON sec.SectionID = r.SectionID
                JOIN course c    ON c.CourseID = sec.CourseID
                JOIN faculty f   ON f.FacultyID = sec.FacultyID
                JOIN classroom cl ON cl.RoomID = sec.RoomID
                JOIN semester sem ON sem.SemesterID = sec.SemesterID
                LEFT JOIN grade g    ON g.RegistrationID = r.RegistrationID
                LEFT JOIN approval ap ON ap.RegistrationID = r.RegistrationID';

        $params = [];

        if ($user['role'] === 'student') {
            $sql .= ' WHERE r.StudentID = ?';
            $params[] = $user['StudentID'];
        } elseif ($user['role'] === 'advisor') {
            $sql .= ' WHERE s.AdvisorID = ?';
            $params[] = $user['AdvisorID'];
        }

        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $sql .= ($params ? ' AND' : ' WHERE') . ' r.Status = ?';
            $params[] = $_GET['status'];
        }

        $sql .= ' ORDER BY r.RegistrationDate DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        json_success($stmt->fetchAll());
    }

    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT r.RegistrationID, r.RegistrationDate, r.Status AS RegStatus, r.DropDate,
                    sec.SectionID, sec.SectionNumber, sec.ScheduleDay, sec.StartTime, sec.EndTime,
                    c.CourseCode, c.CourseName, c.Credit,
                    f.FacultyName,
                    cl.RoomNumber, cl.Building,
                    sem.SemesterName, sem.Year,
                    s.StudentID, s.StudentName,
                    g.GradeLetter, g.GradePoint,
                    ap.ApprovalStatus
             FROM registration r
             JOIN student s    ON s.StudentID = r.StudentID
             JOIN section sec  ON sec.SectionID = r.SectionID
             JOIN course c     ON c.CourseID = sec.CourseID
             JOIN faculty f    ON f.FacultyID = sec.FacultyID
             JOIN classroom cl ON cl.RoomID = sec.RoomID
             JOIN semester sem ON sem.SemesterID = sec.SemesterID
             LEFT JOIN grade g     ON g.RegistrationID = r.RegistrationID
             LEFT JOIN approval ap ON ap.RegistrationID = r.RegistrationID
             WHERE r.RegistrationID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Not found', 404);
        }

        // student শুধু নিজের রেজিস্ট্রেশন দেখতে পারবে
        $user = Auth::user();
        if ($user['role'] === 'student' && (int)$user['StudentID'] !== (int)$row['StudentID']) {
            json_error('You can only view your own registrations', 403);
        }

        json_success($row);
    }

    // POST /api/registrations  body: { "section_id": 10001 }
    // student নিজের জন্য register করে, staff চাইলে student_id দিতে পারে
    public function store(): void
    {
        $user = Auth::user();
        $data = $this->input();

        if ($user['role'] === 'student') {
            $studentId = (int)$user['StudentID'];
        } else {
            $studentId = (int)($data['student_id'] ?? 0);
            if ($studentId < 1) {
                json_error('student_id is required for staff registration', 422);
            }
        }

        $sectionId = (int)($data['section_id'] ?? 0);
        if ($sectionId < 1) {
            json_error('section_id is required', 422);
        }

        $section = $this->sectionDetails($sectionId);
        if (!$section) {
            json_error('Section not found', 404);
        }

        // 1) রেজিস্ট্রেশনের সময়সীমা (semester-এর RegistrationStart/End)
        $today = date('Y-m-d');
        if ($today < $section['RegistrationStart'] || $today > $section['RegistrationEnd']) {
            json_error(
                'Registration window is closed. Open from ' . $section['RegistrationStart']
                . ' to ' . $section['RegistrationEnd'],
                422
            );
        }

        // 2) duplicate check — একই section এ আবার register করা যাবে না
        $stmt = $this->db->prepare(
            'SELECT RegistrationID FROM registration
             WHERE StudentID = ? AND SectionID = ? AND Status <> "Dropped"'
        );
        $stmt->execute([$studentId, $sectionId]);
        if ($stmt->fetch()) {
            json_error('This student is already registered for this section', 422);
        }

        // 3) seat capacity check — classroom-এর capacity থেকে বেশি নয়
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM registration
             WHERE SectionID = ? AND Status <> "Dropped"'
        );
        $stmt->execute([$sectionId]);
        if ((int)$stmt->fetchColumn() >= (int)$section['Capacity']) {
            json_error('Section is full (capacity: ' . $section['Capacity'] . ')', 422);
        }

        // 4) schedule conflict check — একই সময়ে দুইটা class রাখা যাবে না
        $conflict = $this->findScheduleConflict($studentId, $section);
        if ($conflict !== null) {
            json_error(
                'Schedule conflict with ' . $conflict['CourseCode']
                . ' (day ' . $conflict['ScheduleDay']
                . ', ' . $conflict['StartTime'] . ' - ' . $conflict['EndTime'] . ')',
                422
            );
        }

        // 5) prerequisite check — prereq কোর্সে কমপক্ষে C (2.00) থাকতে হবে
        $missing = $this->missingPrerequisites($studentId, (int)$section['CourseID']);
        if (count($missing) > 0) {
            json_error('Prerequisite(s) not completed: ' . implode(', ', $missing), 422);
        }

        // সব check পাস — রেজিস্ট্রেশন তৈরি + advisor-এর জন্য pending approval
        $row = $this->model->create([
            'StudentID'        => $studentId,
            'SectionID'        => $sectionId,
            'RegistrationDate' => $today,
            'Status'           => 'Registered',
        ]);

        $this->createPendingApproval($studentId, (int)$row['RegistrationID']);

        json_success([
            'RegistrationID' => $row['RegistrationID'],
            'StudentID'      => $studentId,
            'SectionID'      => $sectionId,
        ], 'Registered successfully', 201);
    }

    // POST /api/registrations/{id}/drop — কোর্স ড্রপ করা
    public function drop($id): void
    {
        $user = Auth::user();

        $stmt = $this->db->prepare(
            'SELECT r.*, s.AdvisorID AS StudentAdvisor
             FROM registration r
             JOIN student s ON s.StudentID = r.StudentID
             WHERE r.RegistrationID = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Registration not found', 404);
        }

        // student শুধু নিজেরটা drop করতে পারবে
        if ($user['role'] === 'student' && (int)$user['StudentID'] !== (int)$row['StudentID']) {
            json_error('You can only drop your own registrations', 403);
        }

        $this->model->update((int)$id, [
            'Status'   => 'Dropped',
            'DropDate' => date('Y-m-d'),
        ]);

        // তারপরেও approval থাকলে সেটাও marked
        $this->db->prepare('UPDATE approval SET ApprovalStatus = "Dropped" WHERE RegistrationID = ?')
            ->execute([$id]);

        json_success(['RegistrationID' => (int)$id], 'Course dropped');
    }

    // ---- helpers ----

    private function sectionDetails(int $sectionId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT sec.*, cl.Capacity, sem.SemesterName, sem.RegistrationStart, sem.RegistrationEnd
             FROM section sec
             JOIN classroom cl ON cl.RoomID = sec.RoomID
             JOIN semester sem ON sem.SemesterID = sec.SemesterID
             WHERE sec.SectionID = ?'
        );
        $stmt->execute([$sectionId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function findScheduleConflict(int $studentId, array $newSection): ?array
    {
        if ($newSection['ScheduleDay'] === null
            || $newSection['StartTime'] === null
            || $newSection['EndTime'] === null) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT sec.SectionID, sec.ScheduleDay, sec.StartTime, sec.EndTime, c.CourseCode
             FROM registration r
             JOIN section sec ON sec.SectionID = r.SectionID
             JOIN course c    ON c.CourseID = sec.CourseID
             WHERE r.StudentID = ? AND r.Status = "Registered"
               AND sec.SemesterID = ?'
        );
        $stmt->execute([$studentId, $newSection['SemesterID']]);

        foreach ($stmt->fetchAll() as $existing) {
            if ($this->timeOverlaps($newSection, $existing)) {
                return $existing;
            }
        }
        return null;
    }

    // সময় (দিন+ঘণ্টা) overlap হয়েছে কিনা
    private function timeOverlaps(array $a, array $b): bool
    {
        $aDays = $this->daysInRange($a['ScheduleDay']);
        $bDays = $this->daysInRange($b['ScheduleDay']);
        if (count(array_intersect($aDays, $bDays)) === 0) {
            return false;
        }

        $aStart = strtotime($a['StartTime']);
        $aEnd   = strtotime($a['EndTime']);
        $bStart = strtotime($b['StartTime']);
        $bEnd   = strtotime($b['EndTime']);

        return $aStart < $bEnd && $aEnd > $bStart;
    }

    // 'Sun-Tue' -> ['Sun','Mon','Tue'], 'Mon-Wed' -> ['Mon','Tue','Wed']
    private function daysInRange(string $dayRange): array
    {
        $week    = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $parts   = explode('-', $dayRange);
        if (count($parts) !== 2) {
            return [];
        }

        $start = (int)array_search($parts[0], $week, true);
        $end   = (int)array_search($parts[1], $week, true);
        if ($start < 0 || $end < 0) {
            return [];
        }

        $days = [];
        for ($i = $start; $i <= $end; $i++) {
            $days[] = $week[$i % 7];
        }
        return $days;
    }

    // যেসব prereq এ এখনও পাস করা হয়নি তা return করে
    private function missingPrerequisites(int $studentId, int $courseId): array
    {
        $stmt = $this->db->prepare('SELECT PrerequisiteCourseID FROM prerequisite WHERE CourseID = ?');
        $stmt->execute([$courseId]);
        $prerequisites = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $missing = [];
        foreach ($prerequisites as $prereqId) {
            $stmt = $this->db->prepare(
                'SELECT g.GradePoint
                 FROM grade g
                 JOIN registration r ON r.RegistrationID = g.RegistrationID
                 JOIN section s      ON s.SectionID = r.SectionID
                 WHERE r.StudentID = ? AND s.CourseID = ? AND r.Status <> "Dropped"'
            );
            $stmt->execute([$studentId, $prereqId]);
            $grade = $stmt->fetch();

            if (!$grade || (float)$grade['GradePoint'] < 2.00) {
                $stmt = $this->db->prepare('SELECT CourseCode FROM course WHERE CourseID = ?');
                $stmt->execute([$prereqId]);
                $missing[] = $stmt->fetchColumn() ?: '#' . $prereqId;
            }
        }
        return $missing;
    }

    // রেজিস্ট্রেশন হলে student-এর advisor-এর জন্য 'Pending' approval তৈরি
    private function createPendingApproval(int $studentId, int $registrationId): void
    {
        $stmt = $this->db->prepare('SELECT AdvisorID FROM student WHERE StudentID = ?');
        $stmt->execute([$studentId]);
        $advisorId = $stmt->fetchColumn();
        if (!$advisorId) {
            return; // advisor নাই, review-এর কিছুই নাই
        }

        $approval = new Approval($this->db);
        $approval->create([
            'RegistrationID' => $registrationId,
            'AdvisorID'      => (int)$advisorId,
            'ApprovalStatus' => 'Pending',
            'ApprovalDate'   => null,
            'Remarks'        => 'Waiting for advisor review',
        ]);
    }
}