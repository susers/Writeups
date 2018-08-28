<?php session_start(); ?>
<?php require_once 'db.php'; ?>
<?php require_once 'user.php'; ?>
<?php require_once 'error.php'; ?>
<?php

$db = new DB();

?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User</title>

    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<?php

$no = $_GET['no'];
if ($db->anti_sqli($no))
{
    die("no hack ~_~");
}

$res = $db->getUserByNo($no);
$user = unserialize($res['data']);
//print_r($res);

?>
<div class="container">
    <table class="table">
        <tr>
            <th>
                username
            </th>
            <th>
                age
            </th>
            <th>
                blog
            </th>
        </tr>
        <tr>
            <td>
                <?php echo $res['username']; ?>
            </td>
            <td>
                <?php echo $user->age; ?>
            </td>
            <td>
                <?php echo xss($user->blog); ?>
            </td>
        </tr>
    </table>

    <hr>
    <br><br><br><br><br>
    <p>the contents of his/her blog</p>
    <hr>
    <?php

    $response = $user->getBlogContents();
    if ($response === 404)
    {
        echo "404 Not found";
    }

    else
    {
        $base64 = base64_encode($response);
        echo "<iframe width='100%' height='10em' src='data:text/html;base64,{$base64}'>";
        // echo $response;
    }

    // var_dump($user->getBlogContents());
    ?>

</div>
</body>
</html>