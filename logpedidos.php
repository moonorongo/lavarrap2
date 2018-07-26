<?php
/*
    Log Pedidos Controller
*/    
    session_start();
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php/Logger.php");
    Logger::configure($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php.xml");
    $log = Logger::getLogger('databaseLogger');
    
    
    if(!isset($_SESSION['LOGGED'])) { // si no esta logueado
        header('HTTP/1.0 401 Unauthorized');
        echo ('NO AUTORIZADO: click <a href="http://'. $_SERVER['SERVER_NAME'] .'">aqui</a> para entrar');
    } else { // si esta logueado
        if (isAdmin()) {
            header('Content-Type: application/json');
        
            $mysql = Mysql::getInstance();
            $mysql->connect();

            $pedidos = new Pedidos($mysql);

            // dataTables Handler
            if(isset($_REQUEST["sEcho"])) {
                require_once($_SERVER["DOCUMENT_ROOT"] ."/script/includes/pedidos/datatable_handler.php");
            }
            
            $mysql->close();                
        } else  {
            // no es admin, no tiene permiso
            echo ('NO AUTORIZADO - debe ser administrador para ingresar');
            $log->info("NO AUTORIZADO - debe ser administrador para ingresar");
        } // end is_admin

    } // if logged    
?>