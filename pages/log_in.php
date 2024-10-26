<?php

session_start();

require_once "connect.php";

$connect = @new mysqli($host, $db_user, $db_password, $db_name);

if($connect->connect_errno!=0) {
    echo "Error: ".$connect->connect_errno;
}

else {
    $login = $_POST['login_email'];
    $password = $_POST['password'];

    $sql="SELECT * FROM users WHERE email ='$login' AND password = '$password'";
    if($result = @$connect->query($sql)) {
        $how_many_users = $result->num_rows;
        {
            if($how_many_users > 0) {

                $_SESSION['logged_in'] = true;

                $row = $result->fetch_assoc();

                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                unset($_SESSION['error']);

                $result->free_result();
                header('Location: balance.php');
            }else {
                $_SESSION['error'] = '<span style = "color:red"> Niepoprawny login lub hasło </span>';
                header('Location: login.php');
            }
        }
        
        $connect->close();

    }


}



?>