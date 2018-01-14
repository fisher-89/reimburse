<?php

namespace App\Services;

/**
 * Description of Payee
 *
 * @author admin
 */
use App\Models\Payee as PayeeModel;
use App\Models\Region;
use App\Models\Reimbursement;
use DB;

class Payee
{

    /**
     * 获取收款人列表数据
     */
    public function getListData()
    {
        $staff_sn = session('current_user')['staff_sn'];
        $arr = PayeeModel::where('staff_sn', $staff_sn)->orderBy('id', 'desc')->get();
        return view('payee.list_data', ['payee' => $arr]);
    }

    /**
     * 保存收款人数据
     * @param type $request
     */
    public function savePayee($request)
    {
        $staff_sn = session()->get('current_user')['staff_sn'];
        $info = $request->except(['_url', '_token']);
        if(empty($info['city_of_account'])){
            $info['city_of_account'] = null;
        }
        if (isset($info['id']) && !empty($info['id'])) {//编辑
            PayeeModel::where(['id' => $info['id'], 'staff_sn' => $staff_sn])->update($info);
        } else {//新增
            $info = array_except($info, ['id']);
            $info['is_default'] = 0;
            $info['staff_sn'] = $staff_sn;
            PayeeModel::insert($info);
        }
        return 'success';
    }

    /**
     * 设为默认收款人
     * @param type $request
     */
    public function payee_default($request)
    {
        $id = intval($request->id);
        DB::transaction(function () use ($id) {
            PayeeModel::where('staff_sn', session('current_user')['staff_sn'])->update(['is_default' => 0]);
            PayeeModel::where('id', $id)->update(['is_default' => 1]);
        });
        return 'success';
    }

    /**
     * 删除收款人信息
     * @param Request $request
     */
    public function payeeDelete($request)
    {
        $id = intval($request->id);
        PayeeModel::where(['id' => $id])->delete();
        return 'success';
    }


    /**
     * 新增报销单 获取默认收款人数据
     * @param string
     * @return array
     */
    public function getDefaultPayeeUser()
    {
        $staff_sn = session('current_user')['staff_sn'];
        $payee = PayeeModel::where(['staff_sn' => $staff_sn, 'is_default' => 1])->first();
        $data['payee_id'] = isset($payee->id) ? $payee->id : '0';
        $data['payee_name'] = isset($payee->bank_account_name) ? $payee->bank_account_name : '';
        return $data;
    }

    /**
     * 地区省市区数据存入静态文件
     */
    public function RegionDataToStatic()
    {
        $fileName = public_path('js/reimburse/region.js');
        if (!file_exists($fileName)) {
            $regionData = $this->getRegionData();
            $this->makeFile($fileName, $regionData);
        }
    }

    private function makeFile($fileName, $regionData)
    {
        if (!file_exists($fileName)) {
            fopen($fileName, 'w');
        }
        file_put_contents($fileName, $regionData);
    }

    private function getRegionData()
    {
        $data = Region::get();
        $province = [];//省
        $city = [];//市
        foreach ($data as $v) {
            if ($v->level == 1) {
                $province[] = $v;
            } elseif ($v->level ==2) {
                $city[] = $v;
            }
        }

        $province = json_encode($province, JSON_UNESCAPED_UNICODE);
        $city = json_encode($city, JSON_UNESCAPED_UNICODE);
        $regionJsClass = 'function regionClass(){this.province=function(){return ' . $province . ';};this.city=function(province_id){return '.$city.'};}';
        return $regionJsClass;
    }

}
