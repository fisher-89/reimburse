<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller {

    public $expense;

    public function __construct(\App\Services\Reimbursement\Expense $expense) {
        $this->expense = $expense;
    }

    /**
     * 明细视图
     * @return type
     */
    public function showCreatePage() {
        $this->expense->getExpenseTypes(); //缓存获取类型
        return view('expense');
    }

    /**
     * 上传发票
     * @param Request $request
     * @return type
     */
    public function uploadBill(Request $request) {
        $img = base64_decode($request->content);
        $uploadPath = 'uploads/bills/' . date('Y') . '/' . date('m') . '/' . date('d');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        $filename = time() . mt_rand(1000, 9999);
        file_put_contents($uploadPath . '/' . $filename, $img);
        // $img->move($uploadPath, $filename);
        $info['file'] = url()->asset($uploadPath . '/' . $filename);
        $info['save'] = $uploadPath . '/' . $filename;
        $info['status'] = 1;
        return json_encode($info);
    }

    /**
     * 查看时消费明细详情展示
     * @param Request $request
     * @return type
     */
    public function checkExpense(Request $request) {
        $id = $request->id;
        $info = Expense::with('type', 'bills')->find($id);
        return view('check_expense', ['info' => $info]);
    }

    /* ------------------------------------------------------------------------------------------- */


}
