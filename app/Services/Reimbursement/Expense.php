<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Reimbursement;


use App\Models\Expense_type;

class Expense {
    /**
     * 从缓存中取得消费类型
     * @return array
     */
    public function getExpenseTypes() {
        if (!cache()->has('expenseTypes')) {
            $expenseTypesOrigin = Expense_type::get();
            $expenseTypes = array();
            foreach ($expenseTypesOrigin as $v) {
                $id = $v->id;
                $type = ['id' => $id, 'name' => $v->name, 'pic_path' => $v->pic_path];
                $expenseTypes[$id] = $type;
            }
            cache()->forever('expenseTypes', $expenseTypes);
        }
        return cache()->get('expenseTypes');
    }
    
    
}
