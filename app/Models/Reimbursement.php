<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Reimbursement extends Model {

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
//    protected $dates = ['send_time', 'approve_time', 'audit_time', 'reject_time', 'create_time'];

    /* ----- 定义关联Start ----- */

    public function status() {//报销单状态
        return $this->belongsTo('App\Models\Reimbursement_status', 'status_id');
    }

    public function expenses() {//消费明细
        return $this->hasMany('App\Models\Expense', 'reim_id')->orderBy('date', 'asc');
    }

    public function reim_department() {//资金归属
        return $this->belongsTo('App\Models\ReimDepartment')->withTrashed();
    }

    /* ----- 定义关联End ----- */


}
