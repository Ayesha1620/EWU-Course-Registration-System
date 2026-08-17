<?php

namespace App\Models;

use App\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $primaryKey = 'DepartmentID';
    protected $fillable = ['DepartmentID', 'DepartmentName', 'OfficeLocation', 'OfficePhone'];
}