@extends('public.layout')

@section('title')
    已驳回报销单
@endsection
@section('header_right')
    {'show':true,'control':true,'text':'刷新','onSuccess':function(){location.reload();}}
@endsection

@section('content')
    <div class='handle_list'>
        <div class='list'>
            @if(count($data) > 0)
                @foreach($data as $v)
                    <a href="{{asset(route('check_reimbursement',['id'=>$v['id']]))}}" class="btn btn-default account">
                        <div class="information pull-left">
                            <div class="date pull-left">
                                <p>{{date('m/d',strtotime($v->reject_time))}}
                                    <br>{{date('Y',strtotime($v->reject_time))}}</p>
                            </div>
                            <div class='name text-left'>
                                <p>{{$v['realname']}}<span style="margin-left:20%;">{{$v->status->name}}</span></p>
                                <p>{{$v['department_name']}}</p>
                            </div>
                        </div>
                        <div class="price pull-left"><p>
                                ￥@if(!empty($v['audit_time'])){{$v['audited_cost']}}@elseif(!empty($v['approve_time'])) {{$v['approved_cost']}}@else {{$v['send_cost']}} @endif</p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
@endsection
