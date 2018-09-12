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
        $statusId = $reimbursement[0]->status_id;

        if ($request->type == 'finish' && $request->EventType == 'bpms_task_change') {
            if ($statusId == 4) {
                //审批任务结束
                return $this->approveBpmsTaskChangeFinish($request, $reimbursement);

            } elseif ($statusId == 5) {
                //审批实例结束|终止
                return $this->approveBpmsInstanceChangeFinish($request, $reimbursement);
            }
        }
        return 0;
    }

    /**
     * 审批任务结束
     * @param $request
     * @param $reimbursement
     */
    protected function approveBpmsTaskChangeFinish($request, $reimbursement)
    {
        switch ($request->result) {
            case 'agree'://同意
                $reimbursement->each(function ($reim) {
                    $reim->status_id = 5;
                    $reim->save();
                });
                break;
            case 'refuse';//拒绝
                $reimbursement->each(function ($reim)use($request){
                    $reim->process_instance_id = '';
                    $reim->second_rejecter_staff_sn = $reim->manager_sn;
                    $reim->second_rejecter_name = $reim->manager_name;
                    $reim->second_rejected_at = date('Y-m-d H:i:s');
                    $reim->second_reject_remarks = $request->remark;
                    $reim->manager_sn = '';
                    $reim->manager_name = '';
                    $reim->save();
                });
                break;
        }
        return 1;
    }

    /**
     * 审批实例结束
     * @param $request
     * @param $reimbursement
     */
    protected function approveBpmsInstanceChangeFinish($request, $reimbursement)
    {
        switch ($request->result) {
            case 'agree'://同意
                $reimbursement->each(function ($reim) {
                    $reim->manager_approved_at = date('Y-m-d H:i:s');
                    $reim->status_id = 6;
                    $reim->manager_sn = $this->financeOfficerSn;
                    $reim->manager_name = $this->financeOfficerName;
                    $reim->save();
                });
                break;
            case 'refuse';//拒绝
                $reimbursement->each(function ($reim)use($request) {
                    $reim->process_instance_id = '';
                    $reim->second_rejecter_staff_sn = $this->financeOfficerSn;
                    $reim->second_rejecter_name = $this->financeOfficerName;
                    $reim->second_rejected_at = date('Y-m-d H:i:s');
                    $reim->second_reject_remarks = $request->remark;
                    $reim->manager_sn = '';
                    $reim->manager_name = '';
                    $reim->save();
                });
                break;
        }
        return 1;
    }
}