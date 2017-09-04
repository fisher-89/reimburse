<div class='list'>
    @if(count($list)>0)
        @foreach($list as $v)
            <a class="btn btn-default account" href="{{asset(route('pendingDetails',['id'=>$v['id']]))}}">
                <div class="information pull-left">
                    <div class="date pull-left">
                        <p>{{date('m/d',strtotime($v->send_time))}}</p>
                        <p>{{date('Y',strtotime($v->send_time))}}</p>
                    </div>
                    <div class='name text-left'>
                        <p>{{$v['realname']}}<span style="margin:0 0 0 20%;"> {{$v['department->name']}}</span></p>
                        <p title="{{$v['description']}}">@if(mb_strlen($v['description'], 'utf-8') > 10) {{mb_substr($v['description'], 0, 10, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                    </div>
                </div>
                <div class="price pull-left"><p>￥{{$v['send_cost']}}</p></div>
            </a>
        @endforeach
    @else
        <button type="button" style="width:100%;" class="btn-warning" onclick="history.go(-1)">无待审批报销单！请点击返回</button>
    @endif
</div>    