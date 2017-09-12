@extends('public.layout')

@section('title')
    个人中心
@endsection

@section('content')
    <div class='personal'>
        <div class="face text-center" id="background">
            <p class="avatar"><img
                        src="https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=1454936901,242194328&fm=117&gp=0.jpg"
                        width="80px" height="80px" alt="" title="头像"></p>
            <p class="name">{{session('current_user')['realname']}}</p>
            <p class="department">{{session('current_user')['department']['full_name']}}</p>
        </div>
        <div class='group'>
            <a href="{{asset(route('hasCompletedList'))}}" class='btn btn-default'><i class='fa fa-file-text-o'></i>已完成报销单</a>
            @if((Cache::get('approver'))&&(in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver1']) || in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver2']) || in_array(session('current_user')['staff_sn'],Cache::get('approver')['approver3'])))
                <a href="{{asset(route('haveApprovalList'))}}" class='btn btn-default'><i class='fa fa-paste'></i>已审批报销单</a>
                <a href="{{asset(route('hasRejectedList'))}}" class='btn btn-default'><i class='fa fa-paste'></i>已驳回报销单</a>
            @endif
        </div>
        <div class='group'>
            <a href="{{asset(route('payee_list'))}}" class='btn btn-default'><i class='fa fa-gear'></i>收款人设置</a>
            <a id="exit" class='btn btn-default'><i class='fa fa-power-off'></i>退出应用</a>
        </div>
    </div>
@endsection
@section('js')
    @parent
    <script>
        $('#exit').on('click', function () {
            window.location.href = '/logout';
        });

        $(function () {
            var per = new personal();
            per.getBackground();
        });

        function personal() {
            this.getBackground = function () {
                var img = 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1504417787662&di=9c9c0cd95732f1ca703d2a7a33468345&imgtype=0&src=http%3A%2F%2Fimg1.qunarzz.com%2Ftravel%2Fd9%2F1606%2Faf%2Ffb303f8dabb6589a.jpg_r_640x426x70_a9c3996f.jpg';
                var img1 = 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1504421183336&di=ac09e1dca05ab8c0aa52a8ed1719ca24&imgtype=0&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F140616%2F240472-1406160R03110.jpg';
                var img2 = 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1505012185&di=74d929737f7fb245bddbde2d595ccc04&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.hb.aicdn.com%2F60ef23c10e64aba53f750ece9272fb5717bfe0a8c443-1Id1OD_fw580';
                var arr = [img, img1, img2];
                $('#background').css({'background':'url('+arr[2]+')'});
                var i = 0;
                var length = arr.length - 1;
                var timer = 0;
                timer = setInterval(function () {
                    if (i < length) {
                        i++
                    } else {
                        i = 0;
                    }
                    $('#background').css({'background':'url('+arr[i]+')'});

                }, '3000');
            }
        }
    </script>
@stop

