<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_status', function (Blueprint $table) {
            $table->primary('id');
            $table->tinyInteger('id');
            $table->char('name','5')->comment('报销状态');
        });
        DB::statement("ALTER TABLE`bx_reimbursement_status` comment'报销单状态表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reimbursement_status');
    }
}
