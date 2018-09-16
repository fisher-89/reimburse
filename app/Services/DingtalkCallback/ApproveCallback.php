<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12/012
 * Time: 10:47
 */

namespace App\Services\DingtalkCallback;


class ApproveCallback
{
    use BatchApprove, SingleApprove;


    /**
     * 批量审批回调
     * @param $request
     */
    public function batchApproveCallback($request){
        return $this->batch($request);
    }

    /**
     * 单条审批
     * @param $request
     */
    public function singleApproveCallback($request){
        return $this->single($request);
    }
}