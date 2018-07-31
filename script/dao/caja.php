<?php
    class Caja {
        private $columns = Array("timestamp", "message");
        private $mysql = null;
        private $codigoSucursal = null;
        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }


        public function get($codigo) {
            $sql = "SELECT * FROM caja 
                    WHERE codigo = $codigo";
                
            $result = $this->mysql->query($sql);
            return $result->fetch_assoc();
        }


        public function listAll() {
            $sql = "SELECT codigo, observaciones FROM caja 
                    WHERE 
                        (observaciones like '%SVM%') OR
                        (observaciones like '%SCR%') OR
                        (observaciones like '%SZE%') OR
                        (observaciones like '%SBE%') OR
                        (observaciones like '%SMI%') OR
                        (observaciones like '%SCA%') OR
                        (observaciones like '%PAT%')";

            return $this->mysql->query($sql);
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
        
       
        public function listEgresosMes($fecha, $search = "") {
            if(empty($search)) {
                $sql = "
                    SELECT 
                        caja.*,
                        pedidos.codigoTalon, 
                        pedidos.codigo AS codigoPedido,
                        clientes.nombres, 
                        clientes.apellido
                    FROM caja 
                    LEFT JOIN pedidosCaja ON pedidosCaja.codigoCaja = caja.codigo
                    LEFT JOIN pedidos ON pedidosCaja.codigoPedido = pedidos.codigo
                    LEFT JOIN clientes ON pedidos.codigoCliente = clientes.codigo

                    WHERE month(fecha) = month('$fecha') 
                        AND year(fecha) = year('$fecha') 
                        AND caja.codigoSucursal = $this->codigoSucursal 
                    ORDER BY fecha";

            } else {
                $sql = "
                    SELECT 
                        caja.*,
                        clientes.nombres, 
                        clientes.apellido
                    FROM caja 
                    LEFT JOIN pedidosCaja ON pedidosCaja.codigoCaja = caja.codigo
                    LEFT JOIN pedidos ON pedidosCaja.codigoPedido = pedidos.codigo
                    LEFT JOIN clientes ON pedidos.codigoCliente = clientes.codigo

                    WHERE 
                    	( (UPPER(caja.observaciones) LIKE UPPER('%$search%')) OR 
                    	  (UPPER(clientes.nombres) LIKE UPPER('%$search%')) OR 
                    	  (UPPER(clientes.apellido) LIKE UPPER('%$search%')) ) AND
                    	caja.codigoSucursal = $this->codigoSucursal 
                	ORDER BY fecha DESC 
                	LIMIT 2000";
            }

                
            $out = Array();
            $result = $this->mysql->query($sql);
            while ($row = $result->fetch_assoc()) {
                $row['observaciones'] = $row['observaciones'] . ' - Cliente: '. $row['nombres'] .' '. $row['apellido'];

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


        public function editarCaja($codigo, $monto, $observaciones) {
            $stmt = $this->mysql->getStmt("UPDATE caja SET monto = ?, observaciones = ? WHERE codigo = ?");
            $stmt->bind_param("dsi", $monto, $observaciones, $codigo);
            $stmt->execute();
        }

        
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
        

        public function registrarCajaPedido($monto, $codigoPedido, $observaciones = "", $conDebito = 0) {
            $stmt = $this->mysql->getStmt("INSERT INTO caja(monto,codigoSucursal, observaciones, conDebito) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("disi", $monto, $this->codigoSucursal, $observaciones, $conDebito);
            $stmt->execute();
            $codigoCaja = $this->mysql->getLastId();

            $pedidosCajaDao = new PedidosCaja($this->mysql);
            $pedidosCajaDao->insertOrUpdate($codigoCaja, $codigoPedido);
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
        
        
        public function deleteByCodigoPedido($codigo) {
            $stmt = $this->mysql->getStmt("
                DELETE caja FROM caja 
                INNER JOIN pedidosCaja
                ON pedidosCaja.codigoCaja = caja.codigo
                WHERE pedidosCaja.codigoPedido = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();

            $stmt = $this->mysql->getStmt("
                DELETE FROM pedidosCaja
                WHERE pedidosCaja.codigoPedido = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("DELETE FROM caja WHERE codigo = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        



// LOG CAJA methods
        public function count($fecha, $sSearch) {
            $sSearch = strtoupper($sSearch);
            $searchCondition = " ( ";
            foreach ($this->columns as $key => $value) {
                $or = ($key == 0)? " ":" OR ";
                $searchCondition .= $or ."( UPPER(". $value .") LIKE UPPER('%". $sSearch ."%') ) ";
            }
            $searchCondition .= " ) ";
            
            $aBuscar = empty($sSearch)? " (1 = 1) " : $searchCondition;
            $whereFecha = empty($fecha)? " (1 = 1) " : " (DATE(timestamp) = '$fecha') ";

            $sql = 
                "SELECT 
                   count(id) AS total
                FROM  log4php_log
                WHERE 
                    $whereFecha
                    AND $aBuscar";
            
            $result = $this->mysql->query($sql);
            
            if ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }


        public function getPagedSorted($sSearch, $start, $length, $fecha) {
            $sSearch = strtoupper($sSearch);
            // Armo query busqueda en base a configuracion columns
            $searchCondition = " ( ";
            foreach ($this->columns as $key => $value) {
                $or = ($key == 0)? " ":" OR ";
                $searchCondition .= $or ."( UPPER(". $value .") LIKE UPPER('%". $sSearch ."%') ) ";
            }
            $searchCondition .= " ) ";
            
            $aBuscar = empty($sSearch)? " (1 = 1) " : $searchCondition;
            $whereFecha = empty($fecha)? " (1 = 1) " : " (DATE(timestamp) = '$fecha') ";
       
            $out = Array();
            $sql =  "SELECT 
                        timestamp, message
                    FROM  log4php_log
                    WHERE (1 = 1)
                      AND $whereFecha
                      AND $aBuscar 
                      AND sucursal = $this->codigoSucursal
                    ORDER BY timestamp DESC
                    LIMIT $start, $length";

            $stmt = $this->mysql->getStmt($sql);
            $stmt -> execute();
            $stmt -> bind_result($timestamp, $message);
            
            while($stmt -> fetch()) {
                $tmpRow = array(
                           "fecha" => date_create($timestamp)->format('d/m/Y'),
                           "message" => $message
                          );
                $out[] = $tmpRow;
            }
            
            $stmt -> close();
            return $out;
        }
  

        public function debitoToCaja($monto, $observaciones) {
            $montoDebito = -abs($monto);
            $montoCaja = abs($monto);

            $obsDebito = 'Extraccion del banco (DEBITO) ' . $observaciones;
            $obsCaja = 'Ingreso a caja (DEBITO) ' . $observaciones;

            $stmt = $this->mysql->getStmt("INSERT INTO caja(monto, observaciones, conDebito, codigoSucursal) VALUES (?, ?, 1, ". $this->codigoSucursal .")");
            $stmt->bind_param("ds", $montoDebito, $obsDebito);
            $stmt->execute();

            $stmt = $this->mysql->getStmt("INSERT INTO caja(monto, observaciones, conDebito, codigoSucursal) VALUES (?, ?, 0, ". $this->codigoSucursal .")");
            $stmt->bind_param("ds", $montoCaja, $obsCaja);
            $stmt->execute();
        } 

    }
?>