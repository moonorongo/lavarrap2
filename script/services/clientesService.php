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
/*                
                $cumple = "";
                $year = "";
                if($row["fechaNacimiento"] != "") {
                    $currentDay = date('Y-m-d');
                    $aCurrentDay = explode('-',date('Y-m-d'));
                    $year = $aCurrentDay[0];
                    
                    $cumple = explode('-', $row["fechaNacimiento"]);
                    $cumple = $year ."-". $cumple[1] ."-". $cumple[0];

                    if($currentDay > $cumple) $year = $aCurrentDay[0] + 1;
                    
                    $cumple = explode('-', $row["fechaNacimiento"]);
                    $cumple = $cumple[2] ."/". $cumple[1] ."/". $year;
                }
*/                
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

    
/*
private int sEcho;
    private int iTotalRecords;
    private int iTotalDisplayRecords;
    private ArrayList<HashMap<String,String>> aaData;

*/
    
?>