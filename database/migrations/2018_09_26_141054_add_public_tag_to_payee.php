<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublicTagToPayee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payees', function (Blueprint $table) {
            $table->tinyInteger('is_public')->default(0)->comment('是否为对公账户');
        });

        Schema::table('reimbursements', function (Blueprint $table) {
            $table->tinyInteger('payee_is_public')->default(0)->comment('是否为对公账户');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payees', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });

        Schema::table('reimbursements', function (Blueprint $table) {
            $table->dropColumn('payee_is_public');
        });
    }
}
