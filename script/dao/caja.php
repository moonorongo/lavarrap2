<?php
    class Caja {
        
        private $mysql = null;
        private $codigoSucursal = null;
        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        

        public function cajaDiaria($fecha) {
            $sql =  "SELECT SUM(monto) as sumaCobrado 
                     FROM caja WHERE DATE(fecha) = DATE('$fecha') AND 
                     codigoSucursal = $this->codigoSucursal AND 
                     esSaldoInicialMes = 0 AND
                     LEFT(observaciones, 5) <> '*EXT*'";
            
            $result = $this->mysql->query($sql);
            if($row = $result->fetch_assoc()) {
                return floatval($row['sumaCobrado']);
            } else {
                return 0;
            }
        }
        


        public function listMovimientosVentas($mes, $anio) {
            $sql = "SELECT monto, observaciones, cast(fecha as date) as fecha
                      FROM caja 
                      WHERE
                      ( (MONTH(fecha) = $mes) AND (YEAR(fecha) = $anio) ) AND
                      (codigoSucursal = $this->codigoSucursal) AND
                      (LEFT(observaciones, 5) <> '*EXT*') AND
                      (esSaldoInicialMes = 0) 
                      ORDER BY fecha";

            $out = array();
            $result = $this->mysql->query($sql);
            while($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;

        }
        
        
        
        public function listEgresosMes($fecha) {
            
            $sql = "SELECT * FROM caja WHERE month(fecha) = month('$fecha') 
                AND year(fecha) = year('$fecha') 
                AND codigoSucursal = $this->codigoSucursal ORDER BY fecha";
                
            $out = Array();
            $result = $this->mysql->query($sql);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        public function listEgresosDia($fechaInicial, $fechaFinal = null) {
            $sql = "";
            if(is_null($fechaFinal)) {
                $sql = "SELECT * FROM caja WHERE fecha = '$fechaInicial'  ORDER BY fecha";
            } else {
                $sql = "SELECT * FROM caja WHERE fecha >= '$fechaInicial' AND fecha <= '$fechaFinal' ORDER BY fecha";
            }
                
            $out = Array();
            $result = $this->mysql->query($sql);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        public function diferenciaDiaria($fecha) {}
        
        public function diferenciaMensual($fecha) {}
        
        
        public function registrarIngreso($monto, $observaciones = "") {
            $monto = abs($monto);
            $stmt = $this->mysql->getStmt("INSERT INTO caja(monto,codigoSucursal, observaciones) VALUES (?, ?, ?)");
            $stmt -> bind_param("dis", $monto, $this->codigoSucursal, $observaciones);
            $stmt -> execute();
        }

        
        public function registrarEgreso($monto, $observaciones = "") {
            $monto = -abs($monto);
            $stmt = $this->mysql->getStmt("INSERT INTO caja(monto,codigoSucursal, observaciones) VALUES (?, ?, ?)");
            $stmt -> bind_param("dis", $monto, $this->codigoSucursal, $observaciones);
            $stmt -> execute();
        }
        
        
        
        
        
        
        public function obtenerSaldoMes($year, $month) {
            $sql = "SELECT SUM(monto) AS monto FROM caja WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year' 
                AND codigoSucursal = $this->codigoSucursal";
            
            $result = $this->mysql->query($sql);
            $out = $result->fetch_assoc();
            return $out;            
        }
        
        
                
                
        
        
        
        public function registrarSaldoInicial($fecha, $monto) {
            $saldoInicial = $this->getSaldoInicial($fecha);
            $fecha = explode("-", $fecha);
            $fecha = $fecha[0] ."-".$fecha[1] ."-01 00:00:00";
            $monto = $monto;

            $sql = "";
            
            if (is_null($saldoInicial)) {
                $sql = "INSERT INTO caja(monto,codigoSucursal, observaciones, fecha, esSaldoInicialMes) VALUES (?, ?, 'Saldo inicial $fecha', '$fecha', 1)";
            } else {
                $sql = "UPDATE caja SET monto = ?, codigoSucursal = ? WHERE fecha = '$fecha' AND esSaldoInicialMes = 1 AND codigoSucursal = ". $this->codigoSucursal;
            }
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("di", $monto, $this->codigoSucursal);
            return $stmt->execute();
        }
        
        
        

        
        public function getSaldoInicial($fecha) {
            $timestamp = explode(" ", $fecha);
            $fecha = $timestamp[0]; // venga como venga, tomo solo la fecha

            $fecha = explode("-", $fecha);
            $fecha = $fecha[0] ."-".$fecha[1] ."-01 00:00:00";
            $result =  $this->mysql->query("SELECT monto FROM caja WHERE fecha = '$fecha' AND esSaldoInicialMes = 1 AND codigoSucursal = ". $this->codigoSucursal);
            $out = $result->fetch_assoc();
            return $out;
        }
        
        
        
        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("DELETE FROM caja WHERE codigo = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        


        
        
        
        
    }
?>