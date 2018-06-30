<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | This file is where you may define all of the routes that are handled
  | by your application. Just tell Laravel the URIs it should respond
  | to using a Closure or controller method. Build something great!
  |
 */
Route::group(['middleware' => 'dingding'], function () {
    Route::get('/', function () {
        return redirect('/home');
    });
    Route::get('/home', ['uses' => 'HomeController@showHomePage'])->name('home'); //首页
    Route::get('/button', ['as' => 'button', 'uses' => 'HomeController@countReimbursementToApprove']); //获取首页待审数量
    Route::get('/create_reimbursement/{id?}', ['uses' => 'ReimburseController@showCreatePage'])->name('create_reimbursement'); //创建报销单和编辑报销单
    Route::get('add_approver_user', ['uses' => 'ReimburseController@addApproverUser'])->name('add_approver_user'); //添加审批人 
    Route::post('/get_reimburse_payee_approver_expense', 'ReimburseController@get_reimburse_payee_approver_expense'); //编辑获取收款人、审批人、消费明细数据
    Route::post('/create_reimbursement/{id?}', ['uses' => 'ReimburseController@addReimbursement']); //保存、提交送审报销单处理

    /* -------收款人start------------------ */
    Route::get('/payee_list', ['uses' => 'PayeeController@payeeList'])->name('payee_list'); //收款人列表
    Route::get('/get_payee_list_data', ['uses' => 'PayeeController@getPayeeListData']); //收款人列表数据
    Route::get('/payee_create_or_edit/{id?}', ['uses' => 'PayeeController@payeeCreateOrEdit'])->name('payee_create_or_edit'); //收款人资料详情（添加与编辑）
    Route::post('/payee_save', ['uses' => 'PayeeController@submitPayee']); //提交收款人资料
    Route::post('/payee_default', ['uses' => 'PayeeController@default_payee']); //设为默认收款人
    Route::get('/payee_delete', ['uses' => 'PayeeController@delete'])->name('payee_delete'); //删除收款人信息
    Route::post('/getDefaultPayeeUser', ['uses' => 'PayeeController@getDefaultPayeeUser']); //新增时获取默认收款人
    /* -------收款人end------------------ */

    /* ----------消费明细start--------------- */
    Route::post('add_bill', ['uses' => 'ExpenseController@uploadBill'])->name('add_bill'); //上传发票照片
    Route::get('add_expense/{id?}/{sessionId?}', ['as' => 'add_expense', 'uses' => 'ExpenseController@showCreatePage']); //添加、编辑消费明细
    Route::get('check_expense/{id}', ['uses' => 'ExpenseController@checkExpense'])->name('check_expense')->where('id', '\d+'); //查看消费明细详情
    /* ----------消费明细end--------------- */

    /* ------------------我的报销单start----------------- */
    Route::get('my_reimbursments', function () {
        return view('reimbursement/my_reimbursement_list');
    })->name('mine'); //我的报销单
    Route::get('/get_reimbursement_list', ['uses' => 'ReimburseController@showMyReimbursements']); //AJAX获取我的报销单列表
    Route::get('check_reimbursement/{id}', ['uses' => 'ReimburseController@checkReimbursement'])->name('check_reimbursement')->where('id', '\d+'); //查看报销单详情
    Route::post('withdraw', ['uses' => 'ReimburseController@withdraw'])->name('withdraw'); //撤回（我的报销单）
    Route::post('/deleteReim', ['uses' => 'ReimburseController@deleteReim'])->name('deleteReim');//删除未提交的单
    Route::post('/deleteReject', ['uses' => 'ReimburseController@deleteReject'])->name('deleteReject');//删除驳回的单
    /* ------------------我的报销单end----------------- */

    /* ----------------------待审批报销单start-------------------------- */
    Route::get('approve', function () {
        return view('pending.approving_list');
    })->name('pending_list'); //待审批报销单
    Route::get('/get_pending_list', ['uses' => 'ApproveController@showPendingList']); //AJAX获取待审批报销单列表
    Route::get('pending/detail/{id}', ['uses' => 'ApproveController@showPendingDetail'])->name('pendingDetails')->where('id', '\d+'); //待审批报销单（详情页）
    Route::post('pending/reject', ['uses' => 'ApproveController@pendingReject']); //驳回(待审批单)
    Route::post('/pending/agree', ['uses' => 'ApproveController@pendingAgree']); //同意（待审批报销单）
    /* ----------------------待审批报销单end-------------------------- */

    Route::get('/haveApprovalList', ['uses' => 'ApproveController@haveApprovalList'])->name('haveApprovalList'); //已审批报销单列表
    Route::get('/hasRejectedList', ['uses' => 'ApproveController@hasRejectedList'])->name('hasRejectedList'); //已驳回报销单列表
    Route::get('hasCompletedList', ['uses' => 'UserController@showCompleteList'])->name('hasCompletedList'); //已完成报销单列表
    Route::get('personal', ['uses' => 'UserController@showHomePage'])->name('personal'); //个人中心

    Route::get('/getCsrfToken', function () {
        return 'getCsrfTokenSuccess';
    });//续期csrfToken
});

Route::get('/logout', ['uses' => 'UserController@logout']); //退出应用
