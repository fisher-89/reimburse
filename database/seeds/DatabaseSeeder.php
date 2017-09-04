<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(ReimDepartmentsTableSeeder::class);//资金归属
        $this->call(RegionTableSeeder::class);//地区
        $this->call(ExpenseTypesTableSeeder::class);//消费明细类型
        $this->call(ReimbursementStatusTableSeeder::class);//报销状态
    }
}
