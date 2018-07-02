<?php
/*
    Pedidos Controller
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

        $_method = (isset($_REQUEST["_method"]))? $_REQUEST["_method"] : null;
        $model = (isset($_REQUEST["model"]))? $_REQUEST["model"] : null;
        $codigo = (isset($_REQUEST["codigo"]))? $_REQUEST["codigo"] : null;
        $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;

        $cuentaCorriente = new CuentaCorriente($mysql);
        $cuentaCorrienteService = new CuentaCorrienteService($cuentaCorriente); 

        // dataTables Handler
        if($action == 'listAll') {
            $codigoCliente = $_REQUEST["codigoCliente"];
            $year = $_REQUEST["year"];
            $month = $_REQUEST["month"] + 1;
            $out = $cuentaCorrienteService->listAll($codigoCliente, $month, $year);
            echo json_encode($out);
        }


        if($action=="ingresarPago") {
            $codigoCliente = $_REQUEST["codigoCliente"];
            $cantidad = floatval($_REQUEST["cantidad"]);
            $cantidadTotal = $cantidad;
            $listaPedidos = $cuentaCorriente->listAll($codigoCliente); // al no poner fecha, lista solo los impagos
            $out = Array();
            
            if($cantidad >= 0) {
                foreach($listaPedidos as $item) {
                    if($item["monto"] != 0) {
                        if($item["codigo"] == 0) {
                            $cantidad -= $item["monto"];  // como aca monto es negativo (porq es saldo a favor), en realidad se agrega a cantidad
                        } else {
                            if($item["monto"] < $cantidad) { 
                                $cantidad -= $item["monto"]; 
                                $out[] = Array("codigoCuentaCorriente" => $item["codigoCuentaCorriente"], 
                                            "codigoPedido" => $item["codigo"], 
                                            "monto" => 0);
                            } else {
                                $out[] = Array("codigoCuentaCorriente" => $item["codigoCuentaCorriente"], 
                                            "codigoPedido" => $item["codigo"], 
                                            "monto" => $item["monto"] - $cantidad);
                                $cantidad = 0;
                                break;
                            } 
                        }
                    }
                } // foreach
            } else {
                echo '{ "success" : false }';
            }

            echo '{ "cantidad" : '. $cantidadTotal .', "success" : true,  "itemsAfectados" : '. json_encode($out) .', "aFavorDelCliente" : '. $cantidad .' }';
        } // ingresarPago



        if($action=="confirmarIngresarPago") {
            $success = true;
            $codigoCliente = $_REQUEST["codigoCliente"];
            $caja = new Caja($mysql);
            
            $model = json_decode($model);
            $success = $cuentaCorrienteService->actualizarSaldo($model, $codigoCliente);

            if($success && ($model->cantidad != 0)) {
                $caja->registrarIngreso($model->cantidad,"Ingreso CC cliente ". $codigoCliente);
            } 
            
            echo '{"success" : '. $success .' }';
        } // confirmarIngresarPago





        if($action=="ingresarCorreccion") {
            $caja = new Caja($mysql);
            $codigoCliente = $_REQUEST["codigoCliente"];
            $cantidad = floatval($_REQUEST["cantidad"]);
            $cuentaCorrienteService->corregirSaldo( $cantidad, $codigoCliente);

            if($cantidad < 0) {
                $caja->registrarIngreso($cantidad,"Correccion CC cliente ". $codigoCliente);
            } else {
                $caja->registrarEgreso($cantidad,"Correccion CC cliente ". $codigoCliente);
            }
            
            echo '{"success" : true }';
        }    




        $mysql->close();    
    } // if logged
?>