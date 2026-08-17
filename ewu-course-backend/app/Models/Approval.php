<?php

namespace App\Models;

use App\Model;

class Approval extends Model
{
    protected $table = 'approval';
    protected $primaryKey = 'ApprovalID';
    protected $fillable = ['ApprovalID', 'RegistrationID', 'AdvisorID', 'ApprovalStatus', 'ApprovalDate', 'Remarks'];
}