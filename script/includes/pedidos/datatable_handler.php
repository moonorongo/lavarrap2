<?php 
    $fecha = (isset($_REQUEST["fecha"]))? $_REQUEST["fecha"] : "";
    $sEcho = $_REQUEST["sEcho"] + 1;
    $sSearch = $_REQUEST["sSearch"];

    $lista = $pedidos->getPagedSortedLogs($sSearch,
                                $_REQUEST["iDisplayStart"],
                                $_REQUEST["iDisplayLength"],
                                $fecha);
    
    $out = Array();
    $out["iTotalRecords"] = $pedidos->countLogs($fecha, $sSearch);
    $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
    $out["sEcho"] = $sEcho;
    $out["aaData"] = array_map(function($row) {
        $log_data = unserialize($row['message']);

        $totalServicios = 0;
        $totalServiciosAnterior = 0;

        if(!empty($log_data['servicios'])) {
            $totalServicios = array_reduce($log_data['servicios'], function($i, $row) {
                return $i += $row['_subTotal'];
            });
        }
        if(!empty($log_data['serviciosAnterior'])) {
            $totalServiciosAnterior = array_reduce($log_data['serviciosAnterior'], function($i, $row) {
                return $i += $row['_subTotal'];
            });
        }

        return array(
            "0" => $row['fecha'],
            "1" => $log_data['accion'],
            "2" => $log_data['codigoTalon'],
            "3" => $log_data['codigo'],
            "4" => $log_data['anticipo'],
            "5" => $log_data['anticipoAnterior'],
            "6" => $totalServicios,
            "7" => $totalServiciosAnterior, 
            "8" => $log_data['usuario'],
            "9" => $log_data['ip']
        );
    }, $lista);

    echo json_encode($out);