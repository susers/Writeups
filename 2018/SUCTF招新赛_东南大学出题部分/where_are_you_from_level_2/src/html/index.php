<?php
/**
 * Created by PhpStorm.
 * User: image
 * Date: 18-3-17
 * Time: 下午1:02
 */
require_once('db.inc.php');
$ip=getIp();

    $time=date("Y-m-d h:i:sa");
    $query=$mysqli->query("insert into ip_records(ip,time) values ('$ip','$time')");

    $insert_id=$mysqli->insert_id;
if(!isset($_SESSION['id'])){

    $_SESSION['id']=Array();
    }
    array_push($_SESSION['id'],$insert_id);
    $query='';
    foreach($_SESSION['id'] as $id){
        $query=$id.','.$query;
    }
    $query=substr($query,0,strlen($query)-1);
    $res=$mysqli->query("select * from ip_records where id in ($query)");
   
    
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My pictrues</title>

    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

</head>

<body>

<!-- Top content -->
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text" style="">
                    <h1><strong>hello form <?=$ip;?></strong> </h1>
                    <div class="description">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h1>Your Vsisit Record</h1>

                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="form-bottom">



                        <table class="table">
                            <thead>
                            <tr>
                                <th>Student-ID</th>
                                <th>ip地址</th>
                                <th>访问时间</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php 
			if($res)
                            while($row=$res->fetch_array()){?>
                            <tr>
                                <td><?=$row['id'];?></td>
                                <td><?=$row['ip'];?></td>
                                <td><?=$row['time'];?></td>
                            </tr>
                            <?php
                            }
                            ?>
                           
                            </tbody>
                        </table>
                        <?php
                        if($ip==='127.0.0.1'){
                            print "<p>Congratulations:flag1:SUCTF{X_F0rw4rd3d_F0r_7O_cHe4t_5eV3r}</p>";
                        
                        
                        
                        ?>
                        <code style="width:80%;word-wrap: break-word; word-break: normal;">
                        <span style="color: #000000">
<span style="color: #0000BB">&lt;?php<br /></span><span style="color: #007700">function&nbsp;</span><span style="color: #0000BB">getIp</span><span style="color: #007700">(){<br />&nbsp;&nbsp;&nbsp;&nbsp;if(!empty(</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'HTTP_CLIENT_IP'</span><span style="color: #007700">])){<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">=</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'HTTP_CLIENT_IP'</span><span style="color: #007700">];<br />&nbsp;&nbsp;&nbsp;&nbsp;}<br />&nbsp;&nbsp;&nbsp;&nbsp;elseif(!empty(</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'HTTP_X_FORWARDED_FOR'</span><span style="color: #007700">])){<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">=</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'HTTP_X_FORWARDED_FOR'</span><span style="color: #007700">];<br />&nbsp;&nbsp;&nbsp;&nbsp;}<br />&nbsp;&nbsp;&nbsp;&nbsp;elseif(!empty(</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'REMOTE_ADDR'</span><span style="color: #007700">])){<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">=</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'REMOTE_ADDR'</span><span style="color: #007700">];<br />&nbsp;&nbsp;&nbsp;&nbsp;}<br />&nbsp;&nbsp;&nbsp;&nbsp;else{<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">=</span><span style="color: #DD0000">''</span><span style="color: #007700">;<br />&nbsp;&nbsp;&nbsp;&nbsp;}<br />&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">=</span><span style="color: #0000BB">preg_replace</span><span style="color: #007700">(</span><span style="color: #DD0000">'/\s|select|from|limit|union|join/iU'</span><span style="color: #007700">,</span><span style="color: #DD0000">''</span><span style="color: #007700">,</span><span style="color: #0000BB">$cip</span><span style="color: #007700">);<br />&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;</span><span style="color: #0000BB">$cip</span><span style="color: #007700">;<br />}<br />&nbsp;</span><span style="color: #0000BB">$query</span><span style="color: #007700">=</span><span style="color: #0000BB">$mysqli</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">query</span><span style="color: #007700">(</span><span style="color: #DD0000">"insert&nbsp;into&nbsp;ip_records(ip,time)&nbsp;values&nbsp;('</span><span style="color: #0000BB">$ip</span><span style="color: #DD0000">','</span><span style="color: #0000BB">$time</span><span style="color: #DD0000">')"</span><span style="color: #007700">);</span>
</span>

                        </code>
                        <?php
                        }
                        else{
                            print "only guest from 127.0.0.1 can get flag of level1";
                        }
                        ?>
                        <ul>
                       </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 social-login">
                    <h3>Share our site with:</h3>
                    <div class="social-login-buttons">
                        <a class="btn btn-link-1 btn-link-1-facebook" href="#">
                            <i class="fa fa-facebook"></i> Facebook
                        </a>
                        <a class="btn btn-link-1 btn-link-1-twitter" href="#">
                            <i class="fa fa-twitter"></i> Twitter
                        </a>
                        <a class="btn btn-link-1 btn-link-1-google-plus" href="#">
                            <i class="fa fa-google-plus"></i> Google Plus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="copyrights">Collect from <a href="http://www.cssmoban.com/"  title="网站模板">网站模板</a></div>


<!-- Javascript -->
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.backstretch.min.js"></script>
<script src="assets/js/scripts.js"></script>

<!--[if lt IE 10]>
<script src="assets/js/placeholder.js"></script>
<![endif]-->

</body>

</html>
