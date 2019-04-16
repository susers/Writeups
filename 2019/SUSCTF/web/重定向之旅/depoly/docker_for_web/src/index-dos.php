<?php
$part2="rEdirEct";
if(isset($_SERVER["HTTP_REFERER"]))
{
	$referer = $_SERVER["HTTP_REFERER"];
	$referer_info = parse_url($referer);
}
if(isset($referer_info) && substr($referer_info["path"],strlen($referer_info["path"])-14)=='index-ein.html')
{
	header("\$part2:".$part2);
	header("X-Accel-Buffering:no");
	header("Refresh:1,url=index-trois.aspx");
}
else
{
	header("Error:You are from a forbidden place!");
	header("Location:404.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>第二关</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">
	<style type="text/css">
		 .demo{
			padding: 2em 0;
			background: linear-gradient(to right, #2c3b4e, #4a688a, #2c3b4e);
			  }
			  .progress{
				  height: 25px;
				  background: #262626;
				  padding: 5px;
				  overflow: visible;
				  border-radius: 20px;
				  border-top: 1px solid #000;
				  border-bottom: 1px solid #7992a8;
				  margin-top: 50px;
			  }
			  .progress .progress-bar{
				  border-radius: 20px;
				  position: relative;
				  animation: animate-positive 2s;
			  }
			  .progress .progress-value{
				  display: block;
				  padding: 3px 7px;
				  font-size: 13px;
				  color: #fff;
				  border-radius: 4px;
				  background: #191919;
				  border: 1px solid #000;
				  position: absolute;
				  top: -40px;
				  right: -10px;
			  }
			  .progress .progress-value:after{
				  content: "";
				  border-top: 10px solid #191919;
				  border-left: 10px solid transparent;
				  border-right: 10px solid transparent;
				  position: absolute;
				  bottom: -6px;
				  left: 26%;
			  }
			  .progress-bar.active{
				  animation: reverse progress-bar-stripes 0.40s linear infinite, animate-positive 2s;
			  }
			  @-webkit-keyframes animate-positive{
				  0% { width: 0; }
			  }
			  @keyframes animate-positive{
				  0% { width: 0; }
			  }
	</style>
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="index.html">SUSCTF</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="" onclick=alert("Head\nhint：你摸得着头脑吗？")>关于</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container" style="font-size:20px;text-align:center;height:700px;display:flex;justify-content:center;align-items:center;">

      <div class="starter-template">

            <h1>SUSCTF 2019</h1>
            <h2>第二关</h2>
            <p>正在打开通往下一关的大门。。。</p>
            <audio autoplay="autoplay" src="进度条.mp3"></audio>
            <div class="progress">
				<div class="progress-bar progress-bar-success progress-bar-striped active" id="progress-bar" style="width: 0%;">
					<div class="progress-value" id="progress-value">0%</div>
				</div>
			</div>

      </center></div>

    </div>
  </body>
</html>
<?php
set_time_limit(0);
ob_start();
echo ob_get_clean();
flush();
$length = 100;
for($i=0; $i<$length; $i++) {
	usleep(50000);
    $proportion = ($i+1)/$length;
    $script = '<script>document.getElementById("progress-bar").style="width: %u%%";document.getElementById("progress-value").innerText="%u%%";</script>';
    echo sprintf($script, intval($proportion*100), intval(($i+1)/$length*100));
    echo ob_get_clean();
    flush();
}
echo "<script>if(navigator.userAgent.indexOf('Chrome')&&navigator.userAgent.indexOf('Firefox')<0)location.href='index-trois.aspx';</script>";
//为了防止IE不跳转
?>