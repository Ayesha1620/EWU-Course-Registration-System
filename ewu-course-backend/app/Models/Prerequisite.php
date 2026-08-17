<?php

namespace App\Models;

use App\Model;

// composite primary key (CourseID + PrerequisiteCourseID) — তাই
// এই table-এর জন্য controller নিজেই query লিখে নেয়।
class Prerequisite extends Model
{
    protected $table = 'prerequisite';
    protected $primaryKey = 'CourseID';
    protected $fillable = ['CourseID', 'PrerequisiteCourseID'];
}