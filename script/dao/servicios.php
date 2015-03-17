<?php
    class Servicios {
        
        private $mysql = null;
        private $columns = Array("descripcion", "valor");
        private $table = "servicios";
        private $codigoSucursal = null;
                
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        

        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM servicios ORDER BY codigo desc limit 1");
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
            $result = $this->mysql->query("SELECT * FROM servicios WHERE activo = 1 
                AND codigoSucursal = ". $this->codigoSucursal ." ORDER BY descripcion ");
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }


        public function listAllVigente() {
            $out = Array();
            $result = $this->mysql->query("SELECT * FROM servicios WHERE activo = 1 
                AND fechaVigencia = (SELECT MAX(fechaVigencia) FROM servicios WHERE codigoSucursal = ". $this->codigoSucursal .") 
                AND codigoSucursal = ". $this->codigoSucursal ." ORDER BY descripcion ");
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        public function listFechasVigencia() {
            $out = Array();
            $result = $this->mysql->query("SELECT DISTINCT fechaVigencia FROM servicios 
                WHERE codigoSucursal = ". $this->codigoSucursal ." ORDER BY fechaVigencia DESC");
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO servicios (codigo) VALUES (?)"; 
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("i", $newId);
            $stmt -> execute();

            $modelData["codigo"] = $newId;
            $this->update($modelData);
            
            return $modelData;
        }

        

        
        
        
        public function update($modelData) {
            
            $sql = "UPDATE servicios SET ". 
                   " descripcion = ? ,".
                   " codigoSucursal = ? ,".
                   " fechaVigencia = ? ,".
                   " valor = ? WHERE codigo = ? " ;
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("sisdi", $modelData['descripcion'], $modelData['codigoSucursal'], $modelData['fechaVigencia'], $modelData['valor'], $modelData['codigo']);
            $stmt -> execute();
        }


        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE servicios SET activo = 0 WHERE codigo =  ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }

        
        
/* esta ridiculez era para la correccion de servicios, que no se utilizara.        
        
        public function getByDescripcion($descripcion) {
            $fechaVigencia = $this->getLastFechaVigencia();
            $stmt = $this->mysql->getStmt("SELECT codigo, descripcion, valor FROM servicios WHERE descripcion = ? ".
                                            " AND fechaVigencia = ? AND codigoSucursal = ". $this->codigoSucursal );
            
            $stmt -> bind_param("s", $descripcion);
            $stmt -> bind_param("s", $fechaVigencia);
            $stmt -> execute();
            
            $stmt -> bind_result($codigo, $descripcion, $valor);
            

            $stmt -> fetch();
            $out = array("codigo" => $codigo,
                           "descripcion" => $descripcion,
                           "valor" => $valor,
                           "fechaVigencia" => $fechaVigencia
                          );
            
            $stmt -> close();
            return $out;
        }
*/

        
        
        
        public function get($codigo) {
            $stmt = $this->mysql->getStmt("SELECT descripcion, valor, fechaVigencia FROM servicios WHERE codigo = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
            
            $stmt -> bind_result($descripcion, $valor, $fechaVigencia);
            

            $stmt -> fetch();
            $out = array("codigo" => $codigo,
                           "descripcion" => $descripcion,
                           "valor" => $valor,
                           "fechaVigencia" => $fechaVigencia
                          );
            
            $stmt -> close();
            return $out;
        }
        
        
        
        
        
        
        public function getLastFechaVigencia() {
            $stmt = $this->mysql->getStmt("SELECT MAX(fechaVigencia) AS fechaVigencia FROM servicios WHERE codigoSucursal = ". $this->codigoSucursal);
            $stmt -> execute();
            
            $stmt -> bind_result($fechaVigencia);
            $stmt -> fetch();
            $stmt -> close();
            return $fechaVigencia;
        }
        
        
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos, $fechaVigencia) {
            
            $condicionSucursal = " codigoSucursal = ". $this->codigoSucursal;
            $lastFechaVigencia = $this->getLastFechaVigencia();
            $condicionfechaVigencia = ($fechaVigencia == null)? " fechaVigencia = '$lastFechaVigencia' " : " fechaVigencia = '$fechaVigencia' ";
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
                    ." AND $condicionfechaVigencia "
                    ." ORDER BY ". $this->columns[$fieldOrder] ." ". $dirOrder ." LIMIT ". $start .','. $length;

            $result = $this->mysql->query($sql);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        
        
        public function count($soloActivos, $fechaVigencia) {
            $condicionSucursal = " codigoSucursal = ". $this->codigoSucursal;
            $lastFechaVigencia = $this->getLastFechaVigencia();
            $condicionfechaVigencia = ($fechaVigencia == null)? " fechaVigencia = '$lastFechaVigencia' " : " fechaVigencia = '$fechaVigencia' ";
            $condicionActivos = ($soloActivos)? " AND activo = 1 ":"";
            
            $result = $this->mysql->query("SELECT count(codigo) as total from ". $this->table 
                ." WHERE $condicionSucursal "
                ." AND $condicionfechaVigencia "
                ." $condicionActivos ");
            
            while ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }
        

        
        public function isUsed($codigoServicio) {
           
            $result = $this->mysql->query("SELECT COUNT(codigo) as cantidad FROM serviciosPedidos 
                WHERE codigoServicio = $codigoServicio");
            
            while ($row = $result->fetch_assoc()) {
                $out = $row["cantidad"];
            }
            return ($out == 0)? false:true;
        }
        
        
        
        
    }

    
    
?>