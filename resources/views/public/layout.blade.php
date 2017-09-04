<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    @section('css')
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
    @show
</head>
</html>
<body>
<!-- content start -->
<section class="content" id="content">
    @yield('content')
</section>
<div class="waiting">
    <div><span><i class="fa fa-spinner"></i>&nbsp;</span></div>
</div>
<!-- contetn end -->

<!-- footer start -->
<section class="footer" id="footer">
    @yield('footer')
</section>
<!-- footer end -->
</body>
@section('js')
    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="{{asset('js/jquery-1.10.2.min.js')}}"></script>
    <script src="{{asset('js/jquery-ui-1.9.2.custom.min.js')}}"></script>
    <script src="{{asset('js/jquery-migrate-1.2.1.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/modernizr.min.js')}}"></script>
    <!--common scripts for all pages-->
    <script src="{{asset('js/scripts.js')}}"></script>
    <script src="http://g.alicdn.com/dingding/open-develop/0.7.0/dingtalk.js"></script>
    <script>
        //导航栏右侧按钮
        var navRight = @section ('header_right')
            {
                'show': false, 'control': false, 'text': 'none', 'onSuccess': function(){}
            }
                @show;
    </script>
    <script src="{{asset('js/dingding.js')}}"></script>
@show