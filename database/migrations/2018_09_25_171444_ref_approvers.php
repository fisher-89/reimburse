<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefApprovers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvers', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropIndex('approvers_priority_index');
            $table->primary(['staff_sn', 'priority', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approvers', function (Blueprint $table) {
            $table->dropPrimary();
            $table->index('priority');
            $table->increments('id');
        });
    }
}
