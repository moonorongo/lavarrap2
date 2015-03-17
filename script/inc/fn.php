<?php

    function swapDateFormat($fecha) {
        $aFecha = explode("-", $fecha);
        if (count($aFecha) != 0) {
            return $aFecha[2] ."/". $aFecha[1] ."/". $aFecha[0];
        } else {
            $aFecha = explode("/", $fecha);
            return $aFecha[2] ."-". $aFecha[1] ."-". $aFecha[0];
        }
    }



    function mes($mes) {
        $m = Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $m[$mes];
    }
    
    
            
?>