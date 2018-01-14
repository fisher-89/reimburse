@extends('public.layout')

@section('title')
    待审批报销单详情
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
                <div class="name"><span>申请人</span> : <span>{{$info['realname']}}</span> {{$info['send_time']}} </div>
            </div>
            <div>
                <div class="name"><span>所属部门</span> : {{$info->department_name}}</div>
            </div>
            <div>
                <div class="name"><span>资金归属</span> : {{$info->reim_department->name}}</div>
            </div>
            <div>
                <div class="name"><span>报销单号</span> : {{$info['reim_sn']}}</div>
            </div>
            <div>
                <div class="name"><span>描述</span> : {{$info['description']}}</div>
            </div>
            <div>
                <div class="name"><span>备注</span> : {{$info['remark']}}</div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="detail">
            <div class="title">消费明细</div>
            <form id="form">
                <div class="list">
                    @foreach($info->expenses as $v)
                        <div>
                            <div class="flat-green single-row">
                                <input type="checkbox" name="agree[]" value="{{$v->id}}" checked>
                            </div>
                            <a class="detail_cost" href="{{asset(route('check_expense',['id'=>$v->id]))}}">
                                <div class="logo text-center"><img src='{{asset($v->type->pic_path)}}'></div>
                                <div class="info"><h4
                                            title="{{$v->description}}">@if(mb_strlen($v['description'],'utf-8') > 6){{mb_substr($v['description'],0,6,'utf-8').'..'}} @else{{$v['description']}} @endif</h4>
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
    </div>
@endsection

@section('footer')
    <div class='handle_detail'>
        <button class="btn btn-lg btn-danger" id="reject" href="#myModals" data-toggle="modal">驳回</button>
        <button class="btn btn-lg btn-success" id="agree" onclick="pending_agree({{$info['id']}});">同意</button>
        <div class="clearfix"></div>
    </div>
@endsection

@section('js')
    @parent
    <!--驳回界面-->
    @include('public.confirm_reject')
    <!--icheck -->
    <script src="{{asset('js/iCheck/jquery.icheck.js')}}"></script>
    <script src="{{asset('js/icheck-init.js')}}"></script>
    <script>
        //同意处理
        function pending_agree(id) {
//        $(".waiting").show();
            if (confirm('是否同意？')) {
                var url = '/pending/agree';
                var expense_arr_id = $('#form').serialize();
                $.ajax({
                    type: 'post',
                    url: url,
                    data: expense_arr_id + '&id=' + id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (msg) {
                        $(".waiting").hide();
                        if (msg == 'success') {
                            window.history.back();
                        }else if(msg=='dingdingError'){
                            //获取钉钉号失败
                            window.history.back();
                        }
                    },
                    error: function (msg) {
                        if (msg.status == 422) {
                            var response = JSON.parse(msg.responseText);
                            for (var i in response) {
                                alert(response[i])
                            }
                        }
                    }
                });
            }
        }

        //消费明细处理选中
        $(document).on("change", "input[name='agree[]']", function () {
            var check;
            $(".icheckbox_flat-green").each(function () {
                if ($(this).hasClass("checked")) {
                    return check = false;
                } else {
                    return check = true;
                }
            });

            if (check) {
                $("#agree").addClass("disabled");
            } else if ($("#agree").hasClass("disabled")) {
                $("#agree").removeClass("disabled");
            }
        });
    </script>
@endsection