<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    public $timestamps = false;
    protected $fillable = ['staff_sn', 'realname', 'priority'];
}
