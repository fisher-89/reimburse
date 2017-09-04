// dd.config({
//     agentId: _config.agentId, // 必填，微应用ID
//     corpId: _config.corpId, //必填，企业ID
//     timeStamp: _config.timeStamp, // 必填，生成签名的时间戳
//     nonceStr: _config.nonceStr, // 必填，生成签名的随机串
//     signature: _config.signature, // 必填，签名
//     jsApiList: [
//         'runtime.info',
//         'biz.navigation.setRight',
//         'device.notification.toast',
//         'biz.contact.choose',
//         'biz.customContact.choose'
//     ] // 必填，需要使用的jsapi列表，注意：不要带dd。
// });
dd.ready(function () {
    iosNavLeftButton();//ios首页关闭按钮
    andoridNavLeftButton();//andorid首页关闭按钮
    setNavRightButton();//设置右侧按钮
    //退出应用
    $("#exit").on("click", function () {
        dd.biz.navigation.close();
    });

});

dd.error(function (err) {
    location.reload();
});

/**
 * 设置导航栏右侧按钮
 */
function setNavRightButton(){
    dd.biz.navigation.setRight({
        show: navRight.show,//控制按钮显示， true 显示， false 隐藏， 默认true
        control: navRight.control,//是否控制点击事件，true 控制，false 不控制， 默认false
        text: navRight.text,//控制显示文本，空字符串表示显示默认文本
        onSuccess : navRight.onSuccess,
        onFail : function(err) {alert(err)}
    });
}


//ios 首页返回按钮
function iosNavLeftButton() {
    dd.biz.navigation.setLeft({
        show: true, //控制按钮显示， true 显示， false 隐藏， 默认true
        control: true, //是否控制点击事件，true 控制，false 不控制， 默认false
        showIcon: true, //是否显示icon，true 显示， false 不显示，默认true； 注：具体UI以客户端为准
        text: '', //控制显示文本，空字符串表示显示默认文本
        onSuccess: function (result) {
            var home_url = '/home';
            var this_url = window.location.pathname;
            if (home_url == this_url) {
                dd.biz.navigation.close();
            } else {
                history.back();
            }
        },
        onFail: function (err) {}
    });
}
//andorid 首页关闭处理
function andoridNavLeftButton() {
    document.addEventListener('backbutton', function (e) {
        // 在这里处理你的业务逻辑
        var home_url = '/home';
        var this_url = window.location.pathname;
        if (home_url == this_url) {
            e.preventDefault(); //backbutton事件的默认行为是回退历史记录，如果你想阻止默认的回退行为，那么可以通过preventDefault()实现
            dd.biz.navigation.close();
        }
    });
}


