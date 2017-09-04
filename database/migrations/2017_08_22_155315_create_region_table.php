<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('region', function (Blueprint $table) {
            $table->mediumInteger('id')->unsigned()->comment('地区编码主键id');
            $table->char('region_name','15')->comment('地区名');
            $table->mediumInteger('parent_id')->unsigned()->comment('地区父级id');
            $table->tinyInteger('level')->unsigned()->comment('区域层次');
            $table->char('full_name',45)->comment('地区全称');
        });
        DB::statement("ALTER TABLE`bx_region` comment'地区表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('region');
    }
}
