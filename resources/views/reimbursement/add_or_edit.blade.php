@extends('public.layout')

@section('title')
    @if(request()->id)编辑报销单@else创建报销单@endif
@endsection

@section('header_right')
    @if(request()->id && $info['status_id'] != -1)
        {'show':true,'control':true,'text':'删除','onSuccess':function(){delNotSend({{request()->id}});}}
    @else
        {'show':true,'control':true,'text':'刷新','onSuccess':function(){location.reload();}}
    @endif

@endsection


@section('content')
    <!--审批人没有提示start-->
    @if(count($errors->all())>0)
        @foreach($errors->all() as $k=>$v)
            <p id="errors" class="text-center" style="color:red;">{{$v}}</p>
        @endforeach
    @endif
    <!--审批人没有提示end-->
    <div class='new_report'>
        <form action="{{asset(url()->current())}}" method="post" id="form">
            <div>
                <p><i class="fa fa-pencil"></i> 描述(<span class="text-danger">必填</span>)</p>
                <input name="description" placeholder="20字以内" limit="0,20" maxlength="20"
                       value="{{$info['description'] or ''}}" required>
            </div>
            <div>
                <p><i class="fa fa-file"></i> 备注(选填)</p>
                <textarea name="remark" style="border:1px solid #c5bfbf; width:100%;height:75px;" placeholder="150字以内"
                          limit="0,150" maxlength="150">{{$info['remark'] or ''}}</textarea>
            </div>
            <div>
                <p><i class="fa fa-user"></i> 收款人(<span class="text-danger">必填</span>)</p>
                <a href="{{asset(route('payee_list',['checkable'=>true]))}}" id="edit_payee">
                    <input type="text" name="payee_name" readonly placeholder="设置收款人信息" value="加载中...">
                    <input type="hidden" name="payee_id" value="">
                </a>
            </div>
            <div>
                <p><i class="fa fa-pencil"></i> 资金归属</p>
                <select name="reim_department_id" required style="width:180px; border: 1px solid #c5bfbf;">
                    @foreach($info['reim_department'] as $v)
                        <option value="{{$v->id}}"
                                @if($v->id == $info['reim_department_id']) selected @endif>{{$v->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <a href="{{asset(route('add_expense'))}}" class="btn btn-lg btn-warning add_cost" id="add_expense"><i
                            class="fa fa-plus"></i> 添加消费明细</a>
            </div>
            @if($info['approver'])
                <div id="approver_arr">
                    <p>审批人(<span class="text-danger">必填</span>)</p>
                    <a href="{{asset(route('add_approver_user'))}}">
                        <input type="text" readonly name="approver_name" placeholder="请点击选择审批人" limit="0,5" value=""/>
                    </a>
                    <input type="hidden" name="approver_staff_sn" value="" limit="0,30">
                </div>
            @endif
            <div class="cost_list">
                <p class="text-center">正在加载...</p>
            </div>

        </form>
    </div>
@endsection

@section('footer')
    <div class='new_report'>
        <?php if (!isset($info['status_id']) || $info['status_id'] != -1): ?>
        <button class="btn btn-lg btn-success submit-btn disabled" id="save" onclick="commit.save();"> 保存</button>
        <?php endif; ?>
        <button class="btn btn-lg btn-warning submit-btn disabled" id="send" onclick="commit.send();">提交送审</button>
        <div class="clearfix"></div>
    </div>
@endsection

@section('js')
    @parent
    <script>

        //定时获取csrftoken
        function setIntervalGetCsrfToken() {
            $.ajax({
                type: 'get',
                url: '/getCsrfToken',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (msg) {
                    console.log(msg);
                }
            })
        }

        //    if (window.history.state == null) {
        //        history.replaceState(1, "测试");
        //    } else {
        //        history.replaceState(window.history.state + 1, "测试");
        //    }

        if ($('#save').length == 0) {
            $('#send').css('width', '100%');
        }


        $(function () {
//        sessionStorage.clear()
//            console.log(JSON.parse(sessionStorage.getItem('payee')));
//            console.log(JSON.parse(sessionStorage.getItem('approver')));
//            console.log(JSON.parse(sessionStorage.getItem('expense')));
            getPayeeApproverExpenseInfo();//获取收款人、审批人、消费明细数据
            checkIfCouldSend();//初始提交送审按钮
            setInterval(setIntervalGetCsrfToken, 3600000);//续期csrftoken
        });


        var id = '<?php echo isset(request()->id) ? request()->id : 0; ?>';//编辑的id
        //新增、编辑获取收款人、审批人、消费明细数据
        function getPayeeApproverExpenseInfo() {
            if (id == 0) {//新增获取数据
//            sessionStorage.clear()
                getAddPayeeApproverExpense();
            } else {//编辑获取数据
//            sessionStorage.clear()
                getUpdatePayeeApproverExpense();
            }
        }

        /*-------------------------新增处理 （收款人、审批人、消费明细）start-------------------------*/
        //获取新增数据
        function getAddPayeeApproverExpense() {
//        console.log(JSON.parse(sessionStorage.getItem('expense')))
            getAddPayeeList();//新增获取收款人数据
            getAddApproverList();//新增获取审批人数据
            getAddExpensesList();//新增获取消费列表数据
        }

        //新增获取收款人数据
        function getAddPayeeList() {
            var payee_data = JSON.parse(sessionStorage.getItem('payee'));
            addGetPayeeUser(payee_data);
        }

        function addGetPayeeUser(payee_data) {
            if (payee_data == null) {
                //获取默认收款人数据
                $.ajax({
                    type: 'post',
                    url: '/getDefaultPayeeUser',
                    asyns: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (msg) {
                        $('#form input[name="payee_name"]').val(msg.payee_name);
                        $('#form input[name="payee_id"]').val(msg.payee_id);
                        checkIfCouldSend();//初始提交送审按钮
                    }
                });
            } else {
                //获取session的收款人数据
                $('#form input[name="payee_name"]').val(payee_data.payee_name);
                $('#form input[name="payee_id"]').val(payee_data.payee_id);
            }
        }

        //新增获取审批人数据
        function getAddApproverList() {
            var approver_data = JSON.parse(sessionStorage.getItem('approver'));
            if (approver_data != null) {
                $('#form input[name="approver_name"]').val(approver_data.approver_name);
                $('#form input[name="approver_staff_sn"]').val(approver_data.approver_staff_sn);
            }
        }

        /**
         *新增、编辑获取消费明细列表数据
         * @returns {undefined}
         */
        function getAddExpensesList() {
            var expense_data = JSON.parse(sessionStorage.getItem('expense'));
            var str = '';
            if (expense_data != null && expense_data.length > 0) {
                var list = '';
                var total = 0;
                $.each(expense_data, function (k, v) {
                    total += parseFloat(v.send_cost);
                    var description = (v.description.length > 5) ? v.description.substring(0, 5) + '..' : v.description;
                    list += '<a href="/add_expense/' + id + '/' + k + '" class="btn btn-lg edit_expense">' +
                        '<div class="logo text-center"><img src="' + v.type_img + '"></div>' +
                        '<div class="info"><h4 title="' + v.description + '">' + description + '</h4><p title="' + v.date + '">' + v.date + '</p></div>' +
                        '<div class="cost text-right">￥' + v.send_cost + '</div>' +
                        '<div class="bill_num">' + v.bill_num + '</div>' +
                        '</a>';
                });
                str = '<div class="title">';
                str += '<div class="count">共 <span>' + expense_data.length + '</span> 条消费</div>';
                str += '<div class="total">总计：<span>￥' + total + '</span></div>';
                str += '<div class="clearfix"></div>';
                str += '</div>';
                str += '<div class="list">';
                str += list;
                str += '</div>';
                str += '<input type="hidden" name="send_cost" value="' + total + '">';
            }
            $('#form .cost_list').html(str);
        }

        /*-------------------------新增处理 （收款人、审批人、消费明细）end-------------------------*/

        /*-------------------------编辑处理 （收款人、审批人、消费明细）start-------------------------*/

        //编辑获取数据
        function getUpdatePayeeApproverExpense() {
            $.ajax({
                type: 'post',
                url: '/get_reimburse_payee_approver_expense',
                data: {id: id},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (msg) {
//                console.log(msg);
                    if (msg) {
                        $('#form').append('<input type="hidden" name="id" value="' + msg.id + '"/>');//编辑id
                        getUpdatePayeeList(msg);//编辑获取收款人数据
                        getUpdateApproverList(msg);//编辑获取审批人数据
                        getUpdateExpenseList(msg);//编辑获取消费明细数据
                        checkIfCouldSend();//初始提交送审按钮
                    }
                }
            });
        }

        //编辑获取收款人数据
        function getUpdatePayeeList(msg) {
            var payee_data = JSON.parse(sessionStorage.getItem('payee'));
            if (payee_data == null) {
                $('#form input[name="payee_name"]').val(msg.payee_name);
                $('#form input[name="payee_id"]').val(msg.payee_id);
            } else {
                $('#form input[name="payee_name"]').val(payee_data.payee_name);
                $('#form input[name="payee_id"]').val(payee_data.payee_id);
            }

        }

        //编辑获取审批人数据
        function getUpdateApproverList(msg) {
            var approver_data = JSON.parse(sessionStorage.getItem('approver'));
            if (approver_data == null) {
                $('#form input[name="approver_name"]').val(msg.approver_name);
                $('#form input[name="approver_staff_sn"]').val(msg.approver_staff_sn);
            } else {
                $('#form input[name="approver_name"]').val(approver_data.approver_name);
                $('#form input[name="approver_staff_sn"]').val(approver_data.approver_staff_sn);
            }
        }

        //编辑获取消费明细数据
        function getUpdateExpenseList(msg) {
//        sessionStorage.clear();
            var expense_data = JSON.parse(sessionStorage.getItem('expense'));
            if (expense_data == null) {
                expenseToSession(msg);//编辑的消费明细数据存入session
            }
            getAddExpensesList();//编辑数据展示

        }

        function expenseToSession(msg) {
            if (msg.expenses.length > 0) {
                var expense_array = new Array();
                $.each(msg.expenses, function (k, v) {
                    var data = new Object();
                    var bills_arr = [];//发票
                    if (v.bills.length > 0) {
                        $.each(v.bills, function (key, val) {
                            bills_arr.push(val.pic_path);
                        });
                    }
                    data.id = v.id;
                    data.date = v.date;
                    data.type_id = v.type_id;
                    data.type_name = v.type.name;
                    data.type_img = '/' + v.type.pic_path;
                    data.send_cost = v.send_cost;
                    data.description = v.description;
                    data.bill = bills_arr;
                    data.bill_num = bills_arr.length;
                    expense_array.push(data);
                });
                sessionStorage.setItem('expense', JSON.stringify(expense_array));
            }
        }

        /*-------------------------编辑处理 （收款人、审批人、消费明细）end------------------------*/


        /*--------------------------------------保存、提交送审处理start-----------------------------------*/
        var commit = {
            //保存
            save: function () {
                this.ajax(this.getData());
            },
            //提交送审
            send: function () {
                var data = this.getData();
                data = data + '&send=send';
                this.ajax(data);
            },
            //获取报销单数据和消费明细数据
            getData: function () {
                var data = $('#form').serialize();
                var expense = sessionStorage.getItem('expense');//消费明细
                expense = (expense == null) ? '' : expense;
                var result = data + '&expense=' + expense;
                return result;
            },
            ajax: function (data) {
                var status_id = {{$info['status_id'] or 0}};//当前报销单状态
                $(".waiting").show();
                var url = $("#form").attr("action");
                var type = $("#form").attr('method');
                $.ajax({
                    type: type,
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (info) {
                        if (info == "success") {
                            sessionStorage.clear();
                            if (status_id == -1) {
                                window.history.go(-2);
                            } else {
                                window.history.go(-1);
                            }
                        } else if (info == 'dingdingError') {
                            sessionStorage.clear();
                            alert('提交成功（无法获取审批人钉钉号，审批人可能没收到消息提示。处理步骤：1.进入个人中心退出系统后，重新登录。2.把提交的报销单撤回，重新提交。）');
                            window.history.go(-1);
                        } else {
                            $(".waiting").hide();
                            alert("保存失败");
                        }
                    },
                    error: function (err) {
                        $(".waiting").hide();
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
        };

        /*--------------------------------------保存、提交送审处理end-----------------------------------*/


        //编辑时删除未提交单
        function delNotSend(id) {
            if (confirm('确认删除？')) {
                var url = '/deleteReim';
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

        /**
         * 处理提交送审按钮权限
         * @returns {undefined}
         */
        function checkIfCouldSend() {
            if ($('input[name="description"]').length > 0 && $(".list>a").length > 0 && $("input[name=payee_id]").val() != '' && $("input[name='approver_staff_sn']").val() != '') {
                $("#send").removeAttr("disabled");
                $("#send").addClass("submit-btn");
            } else {
                $("#send").removeClass("submit-btn");
                $("#send").attr("disabled", 'disabled');
            }
        }

        //去除alert的网址
        var wAlert = window.alert;
        window.alert = function (message) {
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
                var realAlert = iwindow.alert(message);
                iframe.parentNode.removeChild(iframe);
                return realAlert;
            } catch (exc) {
                return wAlert(message);
            }
        };

    </script>
@endsection