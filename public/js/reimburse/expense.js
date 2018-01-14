/*
 * 消费明细
 */
//金额处理
$("#money_input").on("blur", function () {
    var money = $("#money_input").val();
    if (money.length == 0) {
        $("#money_input").val('');
        return;
    }
    var exp = /^(\-?[1-9]\d{0,6}|0?)(\.\d*)?$/;
    if (!exp.exec(money)) {
        alert("你输入的金额格式错误或金额数字过长！仅支持最大7位数金额");
        $("#money_input").val('');
    } else {
        var money_val;
        var i = money.indexOf('.'); //如果settime包含":" 则 i 返回":"在settime里面的位置，否则返回-1
        if (i == -1) {
            money_val = money + '.00';
        } else if (i > 0) {
            a = money.substr(i + 1, i + 3);
            if (a.length < 2) {
                money_val = money.substr(0, i + 2) + '0';
            } else {
                money_val = money.substr(0, i + 3);
            }
        } else if (i == 0) {
            money_val = '0' + money;
        }
        $("#money_input").val(money_val);
    }
});

//再记一笔
function reset() {

    var data = expense.save();
    if (data == 'success') {
        $("#expense_type span").html(type_name);
        $("#expense_type img").prop("src", type_src);
        $("input[name=type_id]").val("0");
        $('.bill_block').html('');
        $('#form').find('input[name="bill_num"]').val(0);
        $('#form').find('input[name="bills[]"]').remove();
        $('#form')[0].reset();
    }
}

//确定
function back() {
    var data = expense.save();
    if (data == 'success') {
        $('#form')[0].reset();
        history.go(-1);
    }
}

var expense = {
    //session 键
    session_key: 'expense',
    //消费明细 再记一笔和确定处理
    save: function () {
        var form_data = this.getFormData();//表单数据
        this.addSessionStorage(form_data);//存入session
        return 'success';
    },
    //获取表单数据
    getFormData: function () {
        var session_id = $('#form input[name="session_id"]').val();
        var id = $('#form input[name="id"]').val();
        var bills = $('#form input[name="bills[]"]');
        var bill_url_arr = [];
        $.each(bills, function () {
            bill_url_arr.push($(this).val());
        });
        var data = new Object;
        data['date'] = $('#form input[ name="date"]').val();//时间
        data['type_id'] = $('#form #expense_type input[name="type_id"]').val();//类型id
        data['type_name'] = $('#form #expense_type span').text();//类型名
        data['send_cost'] = $('#form input[name="send_cost"]').val();//金额
        data['description'] = $('#form textarea[name="description"]').val();//描述
        data['type_img'] = $("#form #expense_type img").attr('src');//类型图片
        data['bill'] = bill_url_arr;
        data['bill_num'] = $('#form input[name="bill_num"]').val();//发票数量
        if (session_id != undefined) {//编辑session的键数据
            data['session_id'] = session_id;
        }
        if (id != 'undefined' && id != undefined) {//编辑的id
            data['id'] = id;
        }
        return data;
    },
    //得到sessionStorage的数据
    getSessionStorage: function () {
        return sessionStorage.getItem(this.session_key);
    },
    //添加到session
    addSessionStorage: function (form_data) {
        var session_data = this.getSessionStorage();//获取session数据
        if (session_data == null) {//新增处理
            var form_arr = new Array();
            form_arr.push(form_data);
        } else {//编辑处理
            var form_arr = JSON.parse(session_data);
            //判断对象session_id是否含有这个键
            if (form_data.hasOwnProperty('session_id')) {
                //编辑修改数据
                $.each(form_arr, function (k, v) {
                    if (k == form_data.session_id) {
                        form_arr[k] = form_data;
                    }
                });
            } else {
                //新增追加明细
                form_arr.push(form_data);
            }
        }
        sessionStorage.setItem(this.session_key, JSON.stringify(form_arr));
    }
};

//选择消费类型
$("#expense_type img").on("click", function () {
    $(".new_cost>.choose_type").show();
    $(".new_cost>.choose_type a").click(function () {
        $("#expense_type span").html($(this).attr("content"));
        $("#expense_type input").val($(this).attr("type_id"));
        $("#expense_type img").attr("src", $(this).attr("pic_path"));
    });
    $(".new_cost>.choose_type").click(function () {
        $(this).hide();
    });
});
/*---------------------删除发票start-----------------------------*/

//pc双加进行删除
function pc_dblclick_delete(self) {
    delete_bill(self);
}

//手机长按屏幕进行删除
function phone_touch_delete(self) {
    var j = 0;
    var timer = setInterval(function () {
        j += 10;
        if (j >= 10) {
            delete_bill(self);
            clearInterval(timer);
            j = 0;
        }
    }, '1200');
    self.addEventListener('touchend', function (event) {
        clearInterval(timer);
        this.removeEventListener('touchend', arguments.callee);
    }, false);
}

function delete_bill(self) {
    if (confirm('确认删除发票？')) {
        var imgUrl = $(self).attr('alt');
        $(self).remove();
        var bills = $('#form input[name="bills[]"]');
        $.each(bills, function () {
            if ($(this).val() == imgUrl) {
                $(this).remove();
                $("#form input[name=bill_num]").val(parseInt($("#form input[name=bill_num]").val()) - 1);
            }
        });
    }
}

/*---------------------删除发票end----------------------------*/