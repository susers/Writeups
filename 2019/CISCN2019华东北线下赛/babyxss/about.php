<?php include 'function.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文章精选</title>
    <link href="/static/css/main.css" rel="stylesheet">
    
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <?php headers();?>
    <div class="row-fluid">
        <div class="span12">
            <p>
            感谢大家来到我的网站,我们立志于做一个精选文章社区。<br/>
            本站主页所有文章均为精挑细选。<br/>
            如果想投稿给我们，可以直接使用<a href="post.php">发表文章</a>功能。我们会给你提供一个文章托管服务。<br/>
            如需首页推荐，请通过提交反馈向管理员进行申请。管理员会对你文章审核后，决定是否主页推荐。<br/>
            </p>
        </div>
    </div>
</div>
    <?php getJS();?>
</body>
</html>
