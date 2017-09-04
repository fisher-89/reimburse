<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payee extends Model {

    public $timestamps = false;

    public function province(){
        return $this->belongsTo('App\Models\Region','province_of_account');
    }
    public function city(){
        return $this->belongsTo('App\Models\Region','city_of_account');
    }
}
