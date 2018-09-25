<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReimDepartment extends Model
{
    public $timestamps = false;
    use SoftDeletes;

    public function auditor()
    {
        return $this->hasMany('App\Models\Auditor', 'reim_department_id');
    }
}
