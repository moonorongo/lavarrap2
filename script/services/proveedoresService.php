<?php 

    class ProveedoresService {
    
        private $proveedores = null;

        function __construct($proveedores) {
            $this->proveedores = $proveedores;
        }
    
        
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $sEcho, $soloActivos) {
    
            $sEcho++;
            $lista = $this->proveedores->getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos);
            
            $out = Array();
            $out["iTotalRecords"] = $this->proveedores->count();
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();

            foreach($lista as $row) {
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => $row["descripcion"],
                    "1" => ($row["esSucursal"] == 1)? "<i class=\"icon-check\"></i>":"<i class=\"icon-check-empty\"></i>"
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
        
        
    
    }

    
?>