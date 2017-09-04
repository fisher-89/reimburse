<?php

use Illuminate\Database\Seeder;

class ExpenseTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 0, 'name' => '其他', 'pic_path' => 'images/expense_type/0.png'],
            ['id' => 1, 'name' => '飞机', 'pic_path' => 'images/expense_type/1.png'],
            ['id' => 2, 'name' => '火车', 'pic_path' => 'images/expense_type/2.png'],
            ['id' => 3, 'name' => '客车', 'pic_path' => 'images/expense_type/3.png'],
            ['id' => 4, 'name' => '的士', 'pic_path' => 'images/expense_type/4.png'],
            ['id' => 5, 'name' => '住宿', 'pic_path' => 'images/expense_type/5.png'],
            ['id' => 6, 'name' => '吃饭', 'pic_path' => 'images/expense_type/6.png'],
            ['id' => 7, 'name' => '加油', 'pic_path' => 'images/expense_type/7.png'],
            ['id' => 8, 'name' => '修理', 'pic_path' => 'images/expense_type/8.png'],
            ['id' => 9, 'name' => '办公', 'pic_path' => 'images/expense_type/9.png'],
            ['id' => 11, 'name' => '电话', 'pic_path' => 'images/expense_type/10.png'],
        ];
        DB::table('expense_types')->insert($data);
    }
}
