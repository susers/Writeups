<?php

require_once 'lib.php';
$mysqli = new mysqli('localhost', 'user', '!@#user', 'fakebook');

class DB
{

    function __construct()
    {
        $mysqli = new mysqli('localhost', 'root', '!@#1234!@#', 'fakebook');
    }

    public function isValidUsername ($username)
    {
        global $mysqli;
        $query = "select * from users where username = '{$username}'";
        $res = $mysqli->query($query);
        if (!$res->fetch_array())
            return 1;

        else
            return 0;
    }

    function login ($username, $passwd)
    {
        global $mysqli;

        $username = addslashes($username);
        $passwd = sha512($passwd);
        $query = "select * from users where username = '{$username}' and passwd = '{$passwd}'";
        $res = $mysqli->query($query);

        return $res->fetch_array();
    }

    function insertUser ($username, $passwd, $data)
    {
        global $mysqli;

        $username = substr($username, 0, 100);
        $username = addslashes($username);
        $passwd = sha512($passwd);
        $data = serialize($data);
        $data = addslashes($data);

        $query = "insert into users (username, passwd, data) values ('{$username}', '{$passwd}', '{$data}')";
        return $mysqli->real_query($query);
    }

    public function getAllUsers ()
    {
        global $mysqli;

        $query = "select * from users";
        $res = $mysqli->query($query);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserByNo ($no)
    {
        global $mysqli;

//        $no = addslashes($no);
        $query = "select * from users where no = {$no}";
        $res = $mysqli->query($query);
        if (!$res)
            echo "<p>[*] query error! ({$mysqli->error})</p>";
        return $res->fetch_assoc();
    }

    public function anti_sqli ($no)
    {
        $patterns = "/union\Wselect|0x|hex/i";

        return preg_match($patterns,$no);
    }

}


/*
CREATE TABLE `users` ( `no` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(100) NOT NULL , `passwd` VARCHAR(128) NOT NULL , `data` TEXT NOT NULL , PRIMARY KEY (`no`)) ENGINE = MyISAM;

 */
