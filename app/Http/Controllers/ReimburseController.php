<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Services\Reimbursement\ReimbursementList;
use App\Services\Reimbursement\MyReimburse;

class ReimburseController extends Controller {

    public $reimbursementList;

    public function __construct(ReimbursementList $reimbursementList) {
        $this->reimbursementList = $reimbursementList;
    }

    /**
     * 新建、编辑报销单视图
     */
    public function showCreatePage(Request $request) {
        $id = $request->id ? $request->id : 0;
        $info = $this->reimbursementList->getReimbursementInfo($id);
        if ($info) {
            return view('reimbursement/add_or_edit', ['info' => $info]);
        } else {
            return redirect()->route('home')->withErrors("当前审批部门不存在！请联系管理员!");
        }
    }

    /**
     * 添加审批人
     * @param Request $request
     */
    public function addApproverUser() {
        $approver = $this->reimbursementList->getApproverUser();
        if (count($approver) < 1) {
            return redirect()->back()->withErrors('该部门没有配置审批人！请联系管理员！')->withInput();
        }
        return view('reimbursement.choose_approver_user', ['approver' => $approver]);
    }

    /**
     * 编辑获取收款人、审批人、消费明细数据
     * @param Request $request
     */
    public function get_reimburse_payee_approver_expense(Request $request) {
        return $this->reimbursementList->getPayeeApproverExpenseData($request);
    }

    /**
     * 保存和提交送审处理
     */
    public function addReimbursement(Request $request) {
        if (!empty($request->expense)) {//处理消费明细转为数组
            $request->offsetSet('expense', json_decode($request->get('expense'), true));
        }
        $rules = $this->reimbursementList->getRules($request); //验证规则
        $this->validate($request, $rules, [], trans('fields.reimbursement'));
        return $this->reimbursementList->save_send($request); //保存、提交送审处理
    }

    /**
     * 我的报销单
     */
    public function showMyReimbursements(MyReimburse $myReim) {
        return $myReim->getListData();
    }

    /**
     * 查看报销单
     */
    public function checkReimbursement(Request $request, MyReimburse $myReim) {
        $info = $myReim->checkReimburse($request->id);
        return view('check_reimbursement')->with(['info' => $info]);
    }

    /**
     * 我的报销撤回
     * @param Request $request
     */
    public function withdraw(Request $request, MyReimburse $myReim) {
        if ($request->method() == "POST") {
            return $myReim->withdrawReimburse($request->id);
        }
    }

    /**
     * 删除未提交的报销单
     * @param Request $request
     */
    public function deleteReim(Request $request,MyReimburse $myReim){
        $id = $request->id;
        return $myReim->deleteReim($id);
    }/**
     * 删除驳回的报销单
     * @param Request $request
     */
    public function deleteReject(Request $request,MyReimburse $myReim){
        $id = $request->id;
        return $myReim->deleteReject($id);
    }

    /* --------------------------------------------------------- */

}
