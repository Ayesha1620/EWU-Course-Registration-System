<?php

namespace App\Models;

use App\Model;

class Course extends Model
{
    protected $table = 'course';
    protected $primaryKey = 'CourseID';
    protected $fillable = ['CourseID', 'CourseCode', 'CourseName', 'Credit', 'CourseDescription', 'DepartmentID'];
}