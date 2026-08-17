<?php

namespace App\Models;

use App\Model;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'StudentID';
    protected $fillable = ['StudentID', 'StudentName', 'Email', 'Phone', 'DOB', 'DepartmentID', 'AdvisorID'];
}