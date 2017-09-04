<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\Approver as ApproverModel;
use App\Models\Reimbursement;
use App\Services\Reimbursement\ReimbursementList;
use Cache;
use DB;
use App\Models\Expense;
use App\Services\DingdingApi;

/**
 * Description of Approver
 *
 * @author admin
 */
class Approver {

    /**
     * 所有审批人信息存入缓存（包含1级审批、2级审批、3级审批）
     * @param Request $request
     */
    public function approverUserToCache() {
        $approver = ApproverModel::get();
        if (count($approver) > 0) {
            $data = [];
            $approver1 = [];
            $approver2 = [];
            $approver3 = [];
            foreach ($approver as $k => $v) {
                /* ---得到全部一级、二级、三级审批人。start--- */

                if ($v->priority == 1) {
                    $approver1[] = $v->staff_sn;
                } elseif ($v->priority == 2) {
                    $approver2[] = $v->staff_sn;
                } elseif ($v->priority == 3) {
                    $approver4[] = $v->staff_sn;
                }
                /* ---得到全部一级、二级、三级审批人。end--- */
            }
            $data['approver1'] = $approver1;
            $data['approver2'] = $approver2;
            $data['approver3'] = $approver3;
            $data['department_approver_data'] = $approver->toArray();
            Cache::forever('approver', $data);
        }
    }

    /**
     * 判断当前用户是否有审批人
     * @param type $request
     */
    public function getIsApproverUsers($request) {
        $staff_sn = session('current_user')['staff_sn'];
        $approver3 = array_pluck($request->approver3, ['staff_sn']);
        $approver2 = array_pluck($request->approver2, ['staff_sn']);
        $approver1 = array_pluck($request->approver1, ['staff_sn']);
        $show = true; //有审批人
        if (in_array($staff_sn, $approver3)) {
            $show = false;
        } elseif (empty($approver3) && in_array($staff_sn, $approver2)) {
            $show = false;
        } elseif (empty($approver3) && empty($approver2) && in_array($staff_sn, $approver1)) {
            $show = false;
        }
        return $show;
    }

    /**
     * 获取当前用户的所有审批人
     * @param type $request
     */
    public function getApproverUser($request) {
        $staff_sn = session('current_user')['staff_sn'];
        $approver = [];
        if (in_array($staff_sn, array_pluck($request->approver2, ['staff_sn']))) {
            $approver = $request->approver3;
        } elseif (in_array($staff_sn, array_pluck($request->approver1, ['staff_sn']))) {
            $approver = $request->approver2;
        } else {
            $approver = $request->approver1;
        }
        return $approver;
    }

    /* -----------------待审批数据处理start--------------------- */

    /**
     * 获取待审批列表数据
     */
    function getPendingList() {
        $staff_sn = session()->get('current_user')['staff_sn'];
        $list = Reimbursement::where(['status_id' => 1, 'approver_staff_sn' => $staff_sn, 'is_delete' => 0, 'is_homepage' => 1])->get();
        return view('pending.pending_list', ['list' => $list]);
    }

    /**
     * 获取待审批报销单详情
     * @param type $id
     */
    public function getPendingDetails($id) {
        $info = Reimbursement::with('reim_department', 'expenses.type', 'expenses.bills')
                ->where('approver_staff_sn', session()->get('current_user')['staff_sn'])
                ->find($id);
        return $info;
    }

    /**
     * 处理驳回（待审批单）
     * @param type $request
     */
    public function pendingReject($request) {
        $id = $request->id;
        $reject_remarks = $request->reject_remarks;
        $staff_sn = session()->get('current_user')['staff_sn'];
        $data = [
            'status_id' => -1,
            'reject_staff_sn' => $staff_sn,
            'reject_name' => session()->get('current_user')['realname'],
            'reject_remarks' => $reject_remarks,
            'reject_time' => date('Y-m-d H:i:s',time()),
        ];
        Reimbursement::where(['id' => $id, 'approver_staff_sn' => $staff_sn])->update($data);
        return $this->sendRejectMessage($id);//发送驳回消息
    }

    private function sendRejectMessage($id){
        $data = Reimbursement::find($id);
        $reimList = new ReimbursementList();
        $dingding  =$reimList->getUserDingDingId($data->staff_sn);
        if($dingding == 'dingdingError'){
            return $dingding;
        }
//            $dingding = '0564652744672687';
        $api = new DingdingApi();
        $content = $data->approver_name."-已把你的报销单驳回了。描述-".$data->description;
        $api->sendTextMessages($dingding,$content);//发送消息到审批人
        return 'success';
    }


    /**
     * 同意（待审批单处理）
     * @param type $request
     */
    public function pendingAgree($request) {
        DB::transaction(function() use($request) {
            $this->pendingExpense($request); //处理消费明细
            $this->pendingReimburse($request); //处理报销数据
        });
        $this->sendAgreeMessage($request);//发送审批通过消息
        return 'success';
    }

    /**
     * 同意 处理消费明细
     * @param type $request
     */
    private function pendingExpense($request) {
        $agree = $request->agree; //明细id（数组）
        Expense::whereIn('id', $agree)->update(['is_approved' => '1']);
    }

    /**
     * 同意 处理报销单数据
     * @param type $request
     */
    private function pendingReimburse($request) {
        $id = $request->id;
        $agree = $request->agree; //明细id（数组）
        $approved_cost = Expense::whereIn('id', $agree)->sum('send_cost'); //审批金额
        $data['status_id'] = 3;
        $data['approved_cost'] = $approved_cost;
        $data['approve_time'] = date('Y-m-d H:i:s',time());
        Reimbursement::where(['id' => $id])->update($data);
    }

    private function sendAgreeMessage($request){
        $data = Reimbursement::find($request->id);
        $reimList = new ReimbursementList();
        $dingding  =$reimList->getUserDingDingId($data->staff_sn);
        if($dingding == 'dingdingError'){
            return $dingding;
        }
//        $dingding = '0564652744672687';
        $msgContent = $this->getMsgContent($request,$data);//获取消息数据
        app('DingdingApi')->sendOaMessage($msgContent, $dingding);//发送oa消息
    }

    private function getMsgContent($request,$data){
        $msgcontent = [
            'message_url' => route('mine'),
            'head' => [
                'bgcolor' => 'FFBBBBBB',
                'text' => '头部标题',
            ],
            'body' => [
                'title' => '你报销单已审批通过了！请等待审核。',
                'form' => [
                    [
                        'key' => '审批人:',
                        'value' => $data['approver_name'],
                    ],
                    [
                        'key' => '审批金额:',
                        'value' => $data['approved_cost']
                    ],
                    [
                        'key'=>'审批时间',
                        'value'=>$data['approve_time']
                    ]
                ],
                'rich' => [
                    'num' => $data['approved_cost'],
                    'unit' => '元',
                ],
                'content' => '描述：' . $data['description'],
//                'image'=>'@lADOADmaWMzazQKA',
                'file_count' => count($request->agree),
                'author' => $data['approver_name'],
            ],
        ];
        return json_encode($msgcontent);
    }
    /* -----------------待审批数据处理end--------------------- */

    /* -------------------------已审批报销单start------------------- */

    /**
     * 获取已审批报销单列表数据
     */
    public function getHaveApprovalList() {
        $staff_sn = session()->get('current_user')['staff_sn'];
        return Reimbursement::with('status')->where('approve_time', '!=', '')->where('approver_staff_sn', $staff_sn)->orderBy('approve_time','desc')->get();
    }

    /* -------------------------已审批报销单end------------------- */

    /* -------------------------已驳回报销单start------------------- */

    /**
     * 获取已驳回报销单列表数据
     */
    public function getHasRejectedList() {
        $staff_sn = session()->get('current_user')['staff_sn'];
        return Reimbursement::with('status')->where(['approve_time' => null,'reject_staff_sn' => $staff_sn])->orderBy('reject_time','desc')->get();
    }

    /* -------------------------已驳回报销单end------------------- */
}
