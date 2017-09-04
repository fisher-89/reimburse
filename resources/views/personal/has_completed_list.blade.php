@extends('public.layout')

@section('title')
已完成报销单
@endsection
@section('header_right')
    {'show':true,'control':true,'text':'刷新','onSuccess':function(){location.reload();}}
@endsection
@section('content')
<div class='handle_list'>
    <div class='list'>
        @if(count($data) > 0)
        @foreach($data as $k=>$v)
        <a href="{{asset(route('check_reimbursement',['id'=>$v['id']]))}}" class="btn btn-default account">
            <div class="information pull-left">
                    <div class="date pull-left">
                        <p>{{date('m/d',strtotime($v->send_time))}}<br>{{date('Y',strtotime($v->send_time))}}</p>
                    </div>
                    <div class='name text-left'>
                        <p title="{{$v['description']}}">@if(mb_strlen($v['description'], 'utf-8') > 6) {{mb_substr($v['description'], 0, 6, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                    <p>状态：<i>{{$v->status->name}}</i><span style="margin: 0 0 0 20%;"></span></p>
                        </p>
                    </div>
            </div>
            <div class="price pull-left"><p>￥{{$v['audited_cost']}}</p></div>
        </a>
        @endforeach
        @endif
    </div>    
</div>
@endsection
