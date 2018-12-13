<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReimDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FundingBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ReimDepartment::with(['auditor'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validater($request);
        $reimDepartment = new ReimDepartment();
        $reimDepartment->name = $request->name;
        $reimDepartment->manager_sn = $request->manager_sn;
        $reimDepartment->manager_name = $request->manager_name;
        $reimDepartment->cashier_sn = $request->cashier_sn;
        $reimDepartment->cashier_name = $request->cashier_name;
        DB::beginTransaction();
        $reimDepartment->save();
        $reimDepartment->auditor()->createMany($request->auditor);
        DB::commit();
        $reimDepartment->load('auditor');
        return response($reimDepartment, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\ReimDepartment $reimDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReimDepartment $reimDepartment)
    {
        $this->validater($request);
        $reimDepartment->manager_sn = $request->manager_sn;
        $reimDepartment->manager_name = $request->manager_name;
        $reimDepartment->cashier_sn = $request->cashier_sn;
        $reimDepartment->cashier_name = $request->cashier_name;
        DB::beginTransaction();
        $reimDepartment->save();
        $reimDepartment->auditor()->delete();
        $reimDepartment->auditor()->createMany($request->auditor);
        DB::commit();
        $reimDepartment->load('auditor');
        return response($reimDepartment, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReimDepartment $reimDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReimDepartment $reimDepartment)
    {
        $reimDepartment->delete();
        return response('', 204);
    }

    public function validater(Request $request, $rules = [], $message = [], $customAttributes = [])
    {
        $id = $request->route('reimDepartment') ? $request->route('reimDepartment')->id : null;
        $rules = [
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('reim_departments', 'name')
                    ->whereNot('id', $id),
            ],
            'manager_sn' => ['required_with:manager_name', 'integer', 'min:100000', 'max:999999'],
            'manager_name' => ['required', 'string', 'max:10'],
            'cashier_sn' => ['required_with:cashier_name', 'integer', 'min:100000', 'max:999999'],
            'cashier_name' => ['required', 'string', 'max:10'],
            'auditor' => ['required', 'array'],
            'auditor.*.auditor_staff_sn' => ['required', 'integer', 'min:100000', 'max:999999'],
            'auditor.*.auditor_realname' => ['required', 'string', 'max:10'],
        ];
        $message = [];
        $customAttributes = [
            'name' => '名称',
            'manager_sn' => '品牌副总员工编号',
            'manager_name' => '品牌副总',
            'cashier_sn' => '出纳员工编号',
            'cashier_name' => '出纳',
            'auditor' => '财务审核人',
            'auditor.*.auditor_staff_sn' => '财务审核人员工编号',
            'auditor.*.auditor_realname' => '财务审核人姓名',
        ];
        return parent::validate($request, $rules, $message, $customAttributes);
    }
}
