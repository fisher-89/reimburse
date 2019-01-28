<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApproverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Department::with('approver1', 'approver2', 'approver3')->get();
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
        $department = new Department();
        $department->department_id = $request->department_id;
        $department->reim_department_id = $request->reim_department_id;
        DB::beginTransaction();
        $department->save();
        $this->saveApprovers($request, $department);
        DB::commit();
        app('Approver')->approverUserToCache();
        $department->load(['approver1', 'approver2', 'approver3']);
        return response($department, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $this->validater($request);
        $department->department_id = $request->department_id;
        $department->reim_department_id = $request->reim_department_id;
        DB::beginTransaction();
        $department->save();
        $department->approver1()->delete();
        $department->approver2()->delete();
        $department->approver3()->delete();
        $this->saveApprovers($request, $department);
        DB::commit();
        app('Approver')->approverUserToCache();
        $department->load(['approver1', 'approver2', 'approver3']);
        return response($department, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return response('', 204);
    }

    /**
     * 验证添加和编辑提交内容
     *
     * @param Request $request
     * @param array $rules
     * @param array $message
     * @param array $customAttributes
     * @return array
     */
    public function validater(Request $request, $rules = [], $message = [], $customAttributes = [])
    {
        $id = $request->route('department') ? $request->route('department')->id : null;
        $rules = [
            'department_id' => [
                'required',
                'integer',
                Rule::unique('departments', 'department_id')
                    ->whereNot('id', $id),
            ],
            'reim_department_id' => [
                'required',
                'integer',
                Rule::exists('reim_departments', 'id')->whereNull('deleted_at'),
            ],
            'approver1' => ['required', 'array'],
            'approver1.*.staff_sn' => ['required', 'integer', 'min:100000', 'max:999999'],
            'approver1.*.realname' => ['required', 'string', 'max:10'],
            'approver2' => ['required_with:approver3', 'array'],
            'approver2.*.staff_sn' => ['required', 'integer', 'min:100000', 'max:999999'],
            'approver2.*.realname' => ['required', 'string', 'max:10'],
            'approver3' => ['array'],
            'approver3.*.staff_sn' => ['required', 'integer', 'min:100000', 'max:999999'],
            'approver3.*.realname' => ['required', 'string', 'max:10'],
        ];
        $message = [];
        $customAttributes = [
            'department_id' => '部门',
            'reim_department_id' => '资金归属',
            'approver1' => '一级审批人',
            'approver1.*.staff_sn' => '一级审批人编号',
            'approver1.*.realname' => '一级审批人姓名',
            'approver2' => '二级审批人',
            'approver2.*.staff_sn' => '二级审批人编号',
            'approver2.*.realname' => '二级审批人姓名',
            'approver3' => '三级审批人',
            'approver3.*.staff_sn' => '三级审批人编号',
            'approver3.*.realname' => '三级审批人姓名',
        ];
        return parent::validate($request, $rules, $message, $customAttributes);
    }

    protected function saveApprovers(Request $request, Department $department)
    {
        $department->approver1()->createMany(array_map(function ($item) {
            $item['priority'] = 1;
            return $item;
        }, $request->approver1));
        $department->approver2()->createMany(array_map(function ($item) {
            $item['priority'] = 2;
            return $item;
        }, $request->approver2));
        $department->approver3()->createMany(array_map(function ($item) {
            $item['priority'] = 3;
            return $item;
        }, $request->approver3));
    }
}
