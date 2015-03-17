<?php
    class InsumosServicios {
        
        private $mysql = null;
                
        function __construct($mysql) {
            $this->mysql = $mysql;
        }
        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM insumosServicios ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $out = $row["codigo"] + 1;
                }
            } else {
                $out = 1;
            }
            return $out;
        }
        
        
        
        
        public function getByCodigoServicio($codigoServicio) {
            $out = Array();
            $result = $this->mysql->query("SELECT _is.*, i.descripcion as _descripcion FROM insumosServicios _is ". 
                    "INNER JOIN insumos i ON _is.codigoInsumo = i.codigo ".
                    "WHERE _is.codigoServicio = ". $codigoServicio);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        public function delete($codigo) {
            // esto no es lo mas optimo... 
            $stmt = $this->mysql->getStmt("DELETE FROM insumosServicios WHERE codigo =  ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        
        
        public function deleteByCodigoServicio($codigoServicio) {
            $stmt = $this->mysql->getStmt("DELETE FROM insumosServicios WHERE codigoServicio =  ?");
            $stmt -> bind_param("i", $codigoServicio);
            $stmt -> execute();
        }
        

        

        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO insumosServicios (codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
            $this->update($modelData);
        }

        
  
        
        public function update($modelData) {
            
            $sql = "UPDATE insumosServicios SET ". 
                   " codigoInsumo = ? ,". 
                   " codigoServicio = ? ,". 
                   " cantidad = ? WHERE codigo = ? ";
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("iidi", $modelData['codigoInsumo'], $modelData['codigoServicio'], $modelData['cantidad'], $modelData['codigo']);
            $stmt -> execute();

        }
    }

    
  

?>
