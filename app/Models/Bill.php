<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model {

    use SoftDeletes;
    public $timestamps = false;
    protected $fillable = ['pic_path', 'expense_id'];

    /* ----- 访问器Start ----- */

//    public function getPicPathAttribute($value) {
//        $picPathArr = explode(';', $value);
//        if (empty($picPathArr[0])) {
//            $picPathArr = [];
//        }
//        return $picPathArr;
//    }

    /* ----- 访问器End ----- */
}
