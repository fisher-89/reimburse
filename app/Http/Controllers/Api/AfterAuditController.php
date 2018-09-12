<?php

namespace App\Http\Controllers\Api;

use App\Models\Reimbursement;
use App\Services\DingtalkCallback\ApproveCallback;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AfterAuditController extends Controller
{

    public $approveCallback;

    public function __construct(ApproveCallback $approveCallback)
    {
        $this->approveCallback = $approveCallback;
    }

    /**
     * 单条审批回调
     * @param Request $request
     * @return int
     */
    public function managerProcess(Request $request)
    {
        return $this->approveCallback->singleApproveCallback($request);
    }


    /**
     * 品牌副总批量审批回调
     * @param Request $request
     */
    public function batchApproveProcess(Request $request)
    {
        return $this->approveCallback->batchApproveCallback($request);
    }

}
