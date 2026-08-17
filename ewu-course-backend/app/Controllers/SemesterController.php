<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Semester;
use PDO;

class SemesterController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Semester($db);
        $this->rules = ['SemesterName', 'Year'];
    }
}