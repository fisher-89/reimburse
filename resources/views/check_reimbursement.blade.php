@extends('public.layout')

@section('title')
    @if($info['status_id'] ==1)
        待审批报销单
    @elseif($info['status_id'] >1 && $info['status_id'] <4)
        已审批报销单
    @elseif($info['status_id'] == 4)
        已完成报销单
    @elseif($info['status_id'] == -1)
        已驳回报销单
    @endif
@endsection


@section('header_right')
    @if($info['status_id'] == -1 && $info['staff_sn'] == session('current_user')['staff_sn'])
        {'show':true,'control':true,'text':'删除','onSuccess':function(){delReject({{request()->id}});}}
    @endif
@endsection


@section('css')
    @parent
    <!--icheck-->
    <link href="{{asset('js/iCheck/skins/flat/green.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class='handle_detail'>
        <div class="info">
            <div class="title">基本信息</div>
            <div>
                <div class="name"><span>申请人</span> : <span>{{$info['realname']}}</span>{{$info['send_time']}}</div>
            </div>
            <div>
                <div class="name"><span>部门</span> : {{$info->department_name }}</div>
            </div>

            @if($info['status_id']>0 || ($info['status_id']==-1 && isset($info['approver_name'])))
                @if(!empty($info['approver_staff_sn']))
                    <div>
                        <div class="name"><span>审批人</span> :
                            <span>{{$info['approver_name']}}</span>@if($info['status_id'] == 1)({{$info->status->name}}
                            ) @endif  @if($info['status_id'] >1 || $info['status_id'] == -1){{$info['approve_time']}} @endif
                        </div>
                    </div>
                @endif
            @endif

            @if($info['status_id'] ==4)
                <div>
                    <div class="name"><span>审核人</span> :
                        <span>{{$info['accountant_name']}}</span> {{$info['audit_time']}}</div>
                </div>
            @endif

            <div>
                <div class="name"><span>资金归属</span> : {{$info->reim_department->name}}</div>
            </div>
            <div>
                <div class="name"><span>报销单号</span> : {{$info['reim_sn']}}</div>
            </div>
            @if(!empty($info['description']))
                <div>
                    <div class="name"><span>描述</span> : {{$info['description']}}</div>
                </div>
            @endif

            @if(!empty($info['remark']))
                <div>
                    <div class="name"><span>备注</span> : {{$info['remark']}}</div>
                </div>
            @endif

            <div>
                <div class="name"><span>状态</span> : <span
                            style="@if($info->status_id==-1)color:#ec1c1c;@endif">{{$info->status->name}}</span></div>
            </div>
            @if($info['status_id'] == -1)
                <div>
                    <div class="name"><span>驳回人</span> : <span>{{$info['reject_name']}}</span> {{$info['reject_time']}}
                    </div>
                </div>
                <div>
                    <div class="name"><span>驳回原因</span> : {{$info['reject_remarks']}}</div>
                </div>
            @endif
            @if($info['staff_sn'] == session('current_user')['staff_sn'])
                <div>
                    <div class="name"><span>银行</span> : {{$info['payee_bank_other']}}</div>
                </div>
                <div>
                    <div class="name"><span>银行卡号</span> : {{$info['payee_bank_account']}}</div>
                </div>
                <div>
                    <div class="name"><span>收款人</span> : {{$info['payee_name']}}</div>
                </div>
                <div>
                    <div class="name"><span>手机</span> : {{$info['payee_phone']}}</div>
                </div>
                <div>
                    <div class="name"><span style="font-size:10px;">账户省、市</span> : {{$info['payee_province']}}
                        - {{$info['payee_city']}}</div>
                </div>
                @if($info['payee_bank_other'] !="中国农业银行")
                    <div>
                        <div class="name"><span>开户网点</span> : {{$info['payee_bank_dot']}}</div>
                    </div>
                @endif
            @endif
        </div>
        @if(count($info->expenses) > 0)
            <div class="detail">
                <div class="title">消费明细</div>
                <form id="form">
                    <div class="list">
                        @foreach($info->expenses as $v)
                            <div>
                                <div class="flat-green single-row">
                                    @if(($info['status_id'] > 1 && $info['status_id'] < 4 && $v['is_approved'] == 1)||($info['status_id'] ==4 && $v['is_audited'] == 1))
                                        <input type="checkbox" checked>
                                    @else
                                        <input type="checkbox">
                                    @endif
                                    <div class="cover"></div>
                                </div>
                                <a class="detail_cost" href="{{asset(route('check_expense',['id'=>$v['id']]))}}">
                                    <div class="logo text-center"><img src='{{asset($v->type->pic_path)}}' title="{{$v->type->name}}"></div>
                                    <div class="info"><h4
                                                title="{{$v['description']}}">@if(mb_strlen($v['description'],'utf-8') > 6){{mb_substr($v['description'],0,6,'utf-8').'..'}} @else{{$v['description']}} @endif</h4>
                                        <p>{{$v['date']}}</p></div>
                                    <div class="cost text-right">￥{{$v['send_cost']}}</div>
                                    <div class="bill_num">{{count($v->bills)}}</div>
                                </a>
                                <div class="clearfix"></div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection

@section('footer')
    <div class='handle_detail'>
        @if($info['status_id'] == -1 && $info['staff_sn'] == session()->get('current_user')['staff_sn'])
            <a href="{{asset(route('create_reimbursement',['id'=>$info['id']]))}}" style="width:100%;"
               class="btn btn-lg btn-danger">重新编辑</a>
        @endif
        @if($info['status_id'] == 1 && $info['staff_sn'] == session('current_user')['staff_sn'])
            <a style="width:100%;" class="btn btn-lg btn-danger" onclick="withdraw({{$info['id']}})">撤回</a>
        @endif
        <div class="clearfix"></div>
    </div>
@endsection

@section('js')
    @parent
    <!--icheck -->
    <script src="{{asset('js/iCheck/jquery.icheck.js')}}"></script>
    <script src="{{asset('js/icheck-init.js')}}"></script>
    <script>
        //撤回
        function withdraw(id) {
            if (confirm('确认撤回？')) {
                $.ajax({
                    type: 'post',
                    url: '/withdraw',
                    data: {id: id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (msg) {
                        if (msg == 'success') {
                            window.history.go(-1);
                        } else if (msg == 'dingdingError') {
                            alert('撤回成功，消息发送失败');
                            window.history.go(-1);
                        } else {
                            alert('撤回失败！')
                        }
                    }
                });
            }
        }

        //驳回单删除
        function delReject(id) {
            if (confirm('确认删除？')) {
                var url = '/deleteReject';
                $.ajax({
                    type: 'post',
                    url: url,
                    data: {id: id},
                    dataType: 'text',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (msg) {
                        if (msg === 'success') {
                            window.history.go(-1);
                        } else if (msg === 'error') {
                            alert("删除失败")
                        }
                    }
                });
            }
        }

    </script>
@endsection