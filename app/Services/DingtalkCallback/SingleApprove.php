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
            switch ($request->result) {
                case 'agree':
                    return $this->singleAgree($reimbursement);
                    break;
                case 'refuse':
                    return $this->singleRefuse($request, $reimbursement);
                    break;
            }

            return 0;
        }
    }

    protected function singleAgree($reimbursement)
    {
        $approverSn = empty($reimbursement->approver_staff_sn) ? $reimbursement->staff_sn : $reimbursement->approver_staff_sn;//审批人员工编号
        $managerSn = $reimbursement->reim_department->manager_sn;//资金归属管理人员工编号
        $managerName = $reimbursement->reim_department->manager_name;//资金归属管理人员工名字

        if ($reimbursement->audited_cost > 5000) {
            if ($reimbursement->status_id == 5) {
                $reimbursement->status_id = 6;
                $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
                $reimbursement->manager_sn = $this->financeOfficerSn;
                $reimbursement->manager_name = $this->financeOfficerName;
            } else {
                if ((int)$approverSn == (int)$managerSn) {
                    $reimbursement->status_id = 6;
                    $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
                    $reimbursement->manager_sn = $this->financeOfficerSn;
                    $reimbursement->manager_name = $this->financeOfficerName;
                } else {
                    $reimbursement->status_id = 5;
                    $reimbursement->manager_sn = $managerSn;
                    $reimbursement->manager_name = $managerName;
                }
            }

        } else {
            $reimbursement->status_id = 6;
            $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
            $reimbursement->manager_sn = $managerSn;
            $reimbursement->manager_name = $managerName;
        }
        $reimbursement->save();
        return 1;
    }

    protected function singleRefuse($request, $reimbursement)
    {
        $approverSn = empty($reimbursement->approver_staff_sn) ? $reimbursement->staff_sn : $reimbursement->approver_staff_sn;//审批人员工编号
        $managerSn = $reimbursement->reim_department->manager_sn;//资金归属管理人员工编号
        $managerName = $reimbursement->reim_department->manager_name;//资金归属管理人员工名字

        if ($reimbursement->audited_cost > 5000) {
            if ($reimbursement->status_id == 5) {
                $reimbursement->second_rejecter_staff_sn = $this->financeOfficerSn;
                $reimbursement->second_rejecter_name = $this->financeOfficerName;
            } else {
                if ((int)$approverSn == (int)$managerSn) {
                    $reimbursement->second_rejecter_staff_sn = $this->financeOfficerSn;
                    $reimbursement->second_rejecter_name = $this->financeOfficerName;
                } else {
                    $reimbursement->second_rejecter_staff_sn = $managerSn;
                    $reimbursement->second_rejecter_name = $managerName;
                }
            }
        } else {
            $reimbursement->second_rejecter_staff_sn = $managerSn;
            $reimbursement->second_rejecter_name = $managerName;
        }
        $reimbursement->status_id = 3;
        $reimbursement->second_rejected_at = date('Y-m-d H:i:s');
        $reimbursement->second_rejecte_remarks = $request->remark;
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
        return 1;
    }
}