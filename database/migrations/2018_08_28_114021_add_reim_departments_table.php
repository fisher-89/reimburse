<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReimDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reim_departments', function (Blueprint $table) {
            $table->mediumInteger('cashier_sn')->comment('出纳员工编号')->nullable();
            $table->char('cashier_name',10)->comment('出纳姓名')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reim_departments', function (Blueprint $table) {
            $table->dropColumn(['cashier_sn','cashier_name']);
        });
    }
}
