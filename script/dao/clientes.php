<?php
    class Clientes {
        
        private $mysql = null;
        private $columns = Array("nombres","apellido","direccion","telefono");
        private $table = "clientes";
        private $codigoSucursal = null;
        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        

        
        public function listAll() {
            $out = Array();
            $result = $this->mysql->query("SELECT * FROM clientes WHERE activo = 1 AND codigoSucursal = ". $this->codigoSucursal);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        
        public function create($modelData) {
            $sql = "INSERT INTO clientes (nombres) VALUES (null)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $this->mysql->getLastId();
            $this->update($modelData);
            return $modelData["codigo"];
        }

        
        
        public function update($modelData) {
            
            $sql = "UPDATE clientes SET ". 
                   " nombres = ? , ".
                   " apellido = ? , ".
                   " direccion = ? , ".
                   " fechaNacimiento = ? , ".
                   " tieneCuentaCorriente = ? , ".
                   " codigoSucursal = ? , ".                    
                   " telefono = ? ".
                   " WHERE codigo = ? ";
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("ssssiisi", 
                    $modelData['nombres'],
                    $modelData['apellido'],
                    $modelData['direccion'],
                    $modelData['fechaNacimiento'],
                    $modelData['tieneCuentaCorriente'],
                    $this->codigoSucursal, 
                    $modelData['telefono'],
                    $modelData['codigo']);
            
            $stmt -> execute();
        }


        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE clientes SET activo = 0 WHERE codigo =  ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        

        
        
        public function get($codigo) {

            $stmt = $this->mysql->getStmt("SELECT nombres, apellido, direccion, telefono, fechaNacimiento, tieneCuentaCorriente FROM clientes WHERE codigo = ?");
            $stmt->bind_param("i",$codigo);
            $stmt->execute();
            
            $stmt->bind_result($nombres, $apellido, $direccion, $telefono, $fechaNacimiento, $tieneCuentaCorriente);

            $stmt->fetch();
            $out = array("codigo" => $codigo,
                           "nombres" => $nombres,
                           "apellido" => $apellido,
                           "direccion" => $direccion,
                           "telefono" => $telefono,
                           "fechaNacimiento" => $fechaNacimiento,
                           "tieneCuentaCorriente" => $tieneCuentaCorriente);
            $stmt->close();            
            return $out;
        }
        
        
        
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos) {
        
            $condicionSucursal = " codigoSucursal = ". $this->codigoSucursal;
            $sSearch = strtoupper($sSearch);
            
            // Armo query busqueda en base a configuracion columns
            $searchCondition = " ( ";
            foreach ($this->columns as $key => $value) {
                $or = ($key == 0)? " ":" OR ";
                $searchCondition .= $or ."(UPPER(". $value .") LIKE '%". $sSearch ."%') ";
            }
            $searchCondition .= " ) ";
            
            $aBuscar = (strlen($sSearch) == 0)? " (1 = 1) " : $searchCondition;
            $aBuscar .= ($soloActivos)? " AND activo = 1 ":"";
        
            $out = Array();

            $sql =  "SELECT * FROM ". $this->table ." WHERE ". $aBuscar ." AND ". $condicionSucursal  
                    ." ORDER BY ". $this->columns[$fieldOrder] ." ". $dirOrder ." LIMIT ". $start .','. $length;
                    
            $result = $this->mysql->query($sql);


            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        
        
        public function count() {
            $condicionSucursal = " codigoSucursal = ". $this->codigoSucursal;
            $result = $this->mysql->query("SELECT count(codigo) as total from ". $this->table ." WHERE ". $condicionSucursal);
            
            while ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }
        
        

        public function listByNombre($term = null) {
            $out = array();
            if($term != null) {
                
                $stmt = $this->mysql->getStmt("SELECT codigo, nombres, apellido FROM clientes WHERE 
                    ((nombres LIKE ?) OR (apellido LIKE ?)) AND codigoSucursal = ". $this->codigoSucursal);

                $a = "%$term%";
                $b = "%$term%";

                $stmt->bind_param("ss",$a , $b);
                $stmt->execute();

                $stmt->bind_result($codigo, $nombres, $apellido);

                while($stmt->fetch()) {
                    $tmpRow = array("id" => $codigo,
                               "value" => $nombres ." ". $apellido);
                    $out[] = $tmpRow;
                }

                $stmt->close();    
                return $out;
                
            } else { // van todos los clientes activos.
                     // que hayan hecho pedido en los ultimos 6 meses

                $sqlClientes = "select distinct
                                    clientes.codigo, 
                                    clientes.nombres,
                                    clientes.apellido
                                from 
                                    clientes
                                join pedidos on pedidos.codigoCliente = clientes.codigo
                                where 
                                  (pedidos.fechaPedido > DATE_SUB(NOW(), INTERVAL 6 MONTH)) and 
                                  (clientes.codigoSucursal = ". $this->codigoSucursal .") and
                                  (clientes.activo = 1)
                                order by 3,2"; 
                                
                $stmt = $this->mysql->getStmt($sqlClientes);

                $stmt->execute();
                $stmt->bind_result($codigo, $nombres, $apellido);

                while($stmt->fetch()) {
                    $tmpRow = array("id" => $codigo,
                               "text" => $nombres ." ". $apellido
                              );
                    $out[] = $tmpRow;
                }

                $stmt->close();    
                return $out;
            }

        }
        
        
    }

    
    
?>