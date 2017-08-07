<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
//在上面的例子中，当按钮的点击事件被触发时会调用一个函数：
$("button").click(function(){
	$("p").hide(); // 点击后隐藏动作
});
});
</script>



<!DOCTYPE html>
<html>
<head>
<script src="/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#hide").click(function(){
  $("p").hide();
  });
  $("#show").click(function(){
  $("p").show();
  });
});
</script>
</head>
<body>
<p id="p1">如果点击“隐藏”按钮，我就会消失。</p>
<button id="hide" type="button">隐藏</button>
<button id="show" type="button">显示</button>
</body>
</html>
