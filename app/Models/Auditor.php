<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    public $timestamps = false;
    protected $fillable = ['auditor_staff_sn', 'auditor_realname'];
}
