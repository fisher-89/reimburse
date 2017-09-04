@extends('public.layout')

@section('title')
待审批报销单
@endsection
@section('header_right')
    {'show':true,'control':true,'text':'刷新','onSuccess':function(){location.reload();}}
@endsection

@section('content')
<div class='handle_list'>
    <p class="text-center">载入中...</p>
</div>
@endsection

@section('js')
@parent
<script>
    getReimbursementList();
    function getReimbursementList() {
        $.ajax({
            type: 'GET',
            url: "/get_pending_list",
            data: {"timestamp": new Date().getTime()},
            dataType: "text",
            success: function (msg) {
                $(".handle_list").html(msg);
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