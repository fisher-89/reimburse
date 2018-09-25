<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';

    public function reim_department()
    {
        return $this->belongsTo('App\Models\Reim_department')->withTrashed();
    }

    public function approver1()
    {
        return $this->hasMany('App\Models\Approver')->where('priority', 1);
    }

    public function approver2()
    {
        return $this->hasMany('App\Models\Approver')->where('priority', 2);
    }

    public function approver3()
    {
        return $this->hasMany('App\Models\Approver')->where('priority', 3);
    }
}
