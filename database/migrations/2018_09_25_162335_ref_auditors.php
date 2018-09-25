<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefAuditors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auditors', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropIndex('auditors_reim_department_id_index');
            $table->primary(['reim_department_id', 'auditor_staff_sn']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auditors', function (Blueprint $table) {
            $table->dropPrimary();
            $table->index('reim_department_id');
            $table->increments('id');
        });
    }
}
