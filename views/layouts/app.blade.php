<html>
<head>
    <title>XavierQiu_Blog</title>
    {{--<link rel="stylesheet" href="style.css" type="text/css">--}}
    <style>
        body{margin: 0;padding: 0;background-color: #DEDEDE;}
        a{text-decoration:none;}
        .header{padding:10px 50px 10px 50px;border-bottom: 1px solid #eeeeee;}
        .header>.logo{display:inline-block;}
        .header>.menu{display:inline-block;margin-left:20px;}
        .content{}
        .left{background-color: white;margin: 25px 300px 25px 25px;padding: 25px;box-shadow: 1px 1px 2px 1px #848484;}
        .right{background-color: white;width: 200px;margin: 25px;padding: 25px;box-shadow: 1px 1px 2px 1px #848484;position: absolute;top: 92px;right: 0;}
        .footer{padding:10px 50px 10px 50px;background-color:gray;}
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="#"><h2>XavierQiu_Blog</h2></a>
        </div>

        <div class="menu">
            <a href="{{ route('articles.index') }}">Articles</a>
        </div>

    </div>
    <div class="content">
        <div class="left">
            @yield('content')
        </div>
        <div class="right">
            <div style="padding: 20px; border: 1px solid black;">
                <h3>Author</h3>
                <p>name : XavierQiu</p>
                <p>age : 19</p>
                <p>Tel : 17315213349</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Contact me:1574841856@qq.com</p>
    </div>
</body>

</html>

