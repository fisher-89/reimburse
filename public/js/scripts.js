
(function () {
    //调整底部按钮
    scroll();
    function scroll() {
        var $footer = document.getElementById('footer');
        var $content = document.getElementById('content');
        var bottom = $footer.offsetHeight;
        $content.style.marginBottom = bottom + "px";
    }
    //表单验证
    checkForm();

    $("input,textarea,select").on("focus", function () {
        $(this).on("keyup", function () {
            checkForm();
        });
        $(this).on("blur", function () {
            checkForm();
        });
    });
    // $("#form").on("submit", function () {
    //     var check = checkForm();
    //     if (check) {
    //         saveByAjax();
    //     }
    //     return false;
    // });

    
})(jQuery);

function checkForm() {
    if (checkIfRequired() && checkLength() && checkType()) {
        $(".submit-btn").removeClass("disabled");
        return true;
    } else if (!$(".submit-btn").hasClass("disabled")) {
        $(".submit-btn").addClass("disabled");
    }
    return false;
}
function checkIfRequired() {
    var result = true;
    $("#form input,#form textarea").each(function () {
        var required = $(this).attr("required");
        var value = $(this).val();
        if (required === "required" && value.length === 0) {
            return result = false;
        }
        return true;
    });
    return result;
}
function checkLength() {
    var result = true;
    $("#form input,#form textarea").each(function () {
        var limit = $(this).attr("limit");
        if (limit !== undefined) {
            var index = limit.indexOf(",");
            var min = limit.substring(0, index);
            var max = limit.substring(index + 1);
            var valueLength = $(this).val().length;
            if (valueLength < min || valueLength > max) {
                return result = false;
            }
        }
    });
    return result;
}
function checkType() {
    var result = true;
    $("#form input,#form textarea").each(function () {
        var type = $(this).attr("data_type");
        var value = $(this).val();
        if (type === "num") {
            var parent = /^[1-9]\d*$/;
            if (!parent.exec(value)) {
                return result = false;
            }
        } else if (type === "zh_cn") {

        }
    });
    return result;
}

// function saveByAjax() {
//     $(".waiting").show();
//     var url = $("#form").attr("action");
//     var data = $("#form").serialize();
//     var type = $("#form").attr('method');
//     $.ajax({
//         type: type,
//         url: url,
//         data: data,
//         success: function (info) {
//             if (info === "success") {
//                 window.history.go(-1);
//             } else if (info === 'double_back') {
//                 window.history.go(-2);
//             } else {
//                 $(".waiting").hide();
//                 alert("保存失败,返回值：" + info);
//             }
//         },
//         error: function (err) {
//             $(".waiting").hide();
//             if (err.status === 422) {
//                 var responses = JSON.parse(err.responseText);
//                 for (var i in responses) {
//                     alert(responses[i]);
//                 }
//
//             } else {
//                 document.write(err.responseText);
//             }
//         }
//     });
// }