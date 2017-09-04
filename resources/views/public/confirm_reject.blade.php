
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModals" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#32b5c5">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">请输入驳回原因</h4>
            </div>
            <div class="modal-body" style="font-size:24px;">
                <textarea  name="reject_remarks" id ="reject_remarks" style="width:100%;height:100px;" placeholder="请输入驳回原因"></textarea>
            </div>
            <div class="modal-footer" style="margin-top:0;">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success btn-lg disabled" id="confirm_reject" onclick="confirm_reject({{$info['id']}})">确认</button>
            </div>
        </div>
    </div>
</div>
<script>
     //驳回原因处理
    $('#reject_remarks').on('keyup',checkRejectRemarks);
   
    function checkRejectRemarks() {
        var reject_remarks = $('#reject_remarks').val();
        if ($.trim(reject_remarks).length == 0) {
            $('#confirm_reject').addClass('disabled');
        }else{
            $('#confirm_reject').removeClass('disabled');
        }
    }
    //驳回
    function confirm_reject(id) {
        $(".waiting").show();
        var url = '/pending/reject';
        var reject_remarks = $("#reject_remarks").val();
        $.ajax({
            type: 'post',
            url: url,
            data: {id:id,reject_remarks:reject_remarks},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (info) {
                 $(".waiting").hide();
                if (info === 'success') {
                    window.history.back(-2);
                }else if(info ==='dingdingError'){
                    //驳回时无法获取钉钉号
                    window.history.back(-2);
                }
            },
            error:function(msg){
                if(msg.status === 422){
                   var response = JSON.parse(msg.responseText);
                   for(var i in response){
                       alert(response[i]);
                   }
                }
            }
        });
    }
</script>