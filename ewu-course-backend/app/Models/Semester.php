<?php

namespace App\Models;

use App\Model;

class Semester extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'SemesterID';
    protected $fillable = ['SemesterID', 'SemesterName', 'Year', 'StartDate', 'EndDate', 'RegistrationStart', 'RegistrationEnd'];
}