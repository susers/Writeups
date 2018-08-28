<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Join</title>

    <?php include 'bootstrap.php'; ?>

</head>
<body>
<div class="container">
    <h1>Join</h1>
    <div class="form-group">
        <form action="join.ok.php" method="post">
            <div class="row">
                <div class="col-md-1">
                    username
                </div>
                <div class="col-md-4">
                    <input type="text" name="username" maxlength="100" class="form-control">
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
                <div class="col-md-1">
                    age :
                </div>
                <div class="col-md-4">
                    <input type="text" name="age" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-1">
                    blog :
                </div>
                <div class="col-md-4">
                    <input type="text" name="blog" class="form-control">
                </div>
            </div>

            <div class="row">
                <input type="submit" value="join" class="btn btn-info">
            </div>


        </form>
    </div>
</div>
</body>
</html>
