<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Reimbursement;

use App\Models\Department;
use App\Models\ReimDepartment;
use App\Models\Reimbursement;
use App\Models\Payee;
use App\Models\Expense;
use App\Models\Bill;
use App\Services\DingdingApi;
use DB;
use Illuminate\Validation\Rule;

/**
 * Description of Reimbursement
 *
 * @author admin
 */
class ReimbursementList
{

    /**
     * 获取新增、编辑的视图数据
     * @param type $id
     * @return type
     */
    public function getReimbursementInfo($id)
    {
        $info = [];
        $result = $this->getDepartmentData(); //获取当前部门数据
        if (empty($result)) {
            return false;
        }

        if ($id != 0) {//编辑
            $info = Reimbursement::find($id);
        } else {//新增
            $info['reim_department_id'] = $result->reim_department_id; //默认资金归属
        }
        $approver = app('Approver')->getIsApproverUsers($result); //判断当前用户是否有审批人
        $info['approver'] = $approver;
        $info['reim_department'] = ReimDepartment::get(); //资金归属选择
        return $info;
    }

    /**
     * 获取部门数据
     */
    public function getDepartmentData()
    {
        $this_department_id = session('current_user')['department_id']; //当前的部门id
        $result = Department::where('department_id', $this_department_id)->first();
        if (empty($result)) {
            $result = $this->getDepartmentParentsIdMoney();
        }
        return $result;
    }

    /**
     * 得到父类部门的数据
     *
     */
    private function getDepartmentParentsIdMoney()
    {
        $partments_department_id = session('current_user')['department']['parentIds'];
        $result = [];
        if (!empty($partments_department_id)) {
            foreach ($partments_department_id as $k => $v) {
                $data = Department::where('department_id', $v)->first();
                if (!empty($data)) {
                    return $data;
                }
            }
        }
        return $result;
    }

    /**
     * 获取要选择审批人的数据
     */
    public function getApproverUser()
    {
        $data = $this->getDepartmentData();
        return app('Approver')->getApproverUser($data);
    }

    /* ------------------------------编辑获取收款人、审批人、消费明细数据start---------------------------------- */

    /**
     * 编辑获取收款人、审批人、消费明细数据
     * @param type $request
     */
    public function getPayeeApproverExpenseData($request)
    {
        $id = $request->id;
        $data = Reimbursement::with('expenses.type', 'expenses.bills')->find($id);
        return $data;
    }

    /* ------------------------------获取收款人、审批人、消费明细数据end---------------------------------- */

    /* ---------------------------保存、提交送审start------------------------------ */

    /**
     * (保存、提交送审)验证规则
     */
    public function getRules($request)
    {
        $data = $this->getDepartmentData();
        $is_approver = app('Approver')->getIsApproverUsers($data); //（true是，false否）是否有审批人

        if (isset($request->send) && ($request->send == 'send')) {
            // 提交送审验证规则
            $rules = $this->getSendRules($request, $is_approver);
        } else {
            //保存验证规则
            $rules = $this->getSaveRules($request, $is_approver);
        }
        return $rules;
    }

    /**
     * 获取保存验证规则
     * @param type $request
     */
    private function getSaveRules($request, $is_approver)
    {
        $approver_staff_sn_str = $this->getApproverUser()->pluck('staff_sn')->implode(','); //获取当前的审批人员员工编号
        $rules = [
            'description' => 'required|string|max:20',
            'remark' => 'string|max:150',
            'payee_id' => 'exists:payees,id',
            'payee_name' => 'exists:payees,bank_account_name,id,' . $request->payee_id,
            'reim_department_id' => 'exists:reim_departments,id',
            'expense' => 'array',
            'expense.*.date' => 'required|date', //消费明细时间
            'expense.*.type_id' => 'required|exists:expense_types,id',
            'expense.*.send_cost' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/', //金额
            'expense.*.description' => 'required|string', //描述
            'expense.*.bill' => 'array',
        ];
        if ($is_approver) {//当前用户有审批人
            $rules['approver_name'] = 'exists:approvers,realname,staff_sn,' . $request->approver_staff_sn;
            $rules['approver_staff_sn'] = 'in:' . $approver_staff_sn_str;
        }
        if (isset($request->id) && (!empty($request->id))) {//保存 编辑验证id
            $rules['id'] = 'required|exists:reimbursements,id,staff_sn,' . session()->get('current_user')['staff_sn'];
        }
        return $rules;
    }

    /**
     * 获取提交送审验证规则
     * @param type $request
     */
    private function getSendRules($request, $is_approver)
    {
        $approver_staff_sn_str = $this->getApproverUser()->pluck('staff_sn')->implode(','); //获取当前的审批人员员工编号
        $rules = [
            'description' => 'required|string|max:20',
            'remark' => 'string|max:150',
            'payee_id' => 'required|exists:payees,id',
            'payee_name' => 'required|exists:payees,bank_account_name,id,' . $request->payee_id,
            'reim_department_id' => 'required|exists:reim_departments,id',
            'expense' => 'required|array',
            'expense.*.date' => 'required|date', //消费明细时间
            'expense.*.type_id' => 'required|exists:expense_types,id',
            'expense.*.send_cost' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/', //金额
            'expense.*.description' => 'required|string', //描述
            'expense.*.bill' => 'array',
        ];
        if ($is_approver) {//当前用户有审批人
            $rules['approver_name'] = 'required|exists:approvers,realname,staff_sn,' . $request->approver_staff_sn;
            $rules['approver_staff_sn'] = 'required|in:' . $approver_staff_sn_str;
        }
        if (isset($request->id) && (!empty($request->id))) {//提交送审 编辑验证id
            $rules['id'] = 'required|exists:reimbursements,id,staff_sn,' . session()->get('current_user')['staff_sn'];
        }
        return $rules;
    }

    /**
     * 保存、提交送审
     * @param type $request
     */
    public function save_send($request)
    {
        $reimburse = $this->getSaveReimburseData($request); //报销数据
        DB::transaction(function () use ($reimburse, $request) {
            if (isset($reimburse['id'])) {//编辑处理
                $this->updateData($reimburse, $request);
            } else {//新增处理
                $this->insertSaveSend($reimburse, $request);
            }
        });
        return $this->sendMessageToApproverName($request, $reimburse);//发送消息到审批人
    }

    /* ----------------------新增处理start---------------------- */

    private function insertSaveSend($reimburse, $request)
    {
        $id = Reimbursement::insertGetId($reimburse); //报销新增
        if (!empty($request->expense)) {
            $this->insertExpenseData($request->expense, $id, $reimburse['status_id']); //保存消费明细数据处理
        }
    }

    /**
     * 新增 处理保存、提交送审的消费明细数据
     * @param type $expense
     */
    private function insertExpenseData($expense, $id, $status_id)
    {
        foreach ($expense as $k => $v) {
            $this->insertExpenseBills($v, $id, $status_id);
        }
    }

    /* ----------------------新增处理end---------------------- */

    /* --------------------------编辑处理start----------------------------- */

    /**
     * 编辑时 处理（保存、提交送审） and （驳回再次提交处理）
     * @param type $reimburse
     * @param type $request
     */
    private function updateData($reimburse, $request)
    {
        $reimburse_data = Reimbursement::find($reimburse['id']);
        if ($reimburse_data->status_id == -1) {//驳回再次提交处理
            $this->rejected($reimburse, $request, $reimburse_data);
        } else {
            $this->updateSaveSend($reimburse, $request);
        }
    }

    /**
     * 驳回再次提交处理
     * @param type $reimburse
     * @param type $request
     */
    private function rejected($reimburse, $request, $reimburse_data)
    {
        $new_data = array_except($reimburse, ['id']);
        $new_data['created_at'] = date('Y-m-d H:i:s', time());
        $this->insertSaveSend($new_data, $request); //重新插入新数据
        $reimburse_data->is_delete = 1;//删除驳回单
        $reimburse_data->is_homepage = 0;
        $reimburse_data->save();
    }

    /**
     * 编辑时保存、提交送审数据处理
     * @param type $reimburse
     * @param type $request
     */
    private function updateSaveSend($reimburse, $request)
    {
        Reimbursement::where('id', $reimburse['id'])->update($reimburse); //报销单修改
        $this->updateExpense($reimburse, $request); //消费明细修改
    }

    /**
     * 编辑处理消费明细数据
     * @param type $reimburse_id
     * @param type $request
     */
    private function updateExpense($reimburse, $request)
    {
        if (!empty($request->expense)) {//提交上来消费明细不为空
            $this->updateDeleteExpense($reimburse, $request);
            foreach ($request->expense as $k => $v) {
                if (isset($v['id']) && $v['id'] != 'undefined') {
                    $this->updateExpenseData($v, $reimburse['status_id']);
                } else {
                    $this->insertExpenseBills($v, $reimburse['id'], $reimburse['status_id']);
                }
            }
        } else {
            $this->deleteExpenseBills($reimburse['id']); //删除消费明细及发票
        }
    }

    /**
     * 编辑删除多余的明细
     * @param $reimburse
     * @param $request
     */
    private function updateDeleteExpense($reimburse, $request)
    {
        $expense_id = array_filter(array_pluck($request->expense, 'id'));
        $expense = Expense::with('bills')->where('reim_id', $reimburse['id'])->get();
        foreach ($expense as $k => $v) {
            if (!in_array($v->id, $expense_id)) {
                $v->bills()->delete();
                $v->delete();
            }
        }
    }

    /**
     * 编辑修改 消费明细
     * @param type $v
     */
    private function updateExpenseData($v, $status_id)
    {
        $expense_data = array_only($v, ['id', 'description', 'date', 'type_id', 'send_cost']);
        if ($status_id == 3) {//无审批人直接提交到审核
            $expense_data['is_approved'] = 1;
        }
        $expense_data['description'] = $this->filterEmoji($expense_data['description']);
        Expense::where('id', $v['id'])->update($expense_data);
        $this->updateBillData($v); //发票修改处理
    }

    /**
     * 编辑(修改发票)
     * @param type $v
     */
    private function updateBillData($v)
    {
        $bills = Bill::where('expense_id', $v['id'])->get();
        if (empty($v['bill'])) {//提交的发票明细为空
            $bills->each(function ($val) {
                $val->delete();
            });
        } else {//提交数据有发票
            if ($bills->count() > 0) {//数据库有发票
                $this->billsUpdateDispose($v);
            } else {//数据库无发票
                $this->saveExpenseBills($v['bill'], $v['id']);
            }
        }
    }

    /**
     * 发票编辑处理
     * @param type $bills
     * @param type $v
     */
    private function billsUpdateDispose($v)
    {
        Bill::where('expense_id', $v['id'])->get()->each(function ($bill) use (&$v) {
            if (in_array($bill->pic_path, $v['bill'])) {
                $key = array_search($bill->pic_path, $v['bill']);
                array_pull($v['bill'], $key);
            } else {
                $bill->delete();
            }
        });
        $this->saveExpenseBills($v['bill'], $v['id']);
    }

    /**
     * 删除消费明细及发票
     * @param type $reimburse_id
     */
    private function deleteExpenseBills($reimburse_id)
    {
        $expense = Expense::with('bills')->where('reim_id', $reimburse_id)->get();
        foreach ($expense as $k => $v) {
            $v->bills()->delete();
            $v->delete();
        }
    }

    /* --------------------------编辑处理end----------------------------- */

    /**
     * 插入消费明细和发票数据
     * @param type $expense
     * @param type $reim_id
     */
    public function insertExpenseBills($expense, $reim_id, $status_id)
    {
        $data = array_only($expense, ['description', 'date', 'type_id', 'send_cost']);
        $data['reim_id'] = $reim_id;
        $data['staff_sn'] = session()->get('current_user')['staff_sn'];
        if ($status_id == 3) {//无审批人直接提交到审核步骤
            $data['is_approved'] = 1;
        }
        $data['description'] = $this->filterEmoji($data['description']);
        $expense_id = Expense::insertGetId($data); //保存明细
        if (!empty($expense['bill'])) {//保存发票
            $this->saveExpenseBills($expense['bill'], $expense_id);
        }
    }

    /**
     * 发票存入
     * @param type $bills
     * @param type $expense_id
     */
    public function saveExpenseBills($bills, $expense_id)
    {
        foreach ($bills as $k => $v) {
            $bill = new Bill();
            $bill->pic_path = $v;
            $bill->expense_id = $expense_id;
            $bill->save();
        }
    }

    /**
     * 获取保存、提交送审 报销数据
     * @param type $request
     * @return type
     */
    private function getSaveReimburseData($request)
    {
        $payee = Payee::with('province', 'city')->find($request->payee_id); //当前收款人
        $reimburse = $request->except(['_url', 'expense', 'send']);
        $reimburse['description'] = $this->filterEmoji($reimburse['description']);
        $reimburse['remark'] = $this->filterEmoji($reimburse['remark']);
        $reimburse['staff_sn'] = session()->get('current_user')['staff_sn'];
        $reimburse['realname'] = session()->get('current_user')['realname'];
        $reimburse['department_id'] = session()->get('current_user')['department']['id'];
        $reimburse['department_name'] = session()->get('current_user')['department']['full_name'];
        $reimburse['payee_bank_account'] = $payee->bank_account;
        $reimburse['payee_bank_other'] = $payee->bank_other;
        $reimburse['payee_phone'] = $payee->phone;
        $reimburse['payee_province'] = $payee->province ? $payee->province->region_name : '';
        $reimburse['payee_city'] = $payee->city ? $payee->city->region_name : '';
        $reimburse['payee_bank_dot'] = $payee->bank_dot ? $payee->bank_dot : '';
        $reimburse['payee_is_public'] = $payee->is_public;
        $reimburse['status_id'] = 0;
        if (!empty($request->expense)) {
            $total = array_pluck($request->expense, 'send_cost');
            $reimburse['send_cost'] = number_format(array_sum($total), '2', '.', '');
        }
        if (isset($request->send) && ($request->send == 'send')) {//提交送审
            $reimburse['status_id'] = 1;
            $reimburse['send_time'] = date('Y-m-d H:i:s', time());
            $reimburse['reim_sn'] = $this->getReim_sn($request); //订单编号
            $data = $this->getDepartmentData();
            $is_approver = app('Approver')->getIsApproverUsers($data); //（true是，false否）是否有审批人
            if (!$is_approver) {//当前用户没有审批人，直接提交到待审核
                $reimburse['status_id'] = 3;
                $reimburse['approve_time'] = date('Y-m-d H:i:s');
            }
        }

        if (!isset($request->id)) {//新增
            $reimburse['created_at'] = date('Y-m-d H:i:s', time());
        }
        return $reimburse;
    }

    protected function filterEmoji($text)
    {
        return preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $text);
    }

    /**
     * 获取订单编号
     */
    private function getReim_sn($request)
    {
        $department_id = session()->get('current_user')['department']['id'];
        $reim_sn = $this->makeReimSn($department_id); //订单编号
        if (isset($request->id)) {
            $reimburse_data = Reimbursement::find($request->id);
            if (!empty($reimburse_data->reim_sn)) {
                $reim_sn = $reimburse_data->reim_sn;
                if ($reimburse_data->status_id == -1) {//驳回重新提交生成订单编号
                    $reim_sn = $this->remakeRejectedReimSn($reimburse_data->reim_sn);
                }
            }
        }
        return $reim_sn;
    }

    /**
     * 订单编号生成
     * @param type $typeId
     * @return string
     */
    private function makeReimSn($departmentId)
    {
        $today = date('Ymd', time());
        $departmentId = sprintf("%02d", $departmentId);
        $cacheName = 'reimSn_' . $today;
        if (cache()->has($cacheName)) {
            $id = (int)cache()->get($cacheName) + 1;
            $id = sprintf("%04d", $id);
            cache()->put($cacheName, $id, 60 * 24);
        } else {
            $id = sprintf("%04d", 1);
            cache()->add($cacheName, $id, 60 * 24);
        }
        $reimSn = $today . $departmentId . $id;
        return $reimSn;
    }

    /**
     * 驳回后重新生成报销单编号
     * @param type $reimSn
     * @return string
     */
    private function remakeRejectedReimSn($reimSn)
    {
        $position = strrpos($reimSn, 'R');
        if ($position) {
            $remakeId = (int)substr($reimSn, $position + 1);
            $remakeId += 1;
            $reimSn = substr($reimSn, 0, $position + 1) . $remakeId;
        } else {
            $reimSn .= '-R1';
        }
        return $reimSn;
    }

    /**
     * 发送消息（提交送审到审批人）
     * @param $request
     * @param $reimburse
     */
    private function sendMessageToApproverName($request, $reimburse)
    {
        if (isset($request->send) && $request->send == 'send') {
            if (isset($reimburse['approver_staff_sn'])) {//有审批人
                $dingDingId = $this->getUserDingDingId($reimburse['approver_staff_sn']);
                if ($dingDingId == 'dingdingError') {
                    return 'dingdingError';
                }
                $api = new DingdingApi();
//                $dingDingId = '0564652744672687';
                $msgContent = $this->getMsgContent($request, $reimburse);//获取消息内容
                $api->sendOaMessage($msgContent, $dingDingId);//发送oa消息
            }
        }
        return 'success';
    }

    private function getMsgContent($request, $reimburse)
    {
        $msgcontent = [
            'message_url' => route('pending_list'),
            'head' => [
                'bgcolor' => 'FFBBBBBB',
                'text' => '头部标题',
            ],
            'body' => [
                'title' => '描述：' . $reimburse['description'],
                'form' => [
                    [
                        'key' => '姓名:',
                        'value' => $reimburse['realname'],
                    ],
                    [
                        'key' => '部门:',
                        'value' => $reimburse['department_name']
                    ]
                ],
                'rich' => [
                    'num' => $reimburse['send_cost'],
                    'unit' => '元',
                ],
                'content' => '备注：' . $reimburse['remark'],
//                'image'=>'@lADOADmaWMzazQKA',
                'file_count' => count($request->expense),
                'author' => $reimburse['realname'],
            ],
        ];
        return json_encode($msgcontent);
    }

    /**
     * 获取钉钉号
     * @param $reimburse
     */
    public function getUserDingDingId($staff_sn)
    {
        $url = config('oa.get_user');
        $data = [
            'staff_sn' => $staff_sn,
        ];
        $result = app('OAService')->getDataFromApi($url, $data);
        $dingding = $result['message'][0]['dingding'];
        if (!$dingding) {
            return 'dingdingError';//无法获取审批人钉钉号
        }
        return $dingding;

    }
    /* ---------------------------保存、提交送审end------------------------------ */
}
