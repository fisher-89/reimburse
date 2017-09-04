@extends('public.layout')

@section('title')
我的报销单
@endsection

@section('content')
<style>
    header>ul{background: #2291ab;}
    .nav-tabs>li.active>a,.nav-tabs>li.active>a:focus{background-color: #f2f2f2;}
    .tab-content{margin-top:15px;}
    .content .handle_list>.list{padding:5px;}
    .content .handle_list>.list .account{position:relative; overflow:hidden; padding:0}
    .content .handle_list>.list .btn{width:100%;border-color:#32b5c5;margin-bottom:5px;background-color:#fcfcfc;padding:5px;}
    .content .handle_list>.list .btn.local{border-color:#eea236;box-shadow:0 0 2px 0 #eea236;background-color:#fff;}
    .content .handle_list>.list .btn.reject{border-color:#d9534f;}
    .content .handle_list>.list .btn>.information{width:70%;padding-left:5px;}
    .content .handle_list>.list .btn>.information>a{display:block;border-bottom:1px solid #ccc;}
    .content .handle_list>.list .btn>.information>a:last-child{border-bottom: 0}
    .content .handle_list>.list .btn .date{width:30%;}
    .content .handle_list>.list .btn .date>p{margin:0;color:#fff;background:#d9534f;line-height:22px;width:44px;}
    .content .handle_list>.list .btn .name{width:70%}
    .content .handle_list>.list .btn .name p{line-height:22px;margin-bottom:0;color:#666;}
    .content .handle_list>.list .btn .name p>span{color:#666;font-weight:400;font-size:14px;}
    .content .handle_list>.list .btn .name p:first-child{font-size:16px;font-weight:700;color:#333;}
    .content .handle_list>.list .btn>.cost{width:40%}
    .content .handle_list>.list .btn>.cost p{line-height:44px;font-size:18px;;margin-bottom:0;}
    .content .handle_list>.list .btn .price {width:30%; text-align:center;height:44px;line-height:44px;}
</style>
<header class="">
    <ul class="nav nav-tabs" >
        <li class="active">
            <a data-toggle="tab" href="#notSubmit">
                未提交
            </a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#hasSubmit">
                处理中
            </a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#complete">
                已完成
            </a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#hasReject">
                已驳回
            </a>
        </li>
    </ul>
</header>

<div class="tab-content" id="reimbursement_list">
    <p class="text-center">载入中...</p>
</div>
@endsection

@section('js')
@parent
<script>
    sessionStorage.clear();//清楚创建时为提交session数据
    getReimbursementList();
    function getReimbursementList() {
        $.ajax({
            type: 'GET',
            url: "/get_reimbursement_list",
            data: {"timestamp": new Date().getTime()},
            dataType: "text",
            success: function (msg) {
                $("#reimbursement_list").html(msg);
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
</script>
@endsection

