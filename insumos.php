<?php

/*
  Insumos Controller
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

        $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : null;
        $_method = (isset($_REQUEST["_method"])) ? $_REQUEST["_method"] : null;
        $model = (isset($_REQUEST["model"])) ? $_REQUEST["model"] : null;
        $codigo = (isset($_REQUEST["codigo"])) ? $_REQUEST["codigo"] : null;

        $insumos = new Insumos($mysql);
        $insumosService = new InsumosService($insumos);


        // dataTables Handler
        if (isset($_REQUEST["sEcho"])) {
            $out = $insumosService->getPagedSorted($_REQUEST["sSearch"], $_REQUEST["iSortCol_0"], $_REQUEST["sSortDir_0"], $_REQUEST["iDisplayStart"], $_REQUEST["iDisplayLength"], $_REQUEST["sEcho"], true);
            echo json_encode($out);
        }


        // insumosModelCRUD
        if ($action=="insumosModelCRUD") {
            // CRUD Handler - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
            // fetch (GET) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  
            if (($_method == null) && ($model == null) && ($codigo != null)) {
                $out = $insumos->get($codigo);
                echo json_encode($out);
            }


            // destroy (DELETE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if ((($_method != null) && ($_method == "DELETE")) && ($model == null) && ($codigo != null)) {
                $insumos->delete($codigo);
                echo('{"success" : true}');
            }


            // save (CREATE) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if (($_method == null) && ($model != null) && ($codigo == null)) {
                $modelData = json_decode($model, true);
                $insumos->create($modelData);
                echo('{"success" : true}');
            }


            // save(PUT) * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            if (($_method != null) && (($_method == "PUT")) && ($model != null) && ($codigo != null)) {
                $modelData = json_decode($model, true);
                $insumos->update($modelData);
                echo('{"success" : true}');
            }
        }

        if ($action=="addInsumo") {
            $cantidad = $_REQUEST["cantidad"];
            $insumos->add($codigo, $cantidad);
            echo('{"success" : true}');
        }

        $mysql->close();
    } // if logged    
?>