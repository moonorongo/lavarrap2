<?php
    session_start();
    if(isset($_SESSION['LOGGED'])) {
        header('Location: http://'. $_SERVER['SERVER_NAME'] .'/main.php');
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Lavarrap 1.0 - Ingreso</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="stylesheet" type="text/css" href="static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="static/css/main.css">
    <link rel="stylesheet" type="text/css" href="static/css/insumosServicios.css">

    <script>
        function send() {
            window.loginForm.submit();
        }
    </script>
        
    
</head>

<body onLoad="loginForm.nombre.focus()">
    <div id="mainAppContainer">
        <div class="menuBar">
            <div id="mainWrapper" class="centered">
                <div id="logo">
                    <img src="/static/img/logo.png" />
                </div>        
                <div id="menu" class="menu" style="text-align: right; margin-top: 5px; color: white;">
                    <form name="loginForm" action="login.php" method="POST">
                    <label for="nombre">Usuario </label>
                    <input type="text" name="user" />
                    <label for="clave" style="margin-left: 30px;">Clave </label>
                    <input type="password" name="pass" />
                    <button onClick="send()" style="margin-left: 30px;">Enviar</button>
                    </form>
                </div>

            </div>
        </div>

    <div id="mainApp" class="mainApp">
            <div id="sectionContainer" class="centered mainAppBackground"></div>
        </div>
    </div>
</body>
</html>
