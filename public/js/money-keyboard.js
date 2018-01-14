$(function () {

    var keyboardDom = '<div class="shadow"></div>' +
            '<div class="monitor">' +
            '<span id="monitor">0</span>' +
            '<a class="btn btn-success" id="confirm_money">确认</a>' +
            '<div class="clearfix"></div>' +
            '</div>' +
            '<div class="keyboard" id="keyboard">' +
            '<a class="btn btn-default number">1</a>' +
            '<a class="btn btn-default number">2</a>' +
            '<a class="btn btn-default number">3</a>' +
            '<a class="btn btn-default" id="delete"><i class="fa fa-arrow-circle-left"></i></a>' +
            '<a class="btn btn-default number">4</a>' +
            '<a class="btn btn-default number">5</a>' +
            '<a class="btn btn-default number">6</a>' +
            '<a class="btn btn-default" id="plus"><i class="fa fa-plus"></i></a>' +
            '<a class="btn btn-default number">7</a>' +
            '<a class="btn btn-default number">8</a>' +
            '<a class="btn btn-default number">9</a>' +
            '<a class="btn btn-default" id="minus"><i class="fa fa-minus"></i></a>' +
            '<a class="btn btn-default" id="clear">c</a>' +
            '<a class="btn btn-default number">0</a>' +
            '<a class="btn btn-default" id="point">.</a>' +
            '<a class="btn btn-default" id="multiply"><i class="fa fa-times"></i></a>' +
            '<div class="clearfix"></div>' +
            '</div>';
    $("#money_keyboard").html(keyboardDom);

    var show = document.getElementById("money_board");
    var input = document.getElementById("money_input");
    var keyboard = document.getElementById("keyboard");
    var btn = new Object();
    btn.all = keyboard.getElementsByTagName("a");
    btn.number = keyboard.getElementsByClassName("number");
    btn.plus = document.getElementById("plus");
    btn.minus = document.getElementById("minus");
    btn.multiply = document.getElementById("multiply");
    btn.clear = document.getElementById("clear");
    btn.point = document.getElementById("point");
    btn.back = document.getElementById("delete");
    var monitor = document.getElementById("monitor");
    var confirm = document.getElementById("confirm_money");
    var result = 0;
    var model = "none";
    var clear = false;


    show.onclick = function () {
        $("#money_keyboard").show();
        if (show.innerHTML.length == 0) {
            monitor.innerHTML = 0.00;
        } else {
            monitor.innerHTML = show.innerHTML;
        }
    };
    $(".shadow").click(function () {
        $("#money_keyboard").hide();
    });

    for (var i = 0; i < btn.number.length; i++) {
        btn.number[i].onclick = addNumber;
    }
    btn.clear.onclick = resetAll;
    btn.point.onclick = addPoint;
    btn.back.ontouchstart = deleteOne;
    btn.back.ontouchend = deleteClear;
    btn.plus.onclick = plus;
    btn.minus.onclick = minus;
    btn.multiply.onclick = multiply;
    confirm.onclick = confirmMoney;

    function addNumber() {
        var value = this.text;
        var valueOrigin = monitor.innerHTML;
        var preg = /\d+\.\d\d/;
        if (clear) {
            monitor.innerHTML = value;
            clear = false;
        } else if (valueOrigin === "0" || valueOrigin === "0.0" || valueOrigin === "0.00") {
            monitor.innerHTML = value;
        } else if (!preg.test(valueOrigin)) {
            monitor.innerHTML = valueOrigin + value;
        }
    }
    function resetAll() {
        monitor.innerHTML = "0";
        clear = false;
        model = "none";
        result = 0;
        confirm.innerHTML = "确认";
    }
    function addPoint() {
        var valueOrigin = monitor.innerHTML;
        var preg = /\./;
        if (clear) {
            monitor.innerHTML = "0.";
            clear = false;
        } else if (!preg.test(valueOrigin)) {
            monitor.innerHTML = valueOrigin + '.';
        }
    }
    function deleteOnes() {
        var valueOrigin = monitor.innerHTML;
        if (valueOrigin.length === 1 || clear) {
            monitor.innerHTML = 0;
            clear = false;
            clearInterval(decrement);
        } else if (valueOrigin.length > 1) {
            monitor.innerHTML = valueOrigin.substring(0, valueOrigin.length - 1);
        }
    }

    var decrement,stopTimout;
    function deleteOne(){
        deleteOnes();
        stopTimout=setTimeout(function(){
            decrement =setInterval(function(){
            deleteOnes();
            },100);
        },500);
    }

    function deleteClear(){
        clearTimeout(stopTimout);
        clearInterval(decrement);
    }

    function plus() {
        setResult();
        model = "plus";
    }
    function minus() {
        setResult();
        model = "minus";
    }
    function multiply() {
        setResult();
        model = "multiply";
    }
    function confirmMoney() {
        if (this.innerHTML === "=") {
            setResult();
            model = "none";
            confirm.innerHTML = "确认";
        } else {
            var value = monitor.innerHTML;
            value = parseFloat(value).toFixed(2);
            show.innerHTML = value;
            input.value = value;
            $("#money_keyboard").hide();
            checkForm();
        }
    }
    function setResult() {
        var valueOrigin = monitor.innerHTML;
        if (clear) {
            result = parseFloat(valueOrigin);
        } else {
            if (model === "plus") {
                result = parseFloat(result) + parseFloat(valueOrigin);
            } else if (model === "minus") {
                result = parseFloat(result) - parseFloat(valueOrigin);
            } else if (model === "multiply") {
                result = parseFloat(result) * parseFloat(valueOrigin);
            } else {
                result = parseFloat(valueOrigin);
            }
        }
        result = result.toFixed(2);
        clear = true;
        monitor.innerHTML = result;
        confirm.innerHTML = "=";
    }
});
