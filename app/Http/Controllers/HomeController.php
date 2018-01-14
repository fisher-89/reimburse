<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reimbursement;

class HomeController extends Controller
{

    public function showHomePage()
    {
//        dump(session('current_user'));
//        dd(cache()->get('approver'));
        if (!Cache()->has('approver')) {
            app('Approver')->approverUserToCache();
        }
        return view('home');
    }


    /**
     * 获取待办报销数量
     * @return type
     */
    public function countReimbursementToApprove()
    {
        $staff_sn = session()->get('current_user')['staff_sn'];
        $count = Reimbursement::where(['status_id' => 1, 'approver_staff_sn' => $staff_sn, 'is_delete' => 0, 'is_homepage' => 1])->count();
        return $count;
    }

}
