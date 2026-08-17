<?php

namespace App\Controllers;

use App\Auth;
use App\Controller;
use App\Models\Approval;
use PDO;

class ApprovalController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Approval($db);
    }

    // advisor তার advisee-দের রেজিস্ট্রেশন + approval status দেখে
    public function index(): void
    {
        $user = Auth::user();

        // approval ব্যবস্থা শুধু faculty-দের জন্য; student দেখতে পাবে না
        if ($user['role'] === 'student') {
            json_error('Only faculty/advisor can view approvals', 403);
        }

        $sql = 'SELECT r.RegistrationID, r.RegistrationDate, r.Status AS RegStatus,
                       s.StudentID, s.StudentName, s.Email AS StudentEmail,
                       sec.SectionID, sec.SectionNumber, sec.ScheduleDay, sec.StartTime, sec.EndTime,
                       c.CourseCode, c.CourseName, c.Credit,
                       ap.ApprovalID, ap.ApprovalStatus, ap.ApprovalDate, ap.Remarks
                FROM registration r
                JOIN student s   ON s.StudentID = r.StudentID
                JOIN section sec ON sec.SectionID = r.SectionID
                JOIN course c    ON c.CourseID = sec.CourseID
                LEFT JOIN approval ap ON ap.RegistrationID = r.RegistrationID';

        $params = [];
        if ($user['role'] === 'advisor') {
            $sql .= ' WHERE s.AdvisorID = ?';
            $params[] = $user['AdvisorID'];
        }

        $sql .= ' ORDER BY r.RegistrationDate DESC, s.StudentName ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        json_success($stmt->fetchAll());
    }

    // PUT /api/approvals/{registrationId}  body: { "status": "Approved|Rejected|Pending", "remarks": "" }
    public function review($registrationId): void
    {
        $user    = Auth::user();
        $data    = $this->input();
        $status  = strtoupper(trim($data['status'] ?? ''));
        $remarks = $data['remarks'] ?? null;

        if (!in_array($status, ['Approved', 'Rejected', 'Pending'], true)) {
            json_error('status must be Approved, Rejected or Pending', 422);
        }

        // রেজিস্ট্রেশন আছে কিনা, আর student টা এই advisor-এর কিনা
        $stmt = $this->db->prepare(
            'SELECT r.RegistrationID, s.AdvisorID
             FROM registration r
             JOIN student s ON s.StudentID = r.StudentID
             WHERE r.RegistrationID = ?'
        );
        $stmt->execute([$registrationId]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Registration not found', 404);
        }
        if ((int)$row['AdvisorID'] !== (int)$user['AdvisorID']) {
            json_error('This student is not one of your advisees', 403);
        }

        $approvalDate = in_array($status, ['Approved', 'Rejected'], true) ? date('Y-m-d') : null;

        // আগে approval ছিল কিনা দেখে update বা create
        $stmt = $this->db->prepare('SELECT ApprovalID FROM approval WHERE RegistrationID = ?');
        $stmt->execute([$registrationId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $this->model->update((int)$existing['ApprovalID'], [
                'ApprovalStatus' => $status,
                'ApprovalDate'   => $approvalDate,
                'Remarks'        => $remarks,
            ]);
        } else {
            $this->model->create([
                'RegistrationID' => (int)$registrationId,
                'AdvisorID'      => (int)$user['AdvisorID'],
                'ApprovalStatus' => $status,
                'ApprovalDate'   => $approvalDate,
                'Remarks'        => $remarks,
            ]);
        }

        json_success(['RegistrationID' => (int)$registrationId], "Approval set to {$status}");
    }
}