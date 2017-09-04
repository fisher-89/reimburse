@extends('public.layout')

@section('title')
消费明细
@endsection

@section('content')
<div class='new_cost'>
    <form action="{{asset(url()->current())}}" method="post" id="form">
        <div>
            <p><i class="fa fa-calendar"></i> 日期</p>
            <input name="date" type="date" required value="{{date('Y-m-d')}}">
        </div>
        <div id="expense_type">
            <p><i class="fa fa-list-ul"></i> 类型</p>
            <img src="{{asset(Cache::get('expenseTypes')['0']['pic_path'])}}"> <span>{{Cache::get('expenseTypes')['0']['name']}}</span>
            <input name="type_id" type="hidden" value="0">
        </div>
        <div>
            <p><i class="fa fa-cny"></i> 金额</p>
            <input id="money_input" name="send_cost" type="number" limit="0,10" maxlength="10" required value="">
        </div>
        <div>
            <p><i class="fa fa-pencil"></i> 描述</p>
            <textarea name='description' placeholder="请描述消费内容，最多200字" limit="0,200"  maxlength="200" required style="border:1px solid #c5bfbf; width:70%;height:100px;"></textarea>
        </div>
        <input type="hidden" name="bill_num" value="0">

    </form>
    <form action="{{asset(route('add_bill'))}}" id="billUploader" method="post" enctype="multipart/form-data"target="upload">
        <div>
            <p><i class="fa fa-tag"></i> 发票</p>
            <label>
                <a class=" btn btn-xs btn-info"><i class="fa fa-plus"></i></a>
                <input type="file" class="hidden" name="bill" id="addBill">
            </label>
            <iframe class="hidden" name="upload"></iframe>
        </div>
    </form>
    <div class="bill_block"></div>
    <div class="choose_type">
        <div>
            @foreach(Cache::get('expenseTypes') as $k=>$type)
            @if($k != 0)
            <a type_id="{{$k}}" content="{{$type['name']}}" pic_path="{{asset($type['pic_path'])}}">
                <img src="{{asset($type['pic_path'])}}">
                {{$type['name']}}
            </a>
            @endif
            @endforeach
            <a type_id="0" content="{{Cache::get('expenseTypes')['0']['name']}}" pic_path="{{asset(Cache::get('expenseTypes')['0']['pic_path'])}}">
                <img src="{{asset(Cache::get('expenseTypes')['0']['pic_path'])}}">
                {{Cache::get('expenseTypes')['0']['name']}}
            </a>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="money_keyboard" id="money_keyboard"></div>
</div>
@endsection

@section('footer')
<div class='new_cost'>
    <button class="btn btn-lg btn-danger" id="delete" onclick="delete_expense()"><i class="fa fa-trash-o"></i> 删除</button>
    <button class="btn btn-lg btn-success submit-btn"  id="reset_bill" onclick="reset()">再记一笔</button>
    <button class="btn btn-lg btn-warning submit-btn" onclick="back()"><i class="fa fa-save"></i> 确定</button>
    <div class="clearfix"></div>
</div>
@endsection

@section('js')
@parent
<script src="{{asset('js/reimburse/expense.js')}}"></script>
<script>
    //获取初始化消费类型
    var type_src = "{{asset(Cache::get('expenseTypes')['0']['pic_path'])}}";
    var type_name = "{{Cache::get('expenseTypes')['0']['name']}}";

//编辑数据回填
        var sessionId = '<?php echo (isset(request()->sessionId) ? request()->sessionId : 'is_null'); ?>';
        var id = "<?php echo request()->id; ?>";
        if (id == 0) {
            //新增消费明细数据编辑处理
            getAddExpense_update();
        } else {
            //编辑消费明细数据编辑处理
//            console.log(JSON.parse(sessionStorage.getItem('expense')));
            getUpdateExpense_update();

        }

        function getAddExpense_update() {
            if (sessionId != 'is_null') {//编辑处理
                $('#reset_bill').hide();//编辑时隐藏再记一笔按钮
                var session_data = JSON.parse(expense.getSessionStorage());
                $.each(session_data, function (k, v) {
                    if (k == sessionId) {
//                    console.log(v)
                        $('#form input[name="date"]').val(v.date);
                        $('#form #expense_type input[name="type_id"]').val(v.type_id);//类型id
                        $('#form #expense_type span').text(v.type_name);//类型名
                        $("#form #expense_type img").attr('src', v.type_img);//类型图片
                        $('#form input[name="send_cost"]').val(v.send_cost);//金额
                        $('#form textarea[name="description"]').val(v.description);//描述
                        $('#form input[name="bill_num"]').val(v.bill_num);//发票数
                        /*-----------发票start----------*/
                        var bill_hiden_url_arr = '';
                        var bill_img_arr = '';
                        if (v.bill.length > 0) {
                            $.each(v.bill, function (key, val) {
                                bill_hiden_url_arr += '<input type="hidden" name="bills[]" value="' + val + '">';
                                bill_img_arr += '<img src="/' + val + '" alt="' + val + '" title="双击进行删除"  ondblclick="pc_dblclick_delete(this);" onTouchstart="phone_touch_delete(this)">';
                            });
                        }
                        $('#form').append(bill_hiden_url_arr);
                        $(".bill_block").append(bill_img_arr);
                        /*-----------发票end----------*/
                        var session_id = '<input type="hidden" name="session_id" value="' + k + '">';//新增（编辑session的键）
                        $('#form').append(session_id);
                        checkForm();//必填处理
                    }
                });
            } else {
                $('#delete').hide();//新增去掉删除按钮
            }
        }

        function getUpdateExpense_update() {
            $('#reset_bill').hide();//编辑时隐藏再记一笔按钮
            var session_data = JSON.parse(expense.getSessionStorage());
            $.each(session_data, function (k, v) {
                if (k == sessionId) {
//                    console.log(v)
                    $('#form input[name="date"]').val(v.date);
                    $('#form #expense_type input[name="type_id"]').val(v.type_id);//类型id
                    $('#form #expense_type span').text(v.type_name);//类型名
                    $("#form #expense_type img").attr('src', v.type_img);//类型图片
                    $('#form input[name="send_cost"]').val(v.send_cost);//金额
                    $('#form textarea[name="description"]').val(v.description);//描述
                    $('#form input[name="bill_num"]').val(v.bill_num);//发票数
                    /*-----------发票start----------*/
                    var bill_hiden_url_arr = '';
                    var bill_img_arr = '';
                    if (v.bill.length > 0) {
                        $.each(v.bill, function (key, val) {
                            bill_hiden_url_arr += '<input type="hidden" name="bills[]" value="' + val + '">';
                            bill_img_arr += '<img src="/' + val + '" alt="' + val + '" title="双击进行删除"  ondblclick="pc_dblclick_delete(this);"  onTouchstart="phone_touch_delete(this)">';
                        });
                    }
                    $('#form').append(bill_hiden_url_arr);
                    $(".bill_block").append(bill_img_arr);
                    /*-----------发票end----------*/
                    var session_id = '<input type="hidden" name="session_id" value="' + k + '">';//编辑（编辑session的键）
                    var id = '<input type="hidden" name="id" value="' + v.id + '"/>';//编辑id
                    $('#form').append(session_id);
                    $('#form').append(id);
                    checkForm();//必填处理
                }
            });
        }


        //删除明细单
        function delete_expense() {
            var session_data = JSON.parse(expense.getSessionStorage());
            $.each(session_data, function (k, v) {
                if (k == sessionId) {
                    session_data.splice(k, 1);
                }
            });
            sessionStorage.setItem(expense.session_key, JSON.stringify(session_data));
            history.go(-1);
        }


/*------------------------上传发票start----------------------------------*/
        var ImgFileGet_class = function (msg) {
            this.fileSelector = msg.fileSelector;  //file-input的选择器
            this.preViewImgSelector = msg.preViewImgSelector;  //图片预览选择器
            this.max_size = msg.max_size || false;  //图片最大大小，不设为无限度
            this.ajaxInterace = msg.ajaxInterace; //ajax上传图片插件

            var _this = this;

            $(this.fileSelector).change(function () {
                var reader = new FileReader();
                var file = this.files[0];

                reader.onload = function (e) {
                    var com_rate = 1;
                    if ((_this.max_size !== false) && (file.size > _this.max_size)) {

                        com_rate = _this.max_size / file.size;

                    }

                    _this.compressImg(e.target.result, com_rate, function (src_data) {
                        _this.preViewImgSelector && $(_this.preViewImgSelector).attr('src', src_data);

                        if (_this.ajaxInterace) {
                            _this.ajaxInterace(src_data);
                        }
                    });

                };
                reader.readAsDataURL(file);
            });

            this.compressImg = function (imgData, com_rate, onCompress) {
                if (!imgData)
                    return;
                onCompress = onCompress || function () {
                };
                com_rate = com_rate || 1;//压缩比率默认为1

                var img = new Image();
                img.onload = function () {

                    if (com_rate != 1) {//按最大高度等比缩放
                        var rate = Math.sqrt(com_rate);
                        img.width *= rate;
                        img.height *= rate;


                        var canvas = document.createElement('canvas');
                        var ctx = canvas.getContext("2d");
                        canvas.width = img.width;
                        canvas.height = img.height;
                        //ctx.drawImage(img, 0, 0);
                        ctx.clearRect(0, 0, canvas.width, canvas.height); // canvas清屏
                        //重置canvans宽高 canvas.width = img.width; canvas.height = img.height;
                        ctx.drawImage(img, 0, 0, img.width, img.height); // 将图像绘制到canvas上
                        onCompress(canvas.toDataURL("image/jpeg"));//必须等压缩完才读取canvas值，否则canvas内容是黑帆布
                    } else {
                        onCompress(imgData);
                    }
                };

                //记住必须先绑定事件，才能设置src属性，否则img没内容可以画到canvas
                img.src = imgData;
            };
        };


        var ImgFileGet = new ImgFileGet_class({
            fileSelector: "#addBill", //fileInput选择器
            preViewImgSelector: "#pre-img", //预览图片
            max_size: 400 * 1024,
            ajaxInterace: function (src_data) {
                $(".waiting").show();
                var cont_index = src_data.indexOf("base64,") + 7;  //base64编码的图片，类型为jpeg

                var send_msg = {};
                send_msg.content = src_data.substring(cont_index);
                send_msg._token = '{{csrf_token()}}';
                var url = "{{asset(route('add_bill'))}}";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: send_msg,
                    dataType: "json",
                    success: function (msg) {
                        if (msg['status'] === 1) {
                            var file = msg['file'];
                            var save = msg['save'];
                            var img = '<img src="' + file + '" alt="' + save + '" title="双击进行删除" ondblclick="pc_dblclick_delete(this);"  onTouchstart="phone_touch_delete(this)">';
                            var input = '<input type="hidden" name="bills[]" value="' + save + '">';
                            $(".bill_block").append(img);
                            $("#form").append(input);
                            $("input[name=bill_num]").val(parseInt($("input[name=bill_num]").val()) + 1);
                            $(".waiting").hide();
                        }
                    },
                    error: function (err) {
                        alert(err.responseText);
                    }
                });
            }
        });
/*------------------------上传发票end----------------------------------*/
</script>
@endsection
