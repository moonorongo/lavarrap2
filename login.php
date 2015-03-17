<?php
    session_start();
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
    
    $mysql = Mysql::getInstance();
    $mysql->connect();
    
    $login = new Login($mysql);
    
    $user = $_REQUEST["user"];
    $pass = $_REQUEST["pass"];
    
    $userData = $login->check($user, $pass);
    
    if($userData['LOGGED'] == 1) {
        $_SESSION['LOGGED'] = $userData['LOGGED'];
        $_SESSION['SUCURSAL'] = $userData['SUCURSAL'];
        $_SESSION['ADMIN'] = $userData['ADMIN'];
        $_SESSION['DESCRIPCION'] = $userData['DESCRIPCION'];
        header('Location: http://'. $_SERVER['SERVER_NAME'] .'/main.php'); 
    } else {
        header('Location: http://'. $_SERVER['SERVER_NAME']); 
    }
    
    $mysql->close();
?>
