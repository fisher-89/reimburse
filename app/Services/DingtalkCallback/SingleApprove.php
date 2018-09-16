<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12/012
 * Time: 17:11
 */

namespace App\Services\DingtalkCallback;


use App\Models\Reimbursement;
use App\Models\VicePresident;

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
            switch ($request->result) {
                case 'agree':
                    $this->singleAgree($reimbursement);
                    break;
                case 'refuse':
                    $this->singleRefuse($request, $reimbursement);
                    break;
            }
        } else if ($request->type == 'finish' && $request->EventType == 'bpms_instance_change') {
            switch ($request->result) {
                case 'agree':
                    $reimbursement->status_id = 6;
                    if (empty($reimbursement->finance_approved_sn)) {
                        $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
                    } else {
                        $reimbursement->finance_approved_at = date('Y-m-d H:i:s');
                    }
                    $reimbursement->save();
                    break;
                case 'refuse':
                    $this->singleRefuse($request, $reimbursement);
                    break;
            }
        }
        return 1;
    }

    protected function singleAgree($reimbursement)
    {
        if ($reimbursement->manager_sn && empty($reimbursement->finance_approved_sn)) {
            //副总审批
            $reimbursement->status_id = 6;
            $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
        } else if (empty($reimbursement->manager_sn) && $reimbursement->finance_approved_sn) {
            //郭娟、喜哥审批
            $reimbursement->status_id = 6;
            $reimbursement->finance_approved_at = date('Y-m-d H:i:s');
        } else if ($reimbursement->manager_sn && $reimbursement->finance_approved_sn) {
            //副总和郭娟审批
            if (empty($reimbursement->manager_approved_at)) {
                $reimbursement->status_id = 5;
                $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
            } else {
                $reimbursement->status_id = 6;
                $reimbursement->finance_approved_at = date('Y-m-d H:i:s');
            }
        }

        $reimbursement->save();
    }

    protected function singleRefuse($request, $reimbursement)
    {
        if (empty($reimbursement->manager_approved_at) && $reimbursement->manager_sn) {
            $reimbursement->second_rejecter_staff_sn = $reimbursement->manager_sn;
            $reimbursement->second_rejecter_name = $reimbursement->manager_name;
        } else {
            $reimbursement->second_rejecter_staff_sn = $reimbursement->finance_approved_sn;
            $reimbursement->second_rejecter_name = $reimbursement->finance_approved_name;
        }
        $reimbursement->status_id = 3;
        $reimbursement->second_rejected_at = date('Y-m-d H:i:s');
        $reimbursement->second_reject_remarks = $request->remark;
        $reimbursement->process_instance_id = '';
        $reimbursement->accountant_staff_sn = '';
        $reimbursement->accountant_name = '';
        $reimbursement->audit_time = null;
        $reimbursement->manager_sn = '';
        $reimbursement->manager_name = '';
        $reimbursement->manager_approved_at = null;
        $reimbursement->finance_approved_sn = '';
        $reimbursement->finance_approved_name = '';
        $reimbursement->expenses
            ->where('is_audited', 1)
            ->each(function ($expense) {
                $expense->is_audited = 0;
                $expense->save();
            });
        $reimbursement->save();
    }
}