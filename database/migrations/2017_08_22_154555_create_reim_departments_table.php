<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reim_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name','20')->comment('资金归属名');
            $table->dateTime('deleted_at')->nullable()->comment('软删除');
        });
        DB::statement("ALTER TABLE`bx_reim_departments` comment'资金归属表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reim_departments');
    }
}
