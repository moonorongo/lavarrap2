<?php
/*
    Caja Controller
*/    
    session_start();
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php/Logger.php");
    Logger::configure($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php.xml");
    $log = Logger::getLogger('rootLogger');
    
    
    if(!isset($_SESSION['LOGGED'])) { // si no esta logueado
        header('HTTP/1.0 401 Unauthorized');
        echo ('NO AUTORIZADO: click <a href="http://'. $_SERVER['SERVER_NAME'] .'">aqui</a> para entrar');
    } else { // si esta logueado
        if (isAdmin()) {
            header('Content-Type: application/json');
        
            $mysql = Mysql::getInstance();
            $mysql->connect();

            $caja = new Caja($mysql);
            $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;

            if($action == 'cajaHandler') {
                $model = json_decode($_REQUEST["model"]);

                if($model->codigo != -1) {
                    $caja->editarCaja($model->codigo, $model->monto, $model->observaciones);
                } else {
                    if($model->sign == 1) {
                        $caja->registrarIngreso($model->monto, $model->observaciones);
                    } else {
                        $caja->registrarEgreso($model->monto, $model->observaciones);
                    }
                }

                echo '{"success" : '. $model->sign .'}';
            }


            if($action == 'cajaListHandler') {
                $fecha = $_REQUEST["fecha"];
                $search = $_REQUEST['search'];
                $cList = $caja->listEgresosMes($fecha, $search);
                echo json_encode($cList);
            }    
            

            
            if($action == 'cerrarCajaHandler') {
                $success = true;
                $year = $_REQUEST["year"];
                $month = intval($_REQUEST["month"]);
                $monto = $caja->obtenerSaldoMes($year, $month);
                
                if ($month <> 12) {
                    $nextMonth = $month + 1;
                } else {
                    $nextMonth = 1;
                    $year++;
                }
                $success = $caja->registrarSaldoInicial($year .'-'. $nextMonth, $monto['monto']);
                echo '{ "success" : '. $success .'}';
            }    


            if($action == 'fixPedidosCaja') {
                require_once($_SERVER["DOCUMENT_ROOT"] ."/script/includes/caja/pedidos_caja.php");
            }


            if($action == 'deleteCaja') {
                require_once($_SERVER["DOCUMENT_ROOT"] ."/script/includes/caja/delete.php");
            }

            
            $mysql->close();                
        } else  {
            // no es admin, no tiene permiso
            echo ('NO AUTORIZADO - debe ser administrador para ingresar');
            $log->warn("NO AUTORIZADO - debe ser administrador para ingresar");
        } // end is_admin

    } // if logged    
?>