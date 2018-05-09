<?php

    if(isset($_GET['page']))
    {
        $file = $_GET['page'];
        if(strpos($file,"read")){
            header("Location: index.php?page=login.php");
            exit();
        }
        include($file);
    }
    else{
        header("Location: index.php?page=login.php");

    }
?>

<!-- users.sql  -->
