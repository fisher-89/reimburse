<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reim_department extends Model
{
    use SoftDeletes;

    public function auditor(){
        return $this->hasMany('App\Models\Auditor','reim_department_id');
    }
}
