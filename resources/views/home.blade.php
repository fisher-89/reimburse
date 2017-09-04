@extends('public.layout')
@section('title')
报销系统
@endsection

@section('header_right')
{'show':true,'control':true,'text':'个人中心','onSuccess':function(){window.location.href="{{asset(route('personal'))}}"}}
@endsection

@section('content')
<!--报错start-->
@if(count($errors->all())>0)
@foreach($errors->all() as $k=>$v)
<p id="errors" class="text-center"  style="color:red;">{{$v}}</p>
@endforeach
@endif
<!--报错end-->
<div class='home'>
    <div class='top' id='button'>
        @if((Cache::get('approver'))&&(in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver1']) || in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver2']) || in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver3'])))
        <a id="unapprove" href="{{asset(route('pending_list'))}}" class="btn btn-lg btn-default">待审批报销单 <i class="fa fa-angle-right"></i></a>
        <a href="{{asset(route('haveApprovalList'))}}" class="btn btn-lg btn-default">已审批报销单 <i class="fa fa-angle-right"></i></a>
        <a href="{{asset(route('hasRejectedList'))}}" class="btn btn-lg btn-default">已驳回报销单<i class="fa fa-angle-right"></i></a>
        @endif
        <a href="{{asset(route('mine'))}}" class="btn btn-lg btn-default">我的报销单 <i class="fa fa-angle-right"></i></a>
    </div>
</div>
@endsection

@section('footer')
<div class='home'>
    <a href="{{asset(route('create_reimbursement'))}}" class='btn btn-lg btn-success' style="width: 100%;margin:0;margin-bottom: 10px;"><i class='fa fa-plus'></i>创建报销单</a>
</div>
@endsection

@section('js')
@parent
<script>
    //获取待审批的数量
    countReimbursementToApprove();
    function countReimbursementToApprove() {
        $.ajax({
            type: "GET",
            url: "{{asset(route('button'))}}",
            data: {"timestamp": new Date().getTime()},
            dataType: "text",
            success: function (msg) {
                if (msg > 0) {
                    var h = '<div class="count">' + msg + '</div>';
                    $("#unapprove").append(h);
                }
            }
        });
    }
</script>
@endsection