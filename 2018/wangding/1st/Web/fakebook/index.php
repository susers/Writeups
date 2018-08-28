<?php session_start(); ?>
<?php require_once 'db.php'; ?>
<?php require_once 'user.php'; ?>
<?php

$flag = "FLAG{flag is in your mind}";

$db = new DB();
$user = new UserInfo();

?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fakebook</title>

    <?php include 'bootstrap.php'; ?>

</head>
<body>
<div class="container">
    <h1>the Fakebook</h1>
    <?php

    if (!isset($_SESSION['username'])) {
        $message = "<div class='row'>";
        $message .= "<div class='col-md-2'><a href='login.php' class='btn btn-success'>login</a></div>";
        $message .= "<div class='col-md-2'><a href='join.php' class='btn btn-info'>join</a></div>";
        $message .= "</div>";

        echo $message;
    }


    ?>
    <p>Share your stories with friends, family and friends from all over the world on <code>Fakebook</code>.</p>

    <table class="table">
        <tr>
            <th>#</th>
            <th>username</th>
            <th>age</th>
            <th>blog</th>
        </tr>
        <?php

        foreach ($db->getAllUsers() as $user)
        {
            $data = unserialize($user['data']);

            echo "<tr>";
            echo "<td>{$user['no']}</td>";
            echo "<td><a href='view.php?no={$user['no']}'>{$user['username']}</a></td>";
            echo "<td>{$data->age}</td>";
            echo "<td>{$data->blog}</td>";
            echo "</tr>\n";
        }

        ?>
    </table>
</div>
</body>
</html>
