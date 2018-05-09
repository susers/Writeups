<?php
session_start();
if($_SESSION["login"]!==1){
    header("Location: index.php?page=login.php");
    exit();
}
?>
<!doctype html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>index</title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
<?php

if (@$_POST['url']) {
    $url = @$_POST['url'];
    if(preg_match("/^http(s?):\/\/.+/", $url)){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, True);
    curl_setopt($ch,CURLOPT_REDIR_PROTOCOLS,CURLPROTO_GOPHER|CURLPROTO_HTTP|CURLPROTO_HTTPS);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);

    curl_close($ch);
}

}
?>

<div class="htmleaf-container">
	<div class="wrapper">
		<div class="container">
			<h1>Hello</h1>

			<form action="" class="form" method="POST">
				<input name="url" type="text" placeholder="http://www.baidu.com/">
				<button type="submit" id="login-button">Send</button>
			</form>
		</div>

		<ul class="bg-bubbles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
</div>

<script>
$('#login-button').click(function (event) {
	event.preventDefault();
	$('form').fadeOut(500);
	$('.wrapper').addClass('form-success');
});
</script>

<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';color:#000000">
</div>
</body>
</html>
