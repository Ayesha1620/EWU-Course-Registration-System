<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Classroom;
use PDO;

class ClassroomController extends Controller
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Classroom($db);
        $this->rules = ['RoomNumber'];
    }
}