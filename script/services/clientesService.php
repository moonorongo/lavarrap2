<?php 

    
    class ClientesService {
        private $clientes = null;

        function __construct($clientes) {
            $this->clientes = $clientes;
        }
    
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $sEcho, $soloActivos) {
    
            $sEcho++;
            $lista = $this->clientes->getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos);
            
            $out = Array();
            $out["iTotalRecords"] = $this->clientes->count();
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();
            
            foreach($lista as $row) {
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => $row["nombres"],
                    "1" => $row["apellido"],
                    "2" => $row["direccion"],
                    "3" => $row["telefono"]
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
        
        
    
    }
   
?>