<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payee;

class PayeeController extends Controller {

    public $payee;

    public function __construct(\App\Services\Payee $payee) {
        $this->payee = $payee;
    }

    /**
     * 收款人列表
     * @return type
     */
    public function payeeList() {
        return view('payee.list');
    }

    /**
     * 获取收款人列表数据
     * @return type
     */
    public function getPayeeListData() {
        return $this->payee->getListData();
    }

    /**
     * 收款人添加或编辑
     */
    public function payeeCreateOrEdit(Request $request) {
        $this->payee->RegionDataToStatic();//地区省市区数据存入静态文件

        $id = $request->id;
        $user = Payee::find($id);
        return view('payee.add_or_edit', ['user' => $user]);
    }

    /**
     * 提交收款人资料处理
     */
    public function submitPayee(Request $request) {
        $rules = [
            'phone' => 'required|digits:11',
            'bank_other' => 'required|string|between:4,8|in:中国农业银行,中国工商银行,中国建设银行,中国银行,交通银行,招商银行,中国邮政储蓄银行',
            'bank_account_name' => 'required|string|between:2,5',
            'bank_account' => 'required|digits_between:15,19',
            'province_of_account' => 'required|exists:region,id',
            'bank_dot' => 'required_if:bank_other,中国工商银行,中国建设银行,中国银行,交通银行,招商银行,中国邮政储蓄银行|string|max:30',
        ];
        if($request->city_of_account){
            $rules['city_of_account'] = 'required|numeric|exists:region,id,parent_id,'.$request->province_of_account;
        }
        $this->validate($request, $rules, [], trans('fields.payee'));
        return $this->payee->savePayee($request);
    }

    /**
     * 删除收款人信息
     * @param Request $request
     */
    public function delete(Request $request) {
        return $this->payee->payeeDelete($request);
    }

    /**
     * 设为默认收款人
     */
    public function default_payee(Request $request) {
        return $this->payee->payee_default($request);
    }

    /**
     * 新增时获取默认收款人
     */
    public function getDefaultPayeeUser(Request $request) {
        return $this->payee->getDefaultPayeeUser($request);
    }
    

}
