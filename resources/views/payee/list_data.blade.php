<style>
    .payeelist .checkeds .info {
        width: 100%;
        padding: 8px;
        margin-bottom: 3px;
        padding-left: 15px;
        border-radius: 10px;
        color: #0d3625;
    }

    .payeelist .default {
        margin-left: 10px;
    }

    .payeelist .edit {
        margin-left: 30%;
    }

    .payeelist .delete {
        margin-left: 25px;
    }
</style>
@if(count($payee)>0)
@foreach($payee as $v)
    <div class="payeelist col-lg-12">
        <a class="checkeds" payee_id="{{$v['id']}}" payee_name="{{$v['bank_account_name']}}">
            <div class="info" style="border: 1px solid <?php echo $v['is_default'] == 1 ? '#cc2323' : '#a0a1a3'; ?>;">
                <span>{{$v['bank_account_name']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{$v['bank_other']}}</span>
                <div>{{$v['bank_account']}}</div>
            </div>
        </a>
        &nbsp;<span class="default text-success" urlid="{{$v['id']}}">设为默认</span>&nbsp;&nbsp;<a
                class="edit text-primary" href="{{asset(route('payee_create_or_edit',['id'=>$v['id']]))}}"
                style="">修改</a> <a class="delete text-danger" onclick="delPayee({{$v['id']}})">删除</a>
    </div>
    <hr style="border: 2px solid rgba(191, 190, 190, 0.59);margin-top:3px;">
@endforeach
@endif
<script>
    //删除收款人
    function delPayee(id) {
        id = parseInt(id);
        var url = '/payee_delete';
        if (confirm('你确定删除这条收款人信息？')) {
            $.ajax({
                type: 'get',
                url: url,
                data: {id: id},
                success: function (data) {
                    if (data == 'success') {
                        location.reload();
                    }
                }
            });
        }
    }


    //设为默认收款人信息
    $('.payeelist').find('.default').on('click', function () {
        var id = $(this).attr('urlid');
        var self = $(this);
        add_default(id, self)
    });

    function add_default(id, self) {
        id = parseInt(id);
        var url = '/payee_default';
        $.ajax({
            type: 'post',
            url: url,
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (msg) {
                if (msg == 'success') {
                    $('.payeelist a').each(function (k, v) {
                        $(v).find('.info').css('border-color', '#a0a1a3');
                    });
                    var now = self.parent('.payeelist').find('.info');
                    now.css('border-color', '#cc2323');
                }
            }
        });
    }
</script>
