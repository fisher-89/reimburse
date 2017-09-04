@extends('public.layout')

@section('title')
收款人资料
@endsection
@section('content')
<div class='new_report' id="payee_list">
    <p class="text-center">载入中...</p>
</div>
@endsection
@section('footer')

<div class='new_report'>
    <a class="btn btn-lg btn-success submit-btn" href="{{asset(route('payee_create_or_edit'))}}" style="width:100%;">添加收款人</a>
    <div class="clearfix"></div>
</div>
@endsection
@section('js')
@parent
<script>
    getPayeeList();

    function getPayeeList() {
        $.ajax({
            type: 'GET',
            url: "/get_payee_list_data",
            data: {"timestamp": new Date().getTime()},
            dataType: "text",
            success: function (msg) {
                $("#payee_list").html(msg);
<?php if (request()->checkable): ?>
                    bindCheckEvent();
<?php endif; ?>
            },
            error: function (err) {
                if (err.status === 422) {
                    var responses = JSON.parse(err.responseText);
                    for (var i in responses) {
                        alert(responses[i]);
                    }
                } else {
                    document.write(err.responseText);
                }
            }
        });
    }

//创建报销单选择收款人
    function bindCheckEvent() {
        //选择收款人
        $(".payeelist .checkeds").on("click", function () {
            var payee_id = $(this).attr('payee_id');
            var payee_name = $(this).attr('payee_name');
            var payee_data = new Object();
            payee_data.payee_id = payee_id;
            payee_data.payee_name = payee_name;
            sessionStorage.setItem('payee',JSON.stringify(payee_data));
            window.history.go(-1);
        });
    }

    //去除弹框的网址
    var wConfirm = window.confirm;
    window.confirm = function (message) {
        try {
            var iframe = document.createElement("IFRAME");
            iframe.style.display = "none";
            iframe.setAttribute("src", 'data:text/plain,');
            document.documentElement.appendChild(iframe);
            var alertFrame = window.frames[0];
            var iwindow = alertFrame.window;
            if (iwindow == undefined) {
                iwindow = alertFrame.contentWindow;
            }
            var realConfirm = iwindow.confirm(message);
            iframe.parentNode.removeChild(iframe);
            return realConfirm;
        } catch (exc) {
            return wConfirm(message);
        }
    };
</script>
@endsection
