<?php 

    class InsumosService {
    
        private $insumos = null;

        function __construct($insumos) {
            $this->insumos = $insumos;
        }
    
        
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $sEcho, $soloActivos) {
    
            $sEcho++;
            $lista = $this->insumos->getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos);
            
            $out = Array();
            $out["iTotalRecords"] = $this->insumos->count();
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();

            foreach($lista as $row) {
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => $row["descripcion"],
                    "1" => $row["_cantidad"],
                    "2" => $row["_ultimaFecha"]
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
        
        
    
    }

    
?>