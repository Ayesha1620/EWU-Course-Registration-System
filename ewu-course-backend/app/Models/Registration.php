<?php

namespace App\Models;

use App\Model;

class Registration extends Model
{
    protected $table = 'registration';
    protected $primaryKey = 'RegistrationID';
    protected $fillable = ['RegistrationID', 'StudentID', 'SectionID', 'RegistrationDate', 'Status', 'DropDate'];
}