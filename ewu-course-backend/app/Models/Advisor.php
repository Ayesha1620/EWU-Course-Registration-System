<?php

namespace App\Models;

use App\Model;

class Advisor extends Model
{
    protected $table = 'advisor';
    protected $primaryKey = 'AdvisorID';
    protected $fillable = ['AdvisorID', 'FacultyID', 'StartDate', 'Status'];
}