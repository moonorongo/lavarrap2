<?php 

    class TareasService {

        private $serviciosPedidos = null;
        private $codigoSucursal = null;

        
        function __construct($serviciosPedidos) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->serviciosPedidos = $serviciosPedidos;
        }
    
        
        public function get($tareasSeleccionadas) {
            $out = Array();
            foreach($tareasSeleccionadas as $tarea) {
                $model = $this->serviciosPedidos->get($tarea);
                $out[] = $model;
            }
            return $out;
        }
        
        
        public function listAll($codigoEstado) {
    
            $lista = $this->serviciosPedidos->listAll($codigoEstado);
            
            $out = Array();
            $out["aaData"] = Array();

            foreach($lista as $row) {
                $_descripcionDerivacion = "";
                $_descripcionEstado = "";
                $classDerivacion = "";
                
                
                if(!is_null($row['codigoProveedor'])) {
                    if($row['codigoSucursal'] == $this->codigoSucursal) { // si es mi sucursal
                        if($row['codigoProveedor'] != $this->codigoSucursal) {
                            $_descripcionDerivacion = "Derivado a ". $row["_descripcionDerivacion"];
                            $classDerivacion = ($row["esSucursal"] == 1)? "tareaDerivada":"";
                        }
                    } else { // no es mi sucursal
                        if($row['codigoProveedor'] == $this->codigoSucursal) { // es una derivacion para mi
                            $_descripcionDerivacion = "Derivado desde ". $row["_descripcionSucursal"];
                        }
                    }
                }
                
                $_descripcionEstado = $row["_descripcionEstado"];
                $columnaCodigo = (is_null($row["codigoTalon"]) || empty($row["codigoTalon"]))? '('.str_pad($row["codigoPedido"], 8, '0', STR_PAD_LEFT).')' : str_pad($row["codigoTalon"], 8, '0', STR_PAD_LEFT);

                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "DT_RowClass" => $classDerivacion,
                    "0" =>  $row['prefijoCodigo']. $columnaCodigo,
                    "1" => $row["_fechaRetiro"],
                    "2" => $row["cantidad"] ." x ". $row["_descripcionServicio"],
                    "3" => $_descripcionDerivacion,
                    "4" => $_descripcionEstado,
                    "5" => $row['codigoProveedor']
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }

        
  }

?>