<?php

use Illuminate\Database\Seeder;

class ReimDepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name'=>'LS专卖'],
            ['name'=>'利鲨快销'],
            ['name'=>'GO专卖'],
            ['name'=>'JV'],
            ['name'=>'GO批发'],
            ['name'=>'杰尼威尼专卖'],
            ['name'=>'总公司'],
            ['name'=>'电商版块'],
        ];
        DB::table('reim_departments')->insert($data);
    }
}
