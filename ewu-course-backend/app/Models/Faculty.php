<?php

namespace App\Models;

use App\Model;

class Faculty extends Model
{
    protected $table = 'faculty';
    protected $primaryKey = 'FacultyID';
    protected $fillable = ['FacultyID', 'FacultyName', 'Email', 'Phone', 'Designation', 'DepartmentID'];
}