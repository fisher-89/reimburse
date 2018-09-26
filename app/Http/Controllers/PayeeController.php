<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payee;
use Illuminate\Validation\Rule;

class PayeeController extends Controller
{

    public $payee;

    public function __construct(\App\Services\Payee $payee)
    {
        $this->payee = $payee;
    }

    /**
     * 收款人列表
     * @return type
     */
    public function payeeList()
    {
        return view('payee.list');
    }

    /**
     * 获取收款人列表数据
     * @return type
     */
    public function getPayeeListData()
    {
        return $this->payee->getListData();
    }

    /**
     * 收款人添加或编辑
     */
    public function payeeCreateOrEdit(Request $request)
    {
        $this->payee->RegionDataToStatic();//地区省市区数据存入静态文件

        $id = $request->id;
        $user = Payee::find($id);
        return view('payee.add_or_edit', ['user' => $user]);
    }

    /**
     * 提交收款人资料处理
     */
    public function submitPayee(Request $request)
    {
        $rules = [
            'phone' => 'required|digits:11',
            'bank_other' => ['required', 'exists:banks,name'],
            'bank_account_name' => 'required|string|between:2,20',
            'bank_account' => 'required|digits_between:9,22',
            'province_of_account' => 'required|exists:region,id',
            'bank_dot' => 'required_unless:bank_other,中国农业银行|string|max:30',
            'is_public' => ['required', Rule::in(['0', '1'])],
        ];
        if ($request->city_of_account) {
            $rules['city_of_account'] = 'required|numeric|exists:region,id,parent_id,' . $request->province_of_account;
        }
        $this->validate($request, $rules, [], trans('fields.payee'));
        return $this->payee->savePayee($request);
    }

    /**
     * 删除收款人信息
     * @param Request $request
     */
    public function delete(Request $request)
    {
        return $this->payee->payeeDelete($request);
    }

    /**
     * 设为默认收款人
     */
    public function default_payee(Request $request)
    {
        return $this->payee->payee_default($request);
    }

    /**
     * 新增时获取默认收款人
     */
    public function getDefaultPayeeUser(Request $request)
    {
        return $this->payee->getDefaultPayeeUser($request);
    }


}
