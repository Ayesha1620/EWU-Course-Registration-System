<?php

namespace App\Models;

use App\Model;

class Classroom extends Model
{
    protected $table = 'classroom';
    protected $primaryKey = 'RoomID';
    protected $fillable = ['RoomID', 'RoomNumber', 'Building', 'Capacity', 'RoomType'];
}