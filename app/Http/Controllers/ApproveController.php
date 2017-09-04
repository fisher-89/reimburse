<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApproveController extends Controller {

    /*------------------------------待审批报销单start-----------------------------*/
    /**
     * 待审批报销单列表
     */
    public function showPendingList() {
        return app('Approver')->getPendingList();
    }

    /**
     * 待审批报销单详情
     */
    public function showPendingDetail(Request $request) {
        $info = app('Approver')->getPendingDetails($request->id);
        return view('pending.pending_detail', ['info' => $info]);
    }

    /**
     * 驳回 （待审批单）
     * @param Request $request
     */
    public function pendingReject(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:reimbursements,id,approver_staff_sn,' . session()->get('current_user')['staff_sn'],
            'reject_remarks' => 'required|string',
                ], [], trans('fields.pending')
        );
        return app('Approver')->pendingReject($request);
    }

    /**
     * 同意（待审批单处理）
     * @param Request $request
     */
    public function pendingAgree(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:reimbursements,id,approver_staff_sn,' . session()->get('current_user')['staff_sn'],
            'agree'=>'required|array'
                ], [], trans('fields.pending')
        );
        return app('Approver')->pendingAgree($request);
    }
    
    /*------------------------------待审批报销单end-----------------------------*/
    
     /*------------------------------已审批报销单start-----------------------------*/
    
    /**
     * 已审批报销单列表数据
     * @return type
     */
     public function haveApprovalList(){
         $data = app('Approver')->getHaveApprovalList();
         return view('have_approval.have_approval_list')->with(['data'=>$data]);
     }
     /*------------------------------已审批报销单end-----------------------------*/

     
     /*---------------------------已驳回报销单start----------------------*/
     /**
      * 已驳回报销单数据列表
      */
     public function hasRejectedList(){
         $data = app('Approver')->getHasRejectedList();
         return view('reject.rejected_list',['data'=>$data]);
     }
     
     /*---------------------------已驳回报销单end--------------------------*/
    /* ---------------------------------------------------------------------------- */

}
