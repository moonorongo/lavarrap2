<?php
    class Insumos {
        
        private $mysql = null;
        private $columns = Array("descripcion");
        private $table = "insumos";
        private $codigoSucursal = null;
        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        

        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM insumos ORDER BY codigo desc limit 1");
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
            $result = $this->mysql->query("SELECT * FROM insumos WHERE activo = 1");
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        
        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO insumos(codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
            $this->update($modelData);
        }

        
        
        public function update($modelData) {
            
            $sql = "UPDATE insumos SET descripcion = ? WHERE codigo = ?";
  
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("si", $modelData['descripcion'], $modelData['codigo']);
            $stmt->execute();
        }


        
        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE insumos SET activo = 0 WHERE codigo =  ?");
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
        }
        

        
        
        public function get($codigo) {

            $stmt = $this->mysql->getStmt("SELECT descripcion FROM insumos WHERE codigo = ?");
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
            
            $stmt->bind_result($descripcion);
            

            $stmt->fetch();
            $out = array("codigo" => $codigo, "descripcion" => $descripcion);
            
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
        
            $out = Array();
            $mes = date('n');
            $year = date('Y');

            $sql =  "SELECT i.*,
                (SELECT SUM(cantidad) FROM insumosIngresos WHERE MONTH(fechaIngreso) = $mes AND YEAR(fechaIngreso) = $year  AND codigoSucursal = $this->codigoSucursal AND codigoInsumo = i.codigo) as _cantidad,
                (SELECT MAX(fechaIngreso) FROM insumosIngresos WHERE MONTH(fechaIngreso) = $mes AND codigoSucursal = $this->codigoSucursal AND codigoInsumo = i.codigo) as _ultimaFecha
                FROM insumos i WHERE $aBuscar ORDER BY ". $this->columns[$fieldOrder] ." $dirOrder LIMIT $start, $length";
            
            $result = $this->mysql->query($sql);

            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        
        
        public function count() {
            $condicionSucursal = " (true) "; //" codigoSucursal = ". $this->codigoSucursal;
            $result = $this->mysql->query("SELECT count(codigo) as total from ". $this->table ." WHERE ". $condicionSucursal);
            
            while ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }
  
        

        
        
        public function add($codigoInsumo, $cantidad) {
            
            $result = $this->mysql->query("SELECT codigo FROM insumosIngresos ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $codigo = $row["codigo"] + 1;
                }
            } else {
                $codigo = 1;
            }
            
            $sql = "INSERT INTO insumosIngresos (codigo, codigoInsumo, cantidad, codigoSucursal) VALUES (?, ?, ?, ?)";
  
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("iidi", $codigo, $codigoInsumo, $cantidad, $this->codigoSucursal);
            $stmt->execute();            
        }
        
        
        
    }

?>