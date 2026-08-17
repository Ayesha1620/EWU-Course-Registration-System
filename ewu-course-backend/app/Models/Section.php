<?php

namespace App\Models;

use App\Model;

class Section extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'SectionID';
    protected $fillable = ['SectionID', 'CourseID', 'FacultyID', 'SemesterID', 'RoomID', 'SectionNumber', 'ScheduleDay', 'StartTime', 'EndTime'];
}