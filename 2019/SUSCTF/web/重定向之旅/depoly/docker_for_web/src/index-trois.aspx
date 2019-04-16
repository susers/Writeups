<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>第三关</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="index.html">SUSCTF</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="" onclick=alert("JS\nhint：AAencode")>关于</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container" style="font-size:20px;text-align:center;height:700px;display:flex;justify-content:center;align-items:center;">

      <div class="starter-template">

            <h1>SUSCTF 2019</h1>
            <h2>第三关</h2>
            <p>我只给你最后一次机会了。</p>
            <audio id="music" autoplay="autoplay" src="秒表.mp3"></audio>
	<div class="panel" style="box-sizing: content-box; background-position: center center; background-repeat: no-repeat; height: 240px; width: 240px; background-color: #fff; border-radius: 240px; border: 15px #293742 solid; background-image:url('container.svg');">
		<div class="arrow_cont">
			<div class="arrow" style="height:240px;width:240px; box-sizing:border-box;background-position:center center;background-repeat:no-repeat;z-index:1;position:relative; background-image:url('arrow.svg'); transform:ratation(180deg);">
			</div>
		</div>
	</div>
<script type="application/javascript" src="index-ne.js"></script>
<script src="JumpBubble.js"></script>
<script>
	time = 30;
	function set_arrow() {
		time -= 0.05;
		if (time > 60)
			time %= 60;
		document.getElementsByClassName('arrow')[0].style.transform = "rotate(" + time * 6 + "deg)";
		if (time <= 0) {
			clearInterval(thead);
			leave();
		}
	}
	thead = setInterval(set_arrow, 50);
	setTimeout(create_bubble,1000);
</script>
      </center></div>

    </div>
  </body>
</html>