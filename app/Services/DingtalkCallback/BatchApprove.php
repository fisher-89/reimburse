<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12/012
 * Time: 10:58
 */

namespace App\Services\DingtalkCallback;


use App\Models\Reimbursement;

trait BatchApprove
{
    /**
     * 批量审批回调处理
     * @param $request
     * @return int
     */
    protected function batch($request)
    {
        $processInstanceId = $request->processInstanceId;
        $reimbursement = Reimbursement::where('process_instance_id', $processInstanceId)->get();
        if (!$reimbursement) {
            return 0;
        }
        $rejectRemark = $request->remark;
        if ($request->type == 'finish' && $request->EventType == 'bpms_task_change') {
            //审批任务结束
            switch ($request->result) {
                case 'agree'://同意
                    $this->batchApproveBpmsTaskChangeAgree($reimbursement);
                    break;
                case 'refuse';//拒绝
                    $this->batchApproveBpmsTaskChangeRefuse($reimbursement,$rejectRemark);
                    break;
            }
        }else if ($request->type == 'finish' && $request->EventType == 'bpms_instance_change') {
            //审批实例结束|终止
            switch ($request->result) {
                case 'agree':
                    $reimbursement->each(function ($reim) {
                        if(empty($reim->finance_approved_sn)){
                            $reim->manager_approved_at = date('Y-m-d H:i:s');
                        }else{
                            $reim->finance_approved_at = date('Y-m-d H:i:s');
                        }
                        $reim->status_id = 6;
                        $reim->save();
                    });
                    break;
                case 'refuse':
                    $this->batchApproveBpmsTaskChangeRefuse($reimbursement,$rejectRemark);
                    break;
            }
        }
        return 1;
    }

    protected function batchApproveBpmsTaskChangeAgree($reimbursement)
    {
        if(empty($reimbursement[0]->manager_approved_at) && $reimbursement[0]->finance_approved_sn){
            $reimbursement->each(function ($reim) {
                $reim->manager_approved_at = date('Y-m-d H:i:s');
                $reim->status_id = 5;
                $reim->save();
            });
        }
        else if(empty($reimbursement[0]->manager_approved_at) && empty($reimbursement[0]->finance_approved_sn)){
            $reimbursement->each(function ($reim) {
                $reim->manager_approved_at = date('Y-m-d H:i:s');
                $reim->status_id = 6;
                $reim->save();
            });
        }else{
            $reimbursement->each(function ($reim) {
                $reim->finance_approved_at = date('Y-m-d H:i:s');
                $reim->status_id = 6;
                $reim->save();
            });
        }
    }

    protected function batchApproveBpmsTaskChangeRefuse($reimbursement,$rejectRemark)
    {
        $reimbursement->each(function ($reim)use($rejectRemark){
            $reim->second_rejecter_staff_sn = $reim->manager_sn;
            $reim->second_rejecter_name = $reim->manager_name;
            if($reim->manager_approved_at){
                $reim->second_rejecter_staff_sn = $reim->finance_approved_sn;
                $reim->second_rejecter_name = $reim->finance_approved_name;
            }
            $reim->status_id = 4;
            $reim->process_instance_id = '';
            $reim->second_rejected_at = date('Y-m-d H:i:s');
            $reim->second_reject_remarks = $rejectRemark;
            $reim->manager_sn = '';
            $reim->manager_name = '';
            $reim->manager_approved_at = null;
            $reim->finance_approved_sn = '';
            $reim->finance_approved_name = '';
            $reim->save();
        });
    }

}