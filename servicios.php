<?php
/*
    Clientes Controller
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

        global $_SUCURSAL;
        
        $servicios = new Servicios($mysql);
        $serviciosService = new ServiciosService($servicios, new InsumosServicios($mysql), new Insumos($mysql)); 
        
        // dataTables Handler
        if(isset($_REQUEST["sEcho"])) {
            $fechaVigencia = (isset($_REQUEST["fechaVigencia"]))? $_REQUEST["fechaVigencia"] : null;
            $out = $serviciosService->getPagedSorted($_REQUEST["sSearch"],
                                        $_REQUEST["iSortCol_0"],
                                        $_REQUEST["sSortDir_0"],
                                        $_REQUEST["iDisplayStart"],
                                        $_REQUEST["iDisplayLength"],
                                        $_REQUEST["sEcho"], 
                                        true, $fechaVigencia);
            
            echo json_encode($out);
        }


        if($action=="nuevaFechaVigencia") {

            $fechaVigencia = (isset($_REQUEST["fechaVigencia"]))? $_REQUEST["fechaVigencia"] : null;
            $serviciosService->nuevaFechaVigencia($fechaVigencia);

/* esto corregiria algo que no es necesario corregir... asi que a la merda...
            $servicios = new Servicios($mysql);
            $pedidosService = new PedidosService( new Pedidos($mysql), 
                    new ServiciosPedidos($mysql), 
                    $servicios, 
                    new Proveedores($mysql));
  
            $listaPlantillas = $pedidosService->listTemplates();
            foreach($listaPlantillas as $plantilla) {
                // de cada plantilla listo los servicios
                // de cada servicioPedido,  busco POR NOMBRE el sevicio con nuevo precio
                // y actualizo el codigoServicio por el del nuevo que obtuve.
                $a = $plantilla;
                foreach($plantilla["listaServicios"] as $s) {
                    $servicioActualizado = $servicios->getByDescripcion($s["_descripcion"]);
                    $s["codigoServicio"] = $servicioActualizado["codigo"];
                    // aca, con $s modificado, actualizarlo...
                }
                
            }
*/
            
            
            echo '{ "success" : true }';
        }
        
        
        if($action=="serviciosModelCRUD") {
            // CRUD Handler - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
            // fetch (GET) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  
            if ( ($_method == null) && ($model == null) && ($codigo != null) ) {    
                $out = $serviciosService->get($codigo);
                echo json_encode($out);
            }


            // destroy (DELETE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( (($_method != null) && ($_method=="DELETE")) && ($model == null) && ($codigo != null) ) {
                $serviciosService->delete($codigo);
                echo('{"success" : true}');
            }


            // save (CREATE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( ($_method == null) && ($model != null) && ($codigo == null) ) {
                $modelData = json_decode($model, true);
                $modelData["codigoSucursal"] = $_SUCURSAL;
                $fechaVigencia = $servicios->listFechasVigencia();
                $modelData["fechaVigencia"] = $fechaVigencia[0]["fechaVigencia"];
                
                $serviciosService->create($modelData);
                echo('{"success" : true}'); 
            }


            // save(PUT) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ( ($_method != null) && (($_method=="PUT")) && ($model != null) && ($codigo != null) ) {
                $modelData = json_decode($model, true);
                $modelData["codigoSucursal"] = $_SUCURSAL;
                $success = $serviciosService->update($modelData);
                echo('{"success" : '. $success .'}');
            }    
        } // serviciosModelCRUD
        
        
        
        if($action=="getListaInsumos") {
            $out = $serviciosService->getListaInsumos();
            echo json_encode($out);
        } // getListaInsumos
        


        if($action=="copiarServicios") {
            $codigoSucursal = $_REQUEST["codigoSucursal"];
            $out = $serviciosService->copiarServicios($codigoSucursal);
            echo ($out);
        } // copiarServicios
        
        

        $mysql->close();    
    } // if logged    
?>