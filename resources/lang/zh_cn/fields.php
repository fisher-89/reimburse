<?php

return [
    //收款人
    'payee' => [
        'bank_account_name' => '户名',
        'phone' => '收款人手机',
        'bank_account' => '银行卡号',
        'bank_other' => '银行类型',
        'bank_dot' => '开户网点',
        'province_of_account' => '开户行所在省',
        'city_of_account' => '开户行所在市',
    ],
    //报销单
    'reimbursement' => [
        'description' => '描述',
        'remark' => '备注',
        'reim_department_id' => '资金归属',
        'payee_name' => '收款人',
        'payee_id' => '收款人id',
        'approver_staff_sn' => '审批人编号',
        'approver_name' => '审批人',
        'expense' => '消费明细',
        'expense.*.date' => '消费明细时间',
        'expense.*.type_id' => '消费明细类型id',
        'expense.*.send_cost' => '消费明细金额',
        'expense.*.description' => '消费明细描述',
        'expense.*.bill' => '消费明细发票',
    ],
    //待审批单（驳回、同意）
    'pending' => [
        'id' => '当前id',
        'reject_remarks' => '驳回原因',
        'agree' => '消费明细id',
    ],
];
