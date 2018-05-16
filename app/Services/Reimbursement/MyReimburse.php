<?php

/*
 * 我的报销单
 */

namespace App\Services\Reimbursement;

use App\Models\Reimbursement;
use App\Services\DingdingApi;
use App\Services\Reimbursement\ReimbursementList;

/**
 * Description of MyReimburse
 *
 * @author admin
 */
class MyReimburse
{

    /**
     * 我的报销单获取列表数据
     * @return type
     */
    public function getListData()
    {
        $data = [
            'notSubmit' => $this->getNotSubmit(),
            'hasSubmit' => $this->getHasSubmit(),
            'complete'=>$this->getComplete(),
            'hasReject' => $this->getHasReject(),
        ];
        return view('reimbursement/my_reimbursement_list_data', ['data' => $data]);
    }

    /**
     * 查看报销单
     * @param type $id
     */
    public function checkReimburse($id)
    {
        $info = Reimbursement::with('reim_department', 'status')->find($id);
        return $info;
    }

    /**
     * 处理撤回
     * @param type $id
     */
    public function withdrawReimburse($id)
    {
        $reimburse = Reimbursement::where('staff_sn', session()->get('current_user')['staff_sn'])->find($id);
        if (!empty($reimburse) && $reimburse->status_id == 1) {
            $reimburse->status_id = 0;
            $reimburse->save();
            $reimList = new ReimbursementList();
            $dingding  =$reimList->getUserDingDingId($reimburse['approver_staff_sn']);
            if($dingding == 'dingdingError'){
                return $dingding;
            }
//            $dingding = '0564652744672687';
            $api = new DingdingApi();
            $content = $reimburse->realname."-已把报销单撤回了。描述-".$reimburse->description;
            $api->sendTextMessages($dingding,$content);//发送消息到审批人
            return 'success';
        }
        return 'error';
    }

    /**
     * 删除未提交的报销单
     * @param $id
     */
    public function deleteReim($id)
    {
        $reim = Reimbursement::where(['staff_sn' => session()->get('current_user')['staff_sn'], 'status_id' => 0])->find($id);
        if (count($reim) < 1) {
            return 'error';
        }
        $this->deleteReimAll($reim);
        return 'success';
    }

    private function deleteReimAll($reim)
    {
        foreach ($reim->expenses as $key => $val) {
            $val->bills()->delete();
            $val->delete();
        }
        $reim->delete();
    }


    /**
     * 删除已驳回单
     * @param $id
     */
    public function deleteReject($id){
        $reim = Reimbursement::where(['staff_sn'=>session()->get('current_user')['staff_sn'],'status_id'=>-1])->find($id);
        if(count($reim)<1){
            return 'error';
        }
        $reim->is_homepage = 0;
        $reim->is_delete = 1;
        $reim->save();
        return 'success';
    }

    /**
     * 获取未提交数据
     */
    private function getNotSubmit()
    {
        $staff_sn = session()->get('current_user')['staff_sn'];
        return Reimbursement::where(['staff_sn' => $staff_sn, 'status_id' => 0, 'is_homepage' => 1, 'is_delete' => 0])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 获取已提交数据
     */
    private function getHasSubmit()
    {
        $staff_sn = session()->get('current_user')['staff_sn'];
        $data = Reimbursement::where(['staff_sn' => $staff_sn, 'is_homepage' => 1, 'is_delete' => 0])
            ->whereIn('status_id', [1, 2, 3])
            ->orderBy('send_time', 'desc')
            ->get();

        foreach ($data as $k => $v) {
            if (!empty($v['audited_cost'])) {
                $cost = $v['audited_cost'];
            } elseif (!empty($v['approved_cost'])) {
                $cost = $v['approved_cost'];
            } else {
                $cost = $v['send_cost'];
            }
            $data[$k]['cost'] = $cost;
        }
        return $data;
    }

    /**
     * 获取已完成单
     */
    public function getComplete()
    {
        $staff_sn = session('current_user')['staff_sn'];
        $data = Reimbursement::with('status')->where(['staff_sn'=>$staff_sn,'status_id'=>4,'is_delete'=>0])->orderBy('audit_time','desc')->get();
        return $data;
    }

    /**
     * 获取已驳回数据
     */
    private function getHasReject()
    {
        $staff_sn = session()->get('current_user')['staff_sn'];
        return Reimbursement::where(['staff_sn' => $staff_sn, 'status_id' => -1, 'is_homepage' => 1, 'is_delete' => 0])
            ->orderBy('reject_time', 'desc')
            ->get();
    }

}
