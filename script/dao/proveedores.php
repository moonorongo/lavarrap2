<?php
    class Proveedores {
        
        private $mysql = null;
        private $columns = Array("descripcion", "esSucursal");
        private $table = "proveedores";
        private $codigoSucursal = null;

        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        

        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM proveedores ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $out = $row["codigo"] + 1;
                }
            } else {
                $out = 1;
            }
            return $out;
        }
        
        
        
        
        public function listAll($excludeMe = false) {
            $condicionExclusion = ($excludeMe)? " AND codigo <> ". $this->codigoSucursal : "";
            $soloUserJabones = ($this->codigoSucursal != 9)? " AND codigo <> 9 " : "";
            $out = Array();
            $result = $this->mysql->query("SELECT * FROM proveedores WHERE activo = 1". $condicionExclusion . $soloUserJabones);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        
        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO proveedores(codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
            $this->update($modelData);
        }

        
        
        
        public function update($modelData) {
            
            $sql = "UPDATE proveedores SET descripcion = ?, direccion = ?, zona = ?, telefono = ?, esSucursal = ? WHERE codigo = ?";
  
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("ssssii", 
                    $modelData['descripcion'], 
                    $modelData['direccion'], 
                    $modelData['zona'], 
                    $modelData['telefono'], 
                    $modelData['esSucursal'], 
                    $modelData['codigo']);
            $stmt->execute();
        }


        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE proveedores SET activo = 0 WHERE codigo =  ?");
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
        }
        

        
        
        public function get($codigo) {

            $stmt = $this->mysql->getStmt("SELECT descripcion, esSucursal, prefijoCodigo, direccion, zona, telefono FROM proveedores WHERE codigo = ?");
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
            
            $stmt->bind_result($descripcion, $esSucursal, $prefijoCodigo, $direccion, $zona, $telefono);
            
            $stmt->fetch();
            $out = array("codigo" => $codigo, 
                        "descripcion" => $descripcion, 
                        "esSucursal" => $esSucursal, 
                        "direccion" => $direccion,
                        "zona" => $zona,
                        "telefono" => $telefono,
                        "prefijoCodigo" => $prefijoCodigo);
            
            $stmt->close();
            return $out;
        }
        
        
        
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos) {
        
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

            $soloUserJabones = ($this->codigoSucursal != 9)? " AND codigo <> 9 " : "";

            $out = Array();

            $sql =  "SELECT * FROM ". $this->table 
                    ." WHERE ". $aBuscar 
                    . $soloUserJabones
                    ." ORDER BY ". $this->columns[$fieldOrder] ." ". $dirOrder ." LIMIT ". $start .','. $length;
                    
            $result = $this->mysql->query($sql);


            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        
        
        public function count() {
            $soloUserJabones = ($this->codigoSucursal != 9)? " WHERE codigo <> 9 " : "";

            $result = $this->mysql->query("SELECT count(codigo) as total from ". $this->table . $soloUserJabones);
            
            while ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }
        
    }

    
    
?>