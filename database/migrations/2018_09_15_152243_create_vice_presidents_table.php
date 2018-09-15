<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVicePresidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vice_presidents', function (Blueprint $table) {
            $table->increments('id');
            $table->char('staff_sn',6)->default('')->comment('副总工号');
            $table->char('name',10)->default('')->comment('副总名字');
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vice_presidents');
    }
}
