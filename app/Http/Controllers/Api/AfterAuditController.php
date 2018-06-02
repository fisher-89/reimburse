<?php

namespace App\Http\Controllers\Api;

use App\Models\Reimbursement;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AfterAuditController extends Controller
{
    protected $financeOfficerSn = 110085;
    protected $financeOfficerName = '郭娟';

    public function managerProcess(Request $request)
    {
        $processInstanceId = $request->processInstanceId;
        $reimbursement = Reimbursement::where('process_instance_id', $processInstanceId)
            ->where('status_id', 4)
            ->first();
        if (empty($reimbursement)) {
            return 0;
        }
        if ($request->EventType == 'bpms_instance_change' && $request->type == 'finish') {
            switch ($request->result) {
                case 'agree':
                    $reimbursement->manager_approved_at = date('Y-m-d H:i:s');
                    if ($reimbursement->manager_sn != $this->financeOfficerSn && $reimbursement->audited_cost > 5000) {
                        $reimbursement->status_id = 5;
                        $response = $this->sendToFinanceOfficer($reimbursement);
                        if ($response['status'] != 1) return 0;
                        $reimbursement->process_instance_id = $response['message'];
                    } else {
                        $reimbursement->status_id = 6;
                    }
                    break;
                case 'refuse';
                    $reimbursement->status_id = -1;
                    $reimbursement->reject_staff_sn = $reimbursement->manager_sn;
                    $reimbursement->reject_name = $reimbursement->manager_name;
                    $reimbursement->reject_time = date('Y-m-d H:i:s');
                    $reimbursement->process_instance_id = '';
                    break;
            }
            return $reimbursement->save() ? 1 : 0;
        }
        return 0;
    }

    public function financeOfficerProcess(Request $request)
    {
        $processInstanceId = $request->processInstanceId;
        $reimbursement = Reimbursement::where('process_instance_id', $processInstanceId)
            ->where('status_id', 5)
            ->first();
        if (empty($reimbursement)) {
            return 0;
        }
        if ($request->EventType == 'bpms_instance_change' && $request->type == 'finish') {
            switch ($request->result) {
                case 'agree':
                    $reimbursement->status_id = 6;
                    $reimbursement->process_instance_id = '';
                    break;
                case 'refuse';
                    $reimbursement->status_id = -1;
                    $reimbursement->reject_staff_sn = $this->financeOfficerSn;
                    $reimbursement->reject_name = $this->financeOfficerName;
                    $reimbursement->reject_time = date('Y-m-d H:i:s');
                    $reimbursement->process_instance_id = '';
                    break;
            }
            return $reimbursement->save() ? 1 : 0;
        }
        return 0;
    }

    protected function sendToFinanceOfficer($reimbursement)
    {
        $processCode = 'PROC-GLYJ5N2V-E11VUX0YRK67A1WOOODU2-G8JBUYGJ-1';
        $approvers = [$this->financeOfficerSn];
        $formData = $this->makeFormData($reimbursement);
        $params = [
            'process_code' => $processCode,
            'approvers' => $approvers,
            'form_data' => $formData,
            'callback_url' => url('/api/callback/finance-officer'),
            'initiator_sn' => $reimbursement->staff_sn,
        ];
        return app('OAService')->withoutPassport()->getDataFromApi('dingtalk/start_approval', $params);
    }

    protected function makeFormData($reimbursement)
    {
        $formData = [
            '报销单编号' => $reimbursement->reim_sn,
            '内容' => $reimbursement->description,
            '申请人' => $reimbursement->realname,
            '总金额' => $reimbursement->audited_cost ?: '',
            '直属领导' => $reimbursement->approver_name,
            '财务审核' => $reimbursement->accountant_name,
            '资金归属' => $reimbursement->reim_department->name,
        ];
        $formData['消费明细'] = $reimbursement->expenses->where('is_audited', 1)->map(function ($expense) {
            return [
                '金额' => $expense->audited_cost,
                '日期' => $expense->date,
                '描述' => $expense->description,
                '发票' => $expense->bills->pluck('pic_path')->map(function ($picPath) {
                    return url($picPath);
                })->all(),
            ];
        })->all();
        return $formData;
    }
}
