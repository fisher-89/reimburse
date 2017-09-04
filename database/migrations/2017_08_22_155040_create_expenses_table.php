<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->increments('id');
            $table->char('description',200)->commont('备注');
            $table->date('date')->commont('消费日期');
            $table->tinyInteger('type_id')->unsigned()->commont('消费类型id');
            $table->decimal('send_cost','8','2')->comment('申请提交费用');
            $table->decimal('audited_cost','8','2')->nullable()->comment('审核通过费用');
            $table->tinyInteger('is_approved')->unsigned()->default(0)->comment('是否通过审批(0：否  1：是)');
            $table->tinyInteger('is_audited')->unsigned()->default(0)->comment('是否通过审核(0：否  1：是)');
            $table->integer('reim_id')->unsigned()->comment('报销单id');
            $table->integer('staff_sn')->unsigned()->comment('员工编号');
        });
        DB::statement("alter table`bx_expenses` comment'消费明细表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('expenses');
    }
}
