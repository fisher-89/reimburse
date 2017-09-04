@extends('public.layout')

@section('title')
选择审批人
@endsection

@section('content')
<div class='handle_list col-lg-12'>
    <div class='list'>
        <div class="text-center text-info"> 请选择审批人</div>
        <div class="handle_list">
            @foreach($approver as $k=>$v)
            <p><a href="javascript:choose_approver('{{$v->staff_sn}}','{{$v->realname}}')" class="btn btn-default">{{$v->realname}}</a></p
           @endforeach
        </div>
    </div>    
</div>

@endsection
@section('js')
@parent
<script>
   function choose_approver(staff_sn,realname){
       var data =new Object();
       data.approver_name = realname;
       data.approver_staff_sn = staff_sn;
       sessionStorage.setItem('approver',JSON.stringify(data));
       window.history.go(-1);
   }
</script>
@endsection