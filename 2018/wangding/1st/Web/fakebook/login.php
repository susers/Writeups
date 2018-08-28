<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>login</title>

    <?php include 'bootstrap.php'; ?>

</head>
<body>
<div class="container">
    <h1>login page</h1>
    <form action="login.ok.php" method="post" class="form-group">
        <div class="row">
            <div class="col-md-1">
                username :
            </div>
            <div class="col-md-4">
                <input type="text" name="username" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
                passwd :
            </div>
            <div class="col-md-4">
                <input type="password" name="passwd" class="form-control">
            </div>
        </div>
        <div class="row">
            <input type="submit" value="login" class="btn btn-info">
        </div>
    </form>
</div>
</body>
</html>
