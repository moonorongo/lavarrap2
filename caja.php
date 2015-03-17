<?php
/*
    Caja Controller
*/    
    session_start();
    if(!isset($_SESSION['LOGGED'])) { // si no esta logueado
        header('HTTP/1.0 401 Unauthorized');
        echo ('NO AUTORIZADO: click <a href="http://'. $_SERVER['SERVER_NAME'] .'">aqui</a> para entrar');
    } else { // si esta logueado
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
        header('Content-Type: application/json');
    
        $mysql = Mysql::getInstance();
        $mysql->connect();

        $caja = new Caja($mysql);
        $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;

        
        if($action == 'cajaHandler') {
            $model = json_decode($_REQUEST["model"]);

            if($model->sign == 1) {
                $caja->registrarIngreso($model->monto, $model->observaciones);
            } else {
                $caja->registrarEgreso($model->monto, $model->observaciones);
            }
            echo '{"success" : '. $model->sign .'}';
        }


        if($action == 'cajaListHandler') {
            $fecha = $_REQUEST["fecha"];
            $cList = $caja->listEgresosMes($fecha);
            echo json_encode($cList);
        }    
        

        
        if($action == 'cerrarCajaHandler') {
            $success = true;
            $year = $_REQUEST["year"];
            $month = intval($_REQUEST["month"]);
            $nextMonth = $month + 1;
            $monto = $caja->obtenerSaldoMes($year, $month);
            $success = $caja->registrarSaldoInicial($year .'-'. $nextMonth, $monto['monto']);
            echo '{ "success" : '. $success .'}';
        }    

        
        $mysql->close();    
    } // if logged    
?>