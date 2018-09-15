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
            $table->char('staff_sn',6)->comment('副总工号');
            $table->char('name',10)->comment('副总名字');
//            $table->timestamps();
        });
        DB::statement("ALTER TABLE`bx_vice_presidents` comment'品牌副总表'");
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
