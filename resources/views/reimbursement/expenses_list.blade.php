@if(!empty($expenses))
<div class="title">
    <div class="count">共 <span>{{count($expenses)}}</span> 条消费</div>
    <div class="total">总计：<span>￥{{$cost}}</span></div>
    <div class="clearfix"></div>
</div>
<div class="list">
    @foreach($expenses as $k=>$v)
    <a href="{{asset(route('edit_expense',['sessionId'=>$k]))}}" class="btn btn-lg edit_expense">
        <div class="logo text-center"><img src="{{asset($v['type']['pic_path'])}}"></div>
        <div class="info"><h4>@if(mb_strlen($v['description'],'utf-8') >6){{mb_substr($v['description'],0,6,'utf-8').'..'}} @else {{$v['description']}} @endif</h4><p>{{substr($v['date'],0,10)}}</p></div>
        <div class="cost text-right">￥{{$v['send_cost']}}</div>
        <div class="bill_num">{{$v['bill_num']}}</div>
    </a>
    @endforeach
</div>
@endif
<input type="hidden" name="sent_cost" value="{{$cost}}">