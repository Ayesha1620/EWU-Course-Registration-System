<?php

// ============ Route list ============
// format:  $router-><method>('/api/...', [ControllerClass::class, 'method'], [allowed_roles]);

use App\Controllers\AdvisorController;
use App\Controllers\ApprovalController;
use App\Controllers\AuthController;
use App\Controllers\ClassroomController;
use App\Controllers\CourseController;
use App\Controllers\DepartmentController;
use App\Controllers\FacultyController;
use App\Controllers\GradeController;
use App\Controllers\HealthController;
use App\Controllers\PrerequisiteController;
use App\Controllers\RegistrationController;
use App\Controllers\SectionController;
use App\Controllers\SemesterController;
use App\Controllers\StudentController;

// role groups
$PUBLIC  = [];                                   // login লাগে না
$ANY     = ['student', 'advisor', 'faculty'];    // যে কোনো logged-in user
$STAFF   = ['advisor', 'faculty'];               // faculty (আর advisor)
$ADVISOR = ['advisor'];

// ============ Auth ============
$router->post('/api/auth/login', [AuthController::class, 'login'], $PUBLIC);
$router->post('/api/auth/logout', [AuthController::class, 'logout'], $ANY);
$router->get('/api/auth/me', [AuthController::class, 'me'], $ANY);
$router->get('/api/health', [HealthController::class, 'index'], $PUBLIC);

// ============ Departments ============
$router->get('/api/departments', [DepartmentController::class, 'index'], $ANY);
$router->get('/api/departments/{id}', [DepartmentController::class, 'show'], $ANY);
$router->post('/api/departments', [DepartmentController::class, 'store'], $STAFF);
$router->put('/api/departments/{id}', [DepartmentController::class, 'update'], $STAFF);
$router->delete('/api/departments/{id}', [DepartmentController::class, 'destroy'], $STAFF);

// ============ Faculty ============
$router->get('/api/faculty', [FacultyController::class, 'index'], $ANY);
$router->get('/api/faculty/{id}', [FacultyController::class, 'show'], $ANY);
$router->post('/api/faculty', [FacultyController::class, 'store'], $STAFF);
$router->put('/api/faculty/{id}', [FacultyController::class, 'update'], $STAFF);
$router->delete('/api/faculty/{id}', [FacultyController::class, 'destroy'], $STAFF);

// ============ Students ============
$router->get('/api/students', [StudentController::class, 'index'], $ANY);
$router->get('/api/students/{id}', [StudentController::class, 'show'], $ANY);
$router->post('/api/students', [StudentController::class, 'store'], $STAFF);
$router->put('/api/students/{id}', [StudentController::class, 'update'], $STAFF);
$router->delete('/api/students/{id}', [StudentController::class, 'destroy'], $STAFF);

// ============ Advisors ============
$router->get('/api/advisors', [AdvisorController::class, 'index'], $ANY);
$router->get('/api/advisors/{id}', [AdvisorController::class, 'show'], $ANY);
$router->post('/api/advisors', [AdvisorController::class, 'store'], $STAFF);
$router->put('/api/advisors/{id}', [AdvisorController::class, 'update'], $STAFF);
$router->delete('/api/advisors/{id}', [AdvisorController::class, 'destroy'], $STAFF);

// ============ Courses ============
$router->get('/api/courses', [CourseController::class, 'index'], $ANY);
$router->get('/api/courses/{id}', [CourseController::class, 'show'], $ANY);
$router->post('/api/courses', [CourseController::class, 'store'], $STAFF);
$router->put('/api/courses/{id}', [CourseController::class, 'update'], $STAFF);
$router->delete('/api/courses/{id}', [CourseController::class, 'destroy'], $STAFF);

// ============ Prerequisites ============
$router->get('/api/prerequisites', [PrerequisiteController::class, 'index'], $ANY);
$router->get('/api/prerequisites/{id}', [PrerequisiteController::class, 'show'], $ANY);
$router->post('/api/prerequisites', [PrerequisiteController::class, 'store'], $STAFF);
$router->delete(
    '/api/prerequisites/{courseId}/{prerequisiteCourseId}',
    [PrerequisiteController::class, 'destroy'],
    $STAFF
);

// ============ Semesters ============
$router->get('/api/semesters', [SemesterController::class, 'index'], $ANY);
$router->get('/api/semesters/{id}', [SemesterController::class, 'show'], $ANY);
$router->post('/api/semesters', [SemesterController::class, 'store'], $STAFF);
$router->put('/api/semesters/{id}', [SemesterController::class, 'update'], $STAFF);
$router->delete('/api/semesters/{id}', [SemesterController::class, 'destroy'], $STAFF);

// ============ Classrooms ============
$router->get('/api/classrooms', [ClassroomController::class, 'index'], $ANY);
$router->get('/api/classrooms/{id}', [ClassroomController::class, 'show'], $ANY);
$router->post('/api/classrooms', [ClassroomController::class, 'store'], $STAFF);
$router->put('/api/classrooms/{id}', [ClassroomController::class, 'update'], $STAFF);
$router->delete('/api/classrooms/{id}', [ClassroomController::class, 'destroy'], $STAFF);

// ============ Sections ============
$router->get('/api/sections', [SectionController::class, 'index'], $ANY);
$router->get('/api/sections/{id}', [SectionController::class, 'show'], $ANY);
$router->post('/api/sections', [SectionController::class, 'store'], $STAFF);
$router->put('/api/sections/{id}', [SectionController::class, 'update'], $STAFF);
$router->delete('/api/sections/{id}', [SectionController::class, 'destroy'], $STAFF);

// ============ Registrations ============
$router->get('/api/registrations', [RegistrationController::class, 'index'], $ANY);
$router->get('/api/registrations/{id}', [RegistrationController::class, 'show'], $ANY);
$router->post('/api/registrations', [RegistrationController::class, 'store'], $ANY);
$router->post('/api/registrations/{id}/drop', [RegistrationController::class, 'drop'], $ANY);

// ============ Approvals (advising) ============
$router->get('/api/approvals', [ApprovalController::class, 'index'], $STAFF);
$router->put('/api/approvals/{registrationId}', [ApprovalController::class, 'review'], $ADVISOR);

// ============ Grades ============
$router->get('/api/grades', [GradeController::class, 'index'], $ANY);
$router->get('/api/grades/{id}', [GradeController::class, 'show'], $ANY);
$router->post('/api/grades', [GradeController::class, 'store'], $STAFF);
$router->put('/api/grades/{id}', [GradeController::class, 'update'], $STAFF);
$router->delete('/api/grades/{id}', [GradeController::class, 'destroy'], $STAFF);