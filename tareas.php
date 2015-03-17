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
    
        global $_SUCURSAL;    
        
        $mysql = Mysql::getInstance();
        $mysql->connect();
        
        $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;

        $serviciosPedidos = new ServiciosPedidos($mysql);
        $tareasService = new TareasService($serviciosPedidos); 
        
        $proveedores = new Proveedores($mysql);
        $proveedorData = $proveedores->get($_SUCURSAL);
        
        // dataTables Handler
        if($action == 'listAll') {
            $codigoEstado = $_REQUEST["codigoEstado"];
            $out = $tareasService->listAll($codigoEstado);
            echo json_encode($out);
        }

        if($action == 'tareasHandler') {
            $codigoEstado = $_REQUEST["codigoEstado"];
            
            
            switch($codigoEstado) {
                case "-1" : // derivo
                            $tareasSeleccionadas = explode(',', $_REQUEST["tareasSeleccionadas"]);
                            $codigoProveedor = $_REQUEST["codigoProveedor"];
                            $serviciosPedidos->derivar($tareasSeleccionadas, $codigoProveedor);
                            break;
                        
                case "2" :  // inicio lavado
                            $tareasSeleccionadas = explode(',', $_REQUEST["tareasSeleccionadas"]);
                            $serviciosPedidos->cambiarEstado($tareasSeleccionadas, $codigoEstado);
                            $data = $tareasService->get($tareasSeleccionadas);
                            $ticket = new Ticket("P","mm",array(105,148));
                            $ticket->direccion = $proveedorData['direccion'];
                            $ticket->zona = $proveedorData['zona'];
                            $ticket->telefono = $proveedorData['telefono'];
                            $ticket->soloControl = true;
                            $ticket->AddPage();
                            $ticket->ListTareas($data);
                            
                            // aca va a imprimir las observaciones de la 1er tarea.
                            // si bien permite mezclar pedidos, no puedo poner las observaciones de 
                            // cada pedido.
                            // aca en $data viene en cada registro la observacion del pedido... si todas las tareas pertenecen
                            // al mismo pedido no habra problema.
                            
                            $ticket->printObs($data[0]["observaciones"]);
                            $ticket->Output($_SERVER["DOCUMENT_ROOT"] ."/static/download/". session_id() .".pdf",'F');
                            break;
                        
                case "3" :  // finalizo lavado
                            $tareasSeleccionadas = explode(',', $_REQUEST["tareasSeleccionadas"]);
                            $serviciosPedidos->cambiarEstado($tareasSeleccionadas, $codigoEstado);
                            break;
                case "4" :  // entrego pedido
                            $montoPagado = floatval($_REQUEST["montoPagado"]);
                            $vuelto = floatval($_REQUEST["vuelto"]);

                            // marco finalizado
                            $pedidos = new Pedidos($mysql);
                            $pedidoModel = $pedidos->get($_REQUEST["codigoPedido"]);
                            $pedidos->entregar($_REQUEST["codigoPedido"]);

                            $clientes = new Clientes($mysql);
                            $modelCliente = $clientes->get($pedidoModel["codigoCliente"]);
                            
                            // cambio estado tarea a Entregado
                            $listaTareas = $serviciosPedidos->getByCodigoPedido($_REQUEST["codigoPedido"]);
                            $tareasSeleccionadas = Array();
                            
                            forEach($listaTareas as $tarea) {
                                $tareasSeleccionadas[] = $tarea["codigo"];
                            }
                            
                            $serviciosPedidos->cambiarEstado($tareasSeleccionadas, $codigoEstado);
                            
                            // genero ticket
                            $data = $tareasService->get($tareasSeleccionadas);
                            $ticket = new Ticket("P","mm",array(105,148));
                            $ticket->modelCliente = $modelCliente;
                            $ticket->direccion = $proveedorData['direccion'];
                            $ticket->zona = $proveedorData['zona'];
                            $ticket->telefono = $proveedorData['telefono'];
                            $ticket->soloControl = false;
                            $ticket->anticipo = $pedidoModel["anticipo"];
                            $ticket->codigo = $data[0]['_prefijoCodigo'] . $data[0]['codigoPedido'];
                            $ticket->AddPage();
                            $ticket->ListTareas($data, $montoPagado);
                            $ticket->Output("entrega_". $ticket->codigo .".pdf",'I');
                            
                            // registro el movimiento en caja
                            
                            $caja = new Caja($mysql);
                            if($montoPagado != 0) { 
                                $caja->registrarIngreso($montoPagado,"Monto pagado operacion ". $ticket->codigo);
                            }
                            if($vuelto != 0) {
                                $caja->registrarEgreso($vuelto,"Vuelto entregado operacion ". $ticket->codigo);
                            }
                            
                            
                            break;
                case "5" :  // a cuenta Corriente
                            // cambio estado tarea a Entregado
                            $sumatoriaCobrar = 0;
                            $listaTareas = $serviciosPedidos->getByCodigoPedido($_REQUEST["codigoPedido"]);
                            $tareasSeleccionadas = Array();
                            forEach($listaTareas as $tarea) {
                                $tareasSeleccionadas[] = $tarea["codigo"];
                                $sumatoriaCobrar += intval($tarea["_subTotal"]);
                            }
                            
                            $serviciosPedidos->cambiarEstado($tareasSeleccionadas, $codigoEstado);            
                            
                            // marco finalizado 
                            $pedidos = new Pedidos($mysql);
                            $pedidos->entregar($_REQUEST["codigoPedido"]); 
                            
                            
                            // cargo $sumatoriaCobrar en cuentaCorriente del cliente
                            $pedidoModel = $pedidos->get($_REQUEST["codigoPedido"]);
                            $cuentaCorriente = new CuentaCorriente($mysql);
                            $cuentaCorriente->create( Array("codigoCliente" => $pedidoModel["codigoCliente"], 
                                                        "codigoPedido" => $_REQUEST["codigoPedido"], 
                                                        "monto" => $sumatoriaCobrar) );
                            
                            // genero ticket
                            $data = $tareasService->get($tareasSeleccionadas);
                            $ticket = new Ticket("P","mm",array(105,148));
                            $ticket->direccion = $proveedorData['direccion'];
                            $ticket->zona = $proveedorData['zona'];
                            $ticket->telefono = $proveedorData['telefono'];
                            //$ticket->codigo = $data[0]['_prefijoCodigo'] . $data[0]['codigoPedido'];
                            $ticket->codigo = Date('d/m/Y');
                            $ticket->AddPage();
                            $ticket->ListTareasRemito($data, 0);
                            $ticket->Output("remito_". $ticket->codigo .".pdf",'I');
                            
                            break;
            } // switch
            
            echo '{ "success" : true}';
        }

        $mysql->close();    
    } // if logged    
?>