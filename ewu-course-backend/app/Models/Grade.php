<?php

namespace App\Models;

use App\Model;

class Grade extends Model
{
    protected $table = 'grade';
    protected $primaryKey = 'GradeID';
    protected $fillable = ['GradeID', 'RegistrationID', 'QuizMark', 'MidMark', 'FinalMark', 'GradeLetter', 'GradePoint'];
}