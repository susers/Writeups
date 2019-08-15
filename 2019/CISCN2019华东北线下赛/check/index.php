<!--Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
<title>search</title>
<!-- Custom Theme files -->
<link href="css/search_style.css" rel="stylesheet" type="text/css" media="all"/>
<!-- Custom Theme files -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--Google Fonts-->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<!--Google Fonts-->
</head>
<body>
<!--search start here-->
<div class="search">
	<i> </i>
	<div class="s-bar">
	   <form action="index.php">
		<input type="text" name="url"/>
		<input type="submit" />
	  </form>
	</div>

</div>
<!--search end here-->	
<div class="copyright">
	 <p>The most popular search info page in China | Template by  <a href="#" target="_blank"> DeepDarkFantasy </a></p>
</div>	
</body>
</html>

<?php
	error_reporting(0);
	function is_inner_ipaddress($url) 
	{ 
	    try 
	    { 
	        $url_parse=parse_url($url);
	    } 
	    catch(Exception $e) 
	    { 
	        die("<script>alert('url format error')</script>"); 
	        return false;
	    } 
	    $hostname=$url_parse['host'];
	    $ip=gethostbyname($hostname); 
	    $int_ip=ip2long($ip); 
	    return ip2long('127.0.0.0')>>24 == $int_ip>>24;
	}

	$url = $_GET['url']; 
	if(!empty($url)){
		$url = 'http://'.$url;
		if (is_inner_ipaddress($url)) 
	    { 
	        die("<script>alert('Detected inner ip')</script>"); 
	    } 

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$res = curl_exec($ch);
	    echo $res;
	    curl_close($ch);
	}
?>
