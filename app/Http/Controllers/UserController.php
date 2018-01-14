<?php

namespace App\Http\Controllers;

use App\Services\Reimbursement\MyReimburse;


class UserController extends Controller {

    /**
     * 个人中心
     * @return type
     */
    public function showHomePage() {
        return view('personal.personal');
    }

    /**
     * 个人中心已完成报销单列表
     */
    public function showCompleteList(MyReimburse $myReim) {

        $data = $myReim->getComplete();
        return view('personal.has_completed_list', ['data' => $data]);
    }
    
    /**
     * 退出应用
     * @return type
     */
    public function logout(){
        session()->flush();
        $oaLogoutUrl =config('oa.logout');
        $url  =route('home');
        return redirect()->to($oaLogoutUrl.'?url='.$url);
    }
}
