@extends('public.layout')

@section('title')
消费明细
@endsection

@section('content')
<div class='new_cost'>
    <form>
        <div>
            <p><i class="fa fa-calendar"></i> 日期</p>
            {{$info['date']}}
        </div>
        <div id="expense_type">
            <p><i class="fa fa-list-ul"></i> 类型</p>
            <img src="{{asset($info->type->pic_path)}}"> <span>{{$info->type->name}}</span>
        </div>
        <div>
            <p><i class="fa fa-cny"></i> 金额</p>
            @if($info['is_audited'] == 1)
            {{$info['audited_cost']}}
            @else
            {{$info['send_cost']}}
            @endif
        </div>
        <div>
            <p><i class="fa fa-pencil" ></i> 描述</p>
            <textarea title="{{$info['description']}}" readonly style=" width:70%;height:100px;">{{$info['description']}}</textarea>
        </div>
    </form>
    @if(isset($info->bills) && count($info->bills)>0)
    <form>
        <div>
            <p><i class="fa fa-tag"></i> 发票</p>
            <label></label>
        </div>
    </form>
    <div class="bill_block">
        @foreach($info->bills as $v)
        <img src="{{asset($v->pic_path)}}" alt="{{ $v->pic_path }}">
        @endforeach

    </div>
    @endif
</div>
@endsection
