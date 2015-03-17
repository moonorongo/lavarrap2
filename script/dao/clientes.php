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
        

        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM clientes ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $out = $row["codigo"] + 1;
                }
            } else {
                $out = 1;
            }
            return $out;
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
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO clientes(codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
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
                               "label" => $nombres ." ". $apellido,
                               "value" => $nombres ." ". $apellido
                              );
                    $out[] = $tmpRow;
                }

                $stmt->close();    
                return $out;
                
            } else { // van todos los clientes activos.
                
                $stmt = $this->mysql->getStmt("SELECT codigo, nombres, apellido FROM clientes WHERE 
                    activo = 1 AND codigoSucursal = ". $this->codigoSucursal);

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