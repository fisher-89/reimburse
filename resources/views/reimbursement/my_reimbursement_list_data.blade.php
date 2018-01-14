<!------------------------------------------------------未提交 start---------------------------->
<div id="notSubmit" class="tab-pane active">
    <div class='handle_list'>
        <div class='list'>
            @foreach($data['notSubmit'] as $v)
            <a class="btn btn-default account local" href="{{asset(route('create_reimbursement',['id'=>$v['id']]))}}">
                <div class="information pull-left">
                    <div class="date pull-left">
                    </div>
                    <div class='name text-left'>
                        <p title="{{$v['description']}}">描述: @if(mb_strlen($v['description'], 'utf-8') > 6) {{mb_substr($v['description'], 0, 6, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                        <p>状态：<i>{{$v->status->name}}</i>
                        </p>
                    </div>
                </div>
                <div class="price pull-left"><p>￥{{$v->send_cost or 0}}</p></div>
            </a>
            @endforeach
        </div>    
    </div>
</div>
<!------------------------------------------------------------------未提交end------------------------------------------------>
<!---------------------------------------------处理中strat------------------------------------------->
<div id="hasSubmit" class="tab-pane ">
    <div class='handle_list'>
        <div class='list'>
            @foreach($data['hasSubmit'] as $v)
            <a class="btn btn-default account" href="{{asset(route('check_reimbursement',['id'=>$v['id']]))}}">
                <div class="information pull-left">
                    <div class="date pull-left">
                        <p>{{date('m/d',strtotime($v->send_time))}}<br>{{date('Y',strtotime($v->send_time))}}</p>
                    </div>
                    <div class='name text-left'>
                        <p title="{{$v['description']}}">描述: @if(mb_strlen($v['description'], 'utf-8') > 6) {{mb_substr($v['description'], 0, 6, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                        <p>状态：<i>{{$v->status->name}}</i>
                        </p>
                    </div>
                </div>
                <div class="price pull-left"><p>￥{{$v->cost}}</p></div>
            </a>
            @endforeach
        </div>    
    </div>
</div>
<!------------------------------------------------------处理中end------------------------------------>

<!---------------------------------------------已完成strat------------------------------------------->
<div id="complete" class="tab-pane ">
    <div class='handle_list'>
        <div class='list'>
            @if(count($data['complete']) > 0)
            @foreach($data['complete'] as $v)
                <a class="btn btn-default account" href="{{asset(route('check_reimbursement',['id'=>$v['id']]))}}">
                    <div class="information pull-left">
                        <div class="date pull-left">
                            <p>{{date('m/d',strtotime($v->send_time))}}<br>{{date('Y',strtotime($v->send_time))}}</p>
                        </div>
                        <div class='name text-left'>
                            <p title="{{$v['description']}}">描述: @if(mb_strlen($v['description'], 'utf-8') > 6) {{mb_substr($v['description'], 0, 6, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                            <p>状态：<i>{{$v->status->name}}</i>
                            </p>
                        </div>
                    </div>
                    <div class="price pull-left"><p>￥{{$v['audited_cost']}}</p></div>
                </a>
            @endforeach
                @endif
        </div>
    </div>
</div>
<!------------------------------------------------------已完成end------------------------------------>

<!-------------------------------------------------------已驳回start-------------------------------------------->
<div id="hasReject" class="tab-pane ">
    <div class='handle_list'>
        <div class='list'>
            @foreach($data['hasReject'] as $v)
            <a class="btn btn-default account reject" href="{{asset(route('check_reimbursement',['id'=>$v['id']]))}}">
                <div class="information pull-left">
                    <div class="date pull-left">
                        <p>{{date('m/d',strtotime($v->reject_time))}}<br>{{date('Y',strtotime($v->reject_time))}}</p>
                    </div>
                    <div class='name text-left'>
                        <p title="{{$v['description']}}">描述: @if(mb_strlen($v['description'], 'utf-8') > 6) {{mb_substr($v['description'], 0, 6, 'utf-8') . '..'}} @else {{$v['description']}} @endif</p>
                        <p>状态：<i>{{$v->status->name}}</i>
                        </p>
                    </div>
                </div>
                <div class="price pull-left"><p>￥{{$v->send_cost}}</p></div>
            </a>
            @endforeach
        </div>    
    </div>
</div>
<!------------------------------------------------------已驳回end-------------------------------------------------->


