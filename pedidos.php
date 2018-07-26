<?php
/*
    Pedidos Controller
*/    
    session_start();
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php/Logger.php");
    Logger::configure($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php.xml");
    $log = Logger::getLogger('databaseLoggerPedidos');
    
    if(!isset($_SESSION['LOGGED'])) { // si no esta logueado
        header('HTTP/1.0 401 Unauthorized');
        echo ('NO AUTORIZADO: click <a href="http://'. $_SERVER['SERVER_NAME'] .'">aqui</a> para entrar');
    } else { // si esta logueado
        header('Content-Type: application/json');
        
        $mysql = Mysql::getInstance();
        $mysql->connect();

        $_method = (isset($_REQUEST["_method"]))? $_REQUEST["_method"] : null;
        $model = (isset($_REQUEST["model"]))? $_REQUEST["model"] : null;
        $codigo = (isset($_REQUEST["codigo"]))? $_REQUEST["codigo"] : null;
        $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;

        $proveedores = new Proveedores($mysql);
        $caja = new Caja($mysql);
        $pedidosService = new PedidosService(new Pedidos($mysql), 
                                            new ServiciosPedidos($mysql), 
                                            new Servicios($mysql), 
                                            $proveedores,
                                            $caja); 
        
        $proveedorModel = $proveedores->get($_SUCURSAL);
        
        $clientes = new Clientes($mysql);
        
        // dataTables Handler
        if(isset($_REQUEST["sEcho"])) {
            $entregado = (isset($_REQUEST["entregado"]) && !empty($_REQUEST["entregado"]))? $_REQUEST["entregado"] : 0;

            if($entregado != 2) {
                $fechaPedido = (isset($_REQUEST["fechaPedido"]))? $_REQUEST["fechaPedido"] : "";
                $out = $pedidosService->getPagedSorted($_REQUEST["sSearch"],
                                            $_REQUEST["iDisplayStart"],
                                            $_REQUEST["iDisplayLength"],
                                            $_REQUEST["sEcho"], 
                                            $entregado, $fechaPedido);
            } else {
                $out = $pedidosService->getPagedSortedTemplates($_REQUEST["sSearch"],
                                            $_REQUEST["iDisplayStart"],
                                            $_REQUEST["iDisplayLength"],
                                            $_REQUEST["sEcho"]);
            }
            
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
                
                $model = $pedidosService->get($codigo);

                $log_data = array(
                    'accion' => 'Eliminacion',
                    'codigoTalon' => $model['codigoTalon'],
                    'anticipo' => $model['anticipo'],
                    'codigo' => $model['codigo'],
                    'servicios' => $model['listaServicios'],
                    'anticipoAnterior' => 0,
                    'serviciosAnterior' => [],
                    'usuario' => $_SESSION['USER'],
                    'ip' => $_SESSION['USER_IP']
                );
                $log->info(serialize($log_data));

                echo('{"success" : true}');
            }


            // save (CREATE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( ($_method == null) && ($model != null) && ($codigo == null) ) {

                $modelData = json_decode($model, true);
                $conDebito = $modelData['conDebito'];
                $modelData = $pedidosService->create($modelData);

                // registro anticipo, solo si no es plantilla
                if($modelData["nombre"] == "") {
                    $anticipo = floatval($modelData['anticipo']);
                    //if($anticipo != 0) {

                        $caja->registrarCajaPedido(
                            $anticipo, 
                            $modelData['codigo'], 
                            "Anticipo operacion ". $proveedorModel["prefijoCodigo"] . $modelData['codigo'] .
                            " - Codigo Talon: ". $modelData['codigoTalon'],
                            $conDebito);
                    //}
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


                 $log_data = array(
                    'accion' => 'Modificacion',
                    'codigoTalon' => $modelData['codigoTalon'],
                    'anticipo' => $modelData['anticipo'],
                    'codigo' => $codigo,
                    'servicios' => array_filter($modelData['listaServicios'], function($row) { return (!$row['deleted']); }),
                    'anticipoAnterior' => $oldModelData['anticipo'],
                    'serviciosAnterior' => $oldModelData['listaServicios'],
                    'usuario' => $_SESSION['USER'],
                    'ip' => $_SESSION['USER_IP']
                );
                $log->info(serialize($log_data));

                if(!$modelData['soloImprimir']) {
                    $pedidosService->update($modelData);

                    if($modelData["nombre"] == "") { // si no es plantilla
                        $anticipo = floatval($modelData['anticipo']);
                        $diferenciaRegistrar = $anticipo - $oldModelData["anticipo"];

                        // if($diferenciaRegistrar != 0) { // si hay diferencia/anticipo, entonces lo registro
                            $caja->registrarCajaPedido(
                                $diferenciaRegistrar, 
                                $modelData['codigo'], 
                                "Correccion anticipo operacion ". $proveedorModel["prefijoCodigo"] . $modelData['codigo'] .
                                " - Codigo Talon: ". $modelData['codigoTalon']);
                        // }
                    }
                } else {
                    $oldModelData['imprimir'] = $modelData['imprimir'];
                    $oldModelData['soloImprimir'] = $modelData['soloImprimir'];
                    $modelData = $oldModelData;
                }

                
                if ($modelData['imprimir'] || $modelData['soloImprimir']) {
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