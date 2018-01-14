<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->index('expense_id');
            $table->increments('id');
            $table->char('pic_path',40)->default('')->comment('发票相对路径');
            $table->integer('expense_id')->unsigned()->comment('明细id');
        });
        DB::statement("alter table`bx_bills` comment'发票表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bills');
    }
}
