<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Department;
use PDO;

class DepartmentController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Department($db);
        $this->rules = ['DepartmentName'];
    }
}