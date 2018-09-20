<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountantRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->char('accountant_remark', '150')->default('')->comment('财务备注');
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
                'accountant_remark',
            ]);
        });
    }
}
