<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvers', function (Blueprint $table) {
            $table->index('priority');
            $table->increments('id');
            $table->integer('staff_sn')->unsigned()->comment('审批人编号');
            $table->char('realname','10')->comment('审批人名字');
            $table->tinyInteger('priority')->unsigned()->comment('审批优先等级(3,2,1)');
            $table->integer('department_id')->unsigned()->comment('部门表（departments表的主键id）');
        });
        DB::statement("ALTER TABLE`bx_approvers` comment'部门审批人数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('approvers');
    }
}
