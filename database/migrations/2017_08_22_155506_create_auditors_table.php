<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditors', function (Blueprint $table) {
            $table->index('reim_department_id');
            $table->increments('id');
            $table->tinyInteger('reim_department_id')->unsigned()->comment('资金归属id');
            $table->integer('auditor_staff_sn')->unsigned()->comment('审核人编号');
            $table->char('auditor_realname','10')->comment('审核人名字');
        });
        DB::statement("ALTER TABLE`bx_auditors` comment'资金归属审核人表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auditors');
    }
}
