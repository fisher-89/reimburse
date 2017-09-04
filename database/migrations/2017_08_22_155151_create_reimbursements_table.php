<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->index(['status_id','staff_sn','reim_department_id']);
            $table->increments('id');
            $table->char('reim_sn','20')->default('')->comment('订单编号');
            $table->char('description','20')->default('')->comment('描述（标题）');
            $table->char('remark','150')->default('')->comment('备注');
            $table->tinyInteger('status_id')->default(0)->comment('报销单状态(-1驳回，0未提交，1待审批, 2已审批, 3待审核，4已审核)');
            $table->char('staff_sn',6)->default('')->comment('员工编号');
            $table->char('realname',10)->default('')->comment('员工名字');
            $table->integer('department_id')->nullable()->comment('部门id');
            $table->char('department_name',100)->default('')->comment('所属部门名字');
            $table->char('approver_staff_sn',6)->default('')->comment('审批人的员工编号');
            $table->char('approver_name',10)->default('')->comment('审批人姓名');
            $table->char('accountant_staff_sn',6)->default('')->comment('审核人员工编号');
            $table->char('accountant_name',10)->default('')->comment('审核人姓名');
            $table->tinyInteger('reim_department_id')->unsigned()->comment('资金归属id');
            $table->decimal('send_cost',8,2)->nullable()->comment('申请提交费用');
            $table->decimal('approved_cost',8,2)->nullable()->comment('审批通过费用');
            $table->decimal('audited_cost',8,2)->nullable()->comment('审核金额');
            $table->dateTime('create_time')->comment('创建时间');
            $table->dateTime('send_time')->nullable()->comment('提交时间');
            $table->dateTime('approve_time')->nullable()->comment('审批时间');
            $table->dateTime('audit_time')->nullable()->comment('审核时间');
            $table->char('reject_staff_sn',6)->default('')->comment('驳回人的员工编号');
            $table->char('reject_name',10)->default('')->comment('驳回人名字');
            $table->dateTime('reject_time')->nullable()->comment('驳回时间');
            $table->text('reject_remarks')->nullable()->comment('驳回原因');
            $table->tinyInteger('is_delete')->unsigned()->default(0)->comment('是否删除（0否，1是）');
            $table->tinyInteger('is_homepage')->unsigned()->default(1)->comment('是否在首页显示 0：否 1：是');
            $table->tinyInteger('accountant_delete')->unsigned()->default(0)->comment('是否被审核人删除');
            $table->timestamps();
            $table->integer('payee_id')->nullable()->unsigned()->comment('收款人id');
            $table->char('payee_name',5)->default('')->comment('收款人');
            $table->char('payee_bank_account',30)->default('')->comment('收款人卡号');
            $table->char('payee_bank_other',10)->default('')->comment('收款人银行');
            $table->char('payee_phone',11)->default('')->comment('收款人手机');
            $table->char('payee_province',10)->default('')->comment('账户所在省');
            $table->char('payee_city',15)->default('')->comment('账户所在市');
            $table->char('payee_bank_dot',20)->default('')->comment('账户所属网点');
            $table->tinyInteger('print_count')->unsigned()->default(0)->comment('打印次数');
        });
        DB::statement("ALTER TABLE`bx_reimbursements` comment'报销单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reimbursements');
    }
}
