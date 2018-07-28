<!DOCTYPE html>
<html lang="en-US">
<head><title>REQUESTS PAGE</title>
</head>

<body>

<?php
include_once "config.php";
if (isset($_POST['url'])&&!empty($_POST['url']))
{
	$url = $_POST['url'];
	$content_url = getUrlContent($url);
}
else
{
	$content_url = "";
}
if(isset($_GET['debug']))
{
	show_source(__FILE__);
}


?>

<form action="index.php" method="POST">
<input name="url" type="text">
<input type="submit" value="CURL">
</form>


<?php 

echo $content_url;
?>
</body>













































































































<!-- index.php?debug=1-->
</body>
