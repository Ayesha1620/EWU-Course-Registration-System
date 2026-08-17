<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Prerequisite;
use PDO;

class PrerequisiteController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Prerequisite($db);
    }

    // সব prerequisite pair, কোর্স কোডসহ
    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT p.CourseID, c1.CourseCode AS CourseCode, c1.CourseName AS CourseName,
                    p.PrerequisiteCourseID, c2.CourseCode AS PrerequisiteCode
             FROM prerequisite p
             JOIN course c1 ON c1.CourseID = p.CourseID
             JOIN course c2 ON c2.CourseID = p.PrerequisiteCourseID
             ORDER BY c1.CourseCode'
        );
        json_success($stmt->fetchAll());
    }

    // একটা course-এর সব prerequisite
    public function show($id): void
    {
        $stmt = $this->db->prepare(
            'SELECT p.PrerequisiteCourseID, c2.CourseCode AS PrerequisiteCode, c2.CourseName AS PrerequisiteName
             FROM prerequisite p
             JOIN course c2 ON c2.CourseID = p.PrerequisiteCourseID
             WHERE p.CourseID = ?'
        );
        $stmt->execute([$id]);
        json_success($stmt->fetchAll());
    }

    public function store(): void
    {
        $data = $this->input();
        if (empty($data['course_id']) || empty($data['prerequisite_course_id'])) {
            json_error('course_id and prerequisite_course_id are required', 422);
        }

        // duplicate pair block
        $existing = $this->model->where('CourseID', $data['course_id']);
        foreach ($existing as $row) {
            if ((int)$row['PrerequisiteCourseID'] === (int)$data['prerequisite_course_id']) {
                json_error('This prerequisite already exists', 422);
            }
        }

        $row = $this->model->create([
            'CourseID'             => (int)$data['course_id'],
            'PrerequisiteCourseID' => (int)$data['prerequisite_course_id'],
        ]);
        json_success($row, 'Prerequisite added', 201);
    }

    // DELETE /api/prerequisites/{courseId}/{prerequisiteCourseId}
    // composite key (CourseID + PrerequisiteCourseID) — দুটোই matched হতে হবে
    public function destroy($courseId, $prerequisiteCourseId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM prerequisite WHERE CourseID = ? AND PrerequisiteCourseID = ?'
        );
        $stmt->execute([$courseId, $prerequisiteCourseId]);
        json_success([], 'Removed');
    }
}