<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12/012
 * Time: 17:11
 */

namespace App\Services\DingtalkCallback;


use App\Models\Reimbursement;

trait SingleApprove
{
    /**
     * 单条审批
     * @param $request
     */
    protected function single($request)
    {
        $processInstanceId = $request->processInstanceId;
        $reimbursement = Reimbursement::where('process_instance_id', $processInstanceId)
            ->whereIn('status_id', [4, 5])
            ->first();
        if (empty($reimbursement)) {
            return 0;
        }

        if ($request->type == 'finish' && $request->EventType == 'bpms_task_change') {
            if($reimbursement->status_id == 4) {
                switch ($request->result) {
                    case 'agree':
                        $reimbursement->status_id = 5;
                        $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
                        $reimbursement->save();
                        break;
                    case 'refuse':
                        $this->singleRefuse($request,$reimbursement);
                        break;
                }
            }
        }else if($request->type == 'finish' && $request->EventType == 'bpms_instance_change'){
            switch ($request->result) {
                case 'agree':
                    $reimbursement->status_id = 6;
                    $reimbursement->save();
                    break;
                case 'refuse':
                    $this->singleRefuse($request,$reimbursement);
                    break;
            }
        }
        return 1;
    }


    protected function singleRefuse($request,$reimbursement)
    {
        $reimbursement->second_rejecter_staff_sn = $reimbursement->manager_sn;
        $reimbursement->second_rejecter_name = $reimbursement->manager_name;
        if($request->EventType = 'bpms_instance_change'){
            $reimbursement->second_rejecter_staff_sn = $this->financeOfficerSn;
            $reimbursement->second_rejecter_name = $this->financeOfficerName;
        }
        $reimbursement->status_id = 3;
        $reimbursement->second_rejected_at = date('Y-m-d H:i:s');
        $reimbursement->second_reject_remarks = $request->remark;
        $reimbursement->process_instance_id = '';
        $reimbursement->accountant_staff_sn = '';
        $reimbursement->accountant_name = '';
        $reimbursement->audit_time = date('Y-m-d H:i:s');
        $reimbursement->expenses
            ->where('is_approved', 1)
            ->whereIn('id', array_pluck($reimbursement->expenses, 'id'))
            ->each(function ($expense) {
                $expense->is_audited = 0;
                $expense->save();
            });
        $reimbursement->save();
    }
}