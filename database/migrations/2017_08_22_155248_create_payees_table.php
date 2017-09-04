<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payees', function (Blueprint $table) {
            $table->index('staff_sn');
            $table->increments('id');
            $table->char('bank_account','30')->comment('银行账号');
            $table->char('bank_account_name','5')->comment('银行户名');
            $table->char('bank_other','10')->comment('银行类型');
            $table->integer('province_of_account')->unsigned()->comment('账户所在省');
            $table->integer('city_of_account')->unsigned()->nullable()->comment('账户所在市');
            $table->char('bank_dot','30')->nullable()->comment('账户所属网点');
            $table->char('phone','11')->comment('手机');
            $table->integer('staff_sn')->unsigned()->comment('员工编号');
            $table->tinyInteger('is_default')->unsigned()->default(0)->comment('是否默认收款人(0:否，1:是)');
        });
        DB::statement("ALTER TABLE`bx_payees` comment'收款人数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payees');
    }
}
