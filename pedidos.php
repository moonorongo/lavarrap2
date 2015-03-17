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

        $proveedores = new Proveedores($mysql);
        $pedidosService = new PedidosService(new Pedidos($mysql), 
                                            new ServiciosPedidos($mysql), 
                                            new Servicios($mysql), 
                                            $proveedores); 
        
        $proveedorModel = $proveedores->get($_SUCURSAL);
        
        $clientes = new Clientes($mysql);
        
        // dataTables Handler
        if($action == 'listAll') {
            $entregado = (isset($_REQUEST["entregado"]))? $_REQUEST["entregado"] : 0;
            $fechaPedido = (isset($_REQUEST["fechaPedido"]))? $_REQUEST["fechaPedido"] : "";
            $out = $pedidosService->listAll($entregado, $fechaPedido);
            echo json_encode($out);
        }
        


        // dummy para mantener la conexion
        if($action == 'dummy') {
            echo '{"response" : '+ rand(0,10000) +'}';
        }
        


        if($action=="pedidosModelCRUD") {
            // fetch (GET) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  
            if ( ($_method == null) && ($model == null) && ($codigo != null) ) {    
                $out = $pedidosService->get($codigo);
                echo json_encode($out);
            }


            // destroy (DELETE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( (($_method != null) && ($_method=="DELETE")) && ($model == null) && ($codigo != null) ) {
                $pedidosService->delete($codigo);
                echo('{"success" : true}');
            }


            // save (CREATE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( ($_method == null) && ($model != null) && ($codigo == null) ) {

                $modelData = json_decode($model, true);
                $modelData = $pedidosService->create($modelData);

                // registro anticipo, solo si no es plantilla
                if($modelData["nombre"] == "") {
                    $anticipo = floatval($modelData['anticipo']);
                    if($anticipo != 0) {
                        $caja = new Caja($mysql);
                        $caja->registrarIngreso($anticipo,"Anticipo operacion ". $proveedorModel["prefijoCodigo"] . $modelData['codigo']);
                    }
                }
                
                
                if ($modelData['imprimir']) {
                    $clientesData = $clientes->get($modelData["codigoCliente"]);
                    if(count($modelData['listaServicios']) == 0) { // si no tiene lista de servicios
                        $ticket = new Ticket("P","mm",array(105,148));
                        $ticket->proveedorModel = $proveedorModel;
                        $ticket->clientesData = $clientesData;
                        $ticket->modelData = $modelData;
                        $ticket->direccion = $proveedorModel['direccion'];
                        $ticket->zona = $proveedorModel['zona'];
                        $ticket->telefono = $proveedorModel['telefono'];
                        
                        $ticket->generate();
                    } else { // tiene servicios cargados
                        $ticket = new Ticket("P","mm",array(105,148));
                        $ticket->clientesData = $clientesData;
                        $ticket->proveedorModel = $proveedorModel;
                        $ticket->modelData = $modelData;
                        $ticket->direccion = $proveedorModel['direccion'];
                        $ticket->zona = $proveedorModel['zona'];
                        $ticket->telefono = $proveedorModel['telefono'];
                        $ticket->generateWithServices();
                    }                
                }

                echo('{"success" : true}'); 
            }


            // save(PUT) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( ($_method != null) && (($_method=="PUT")) && ($model != null) && ($codigo != null) ) {
                $modelData = json_decode($model, true);
                $oldModelData = $pedidosService->get($modelData["codigo"]);
                $pedidosService->update($modelData);

                if($oldModelData["anticipo"] == 0) { // si anteriormente no registro anticipo
                    if($modelData["nombre"] == "") { // y no es plantilla
                        $anticipo = floatval($modelData['anticipo']);
                        if($anticipo != 0) { // si hay anticipo, entonces lo registro
                            $caja = new Caja($mysql);
                            $caja->registrarIngreso($anticipo,"Anticipo operacion ". $proveedorModel["prefijoCodigo"] . $modelData['codigo']);
                        }
                    }
                }
                
                if ($modelData['imprimir']) {
                    $clientesData = $clientes->get($modelData["codigoCliente"]);
                    if(count($modelData['listaServicios']) == 0) { // si no tiene lista de servicios
                        $ticket = new Ticket("P","mm",array(105,148));
                        $ticket->clientesData = $clientesData;
                        $ticket->proveedorModel = $proveedorModel;
                        $ticket->modelData = $modelData;
                        $ticket->direccion = $proveedorModel['direccion'];
                        $ticket->zona = $proveedorModel['zona'];
                        $ticket->telefono = $proveedorModel['telefono'];
                        $ticket->generate();

                    } else { // tiene servicios cargados
                        
                        $ticket = new Ticket("P","mm",array(105,148));
                        $ticket->clientesData = $clientesData;
                        $ticket->proveedorModel = $proveedorModel;
                        $ticket->modelData = $modelData;
                        $ticket->direccion = $proveedorModel['direccion'];
                        $ticket->zona = $proveedorModel['zona'];
                        $ticket->telefono = $proveedorModel['telefono'];
                        $ticket->generateWithServices();
                    }
                }
                
                echo('{"success" : true}');
            }    
        } // pedidosModelCRUD
        

        
        if($action=="getListaServicios") {
            $out["listaServiciosCombo"] = $pedidosService->getListaServicios();
            $out["listaProveedoresCombo"] = $pedidosService->getListaProveedores();
            echo json_encode($out);
        } 
        

        if($action == 'getTemplates') {
            $out = $pedidosService->listTemplates();
            echo json_encode($out);
        }
        
        
        $mysql->close();    
    } // if logged    
?>