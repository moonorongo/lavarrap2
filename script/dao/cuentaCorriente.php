<?php
    class CuentaCorriente{
        
        private $mysql = null;
        private $codigoSucursal = null;
                
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }

        
        
        public function create($modelData) {
        
            $sql = "INSERT INTO cuentaCorriente (codigoCliente, codigoPedido, monto) VALUES (?, ?, ?)"; 
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("iid", $modelData['codigoCliente'], 
                                      $modelData['codigoPedido'], 
                                      $modelData['monto']);
            $stmt->execute();
            $modelData["codigo"] = $this->mysql->getDb()->insert_id;
            
            return $modelData;
        }

        
        
        public function update($modelData) {
            
//            $sql = "UPDATE cuentaCorriente SET 
//                    codigoCliente = ? ,
//                    codigoPedido = ?, 
//                    monto = ? WHERE codigo = ?"; 
//            
//            $stmt = $this->mysql->getStmt($sql);
//            $stmt->bind_param("iidi", $modelData['codigoCliente'], 
//                                      $modelData['codigoPedido'], 
//                                      $modelData['monto'],
//                                      $modelData['codigo']);
//            $stmt->execute();
        }

        
        
        
        public function updateMonto($codigo, $monto) {
            $sql = "UPDATE cuentaCorriente SET monto = $monto WHERE codigo = $codigo"; 
            //$stmt = $this->mysql->query($sql);
            //$stmt->bind_param("di", $modelData['monto'], $modelData['codigo']);
            return $this->mysql->query($sql);
        }
        
        
        

        
        public function delete($codigo) {
//            $stmt = $this->mysql->getStmt("UPDATE pedidos SET activo = 0 WHERE codigo = ?");
//            $stmt -> bind_param("i", $codigo);
//            $stmt -> execute();
        }
        


        
        
        public function get($codigo) {
            
            $stmt = $this->mysql->getStmt("SELECT * FROM cuentaCorriente cc WHERE cc.codigo = ?");
            
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
            
            $stmt -> bind_result($codigo, $codigoCliente, $codigoPedido, $monto);
            

            $stmt -> fetch();
            $out = array("codigo" => $codigo,
                           "codigoCLiente" => $codigoCliente,
                           "codigoPedido" => $codigoPedido,
                           "monto" => $codigoCliente );
            
            $stmt -> close();
            return $out;
        }
        

        
        
        public function getSaldoCliente($cc) {
            $stmt = $this->mysql->getStmt("SELECT * FROM cuentaCorriente WHERE codigoCliente = $cc AND codigoPedido IS NULL");
            
            $stmt -> execute();
            $stmt -> bind_result($codigo, $codigoCliente, $codigoPedido, $monto);
            $stmt -> fetch();

            $out = array("codigo" => $codigo,
                           "codigoCLiente" => $codigoCliente,
                           "codigoPedido" => $codigoPedido,
                           "monto" => $monto );

            $stmt -> close();
            return $out;
        }
        

        
        
        public function listAll($codigoCliente, $month = -1, $year = -1) {
            
            // si no le pongo fecha lista solo los q falta pagar...
            $condicionFecha = ($month != -1)? " ( MONTH(p.fechaPedido) = $month AND YEAR(p.fechaPedido) = $year ) " : " cc.monto != 0 ";
            
            $sql = "SELECT IF(p.codigo IS NULL, 0, p.codigo) AS codigo, p.fechaRetiro, SUM(sp.cantidad * s.valor) AS _valor, cc.monto,
                    cc.codigo AS codigoCuentaCorriente
                    FROM cuentaCorriente cc 
                    LEFT JOIN pedidos p ON cc.codigoPedido = p.codigo
                    LEFT JOIN serviciosPedidos sp ON sp.codigoPedido = p.codigo
                    LEFT JOIN servicios s ON sp.codigoServicio = s.codigo
                    WHERE ( (sp.codigoEstado = 5 AND p.codigoCliente = $codigoCliente AND $condicionFecha ) OR 
                          (cc.codigoPedido IS NULL AND p.codigoCliente IS NULL AND cc.codigoCliente = $codigoCliente) )
                    GROUP BY p.codigo
                    ORDER BY p.codigo ASC";

            $out = Array();
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> execute();
            $stmt -> bind_result($codigo, $fechaRetiro, $_valor, $monto, $codigoCuentaCorriente);
            
            while($stmt -> fetch()) {
                $tmpRow = array("codigo" => $codigo,
                           "fechaRetiro" => $fechaRetiro,
                           "_valor" => $_valor,
                           "monto" => $monto,
                           "codigoCuentaCorriente" => $codigoCuentaCorriente );
                $out[] = $tmpRow;
            }
            
            $stmt -> close();

            return $out;
        }               

        
        
        
        
        
        public function eliminarSaldos($codigoCliente) {
            $stmt = $this->mysql->getStmt("DELETE FROM cuentaCorriente WHERE codigoCliente = ? AND codigoPedido IS NULL");
            $stmt -> bind_param("i", $codigoCliente);
            return $stmt -> execute();
        }

        
        
    
    }


    

    
    
    
?>