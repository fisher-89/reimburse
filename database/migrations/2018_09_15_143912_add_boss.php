<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->char('finance_approved_sn',6)->index()->default('')->comment('终极boss审批员工工号');
            $table->char('finance_approved_name',10)->default('')->comment('终极boss审批员工');
            $table->dateTime('finance_approved_at')->nullable()->comment('终极boss审批时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->dropColumn([
               'finance_approved_sn',
               'finance_approved_name',
               'finance_approved_at',
            ]);
        });
    }
}
