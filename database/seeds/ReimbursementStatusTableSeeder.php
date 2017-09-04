<?php

use Illuminate\Database\Seeder;

class ReimbursementStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data =[
          ['id'=>0,'name'=>'未提交'],
          ['id'=>1,'name'=>'待审批'],
          ['id'=>2,'name'=>'已审批'],
          ['id'=>3,'name'=>'待审核'],
          ['id'=>4,'name'=>'已审核'],
          ['id'=>-1,'name'=>'被驳回'],
        ];
        DB::table('reimbursement_status')->insert($data);
    }
}
