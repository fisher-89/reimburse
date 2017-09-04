<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_types', function (Blueprint $table) {
            $table->primary('id');
            $table->tinyInteger('id')->unsigned();
            $table->char('name','5')->comment('消费类型');
            $table->char('pic_path','30')->comment('类型图片路径');
        });
        DB::statement("ALTER TABLE`bx_expense_types` comment'消费类型表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('expense_types');
    }
}
