<?php 
    $fecha = (isset($_REQUEST["fecha"]))? $_REQUEST["fecha"] : "";
    $sEcho = $_REQUEST["sEcho"] + 1;
    $sSearch = $_REQUEST["sSearch"];

    $lista = $caja->getPagedSorted($sSearch,
                                $_REQUEST["iDisplayStart"],
                                $_REQUEST["iDisplayLength"],
                                $fecha);
    
    $out = Array();
    $out["iTotalRecords"] = $caja->count($fecha, $sSearch);
    $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
    $out["sEcho"] = $sEcho;
    $out["aaData"] = array_map(function($row) {
        $log_data = unserialize($row['message']);

        return array(
            "0" => $row['fecha'],
            "1" => $log_data['accion'],
            "2" => $log_data['codigoCaja'],
            "3" => $log_data['monto'],
            "4" => $log_data['observaciones'],
            "5" => $log_data['montoAnterior'],
            "6" => $log_data['observacionesAnterior'],
            "7" => $log_data['usuario'],
            "8" => $log_data['ip']
        );
    }, $lista);

    echo json_encode($out);