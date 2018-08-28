<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->char('second_rejecter_staff_sn', 6)->default('')->comment('财务之后驳回人员工编号');
            $table->char('second_rejecter_name', 10)->default('')->comment('财务之后驳回人名字');
            $table->dateTime('second_rejected_at')->nullable()->comment('财务之后驳回时间');
            $table->text('second_reject_remarks')->nullable()->comment('财务之后驳回原因');
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
            $table->dropColumn(['second_rejecter_staff_sn', 'second_rejecter_name', 'second_rejected_at', 'second_reject_remarks']);
        });
    }
}
