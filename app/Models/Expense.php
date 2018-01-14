<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Expense extends Model {

    use SoftDeletes;

    public $timestamps = false;

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
//    protected $dates = ['date'];

//    public function getDateAttribute($value) {
//        return date('Y-m-d', $value);
//    }

    /* ----- 定义关联 ----- */

    public function bills() {
        return $this->hasMany('App\Models\Bill','expense_id');
    }

    public function type() {
        return $this->belongsTo('App\Models\Expense_type');
    }

//    public function reimbursement() {
//        return $this->belongsTo('App\Models\Reimbursement');
//    }

    /* ----- 定义关联End ----- */


}
