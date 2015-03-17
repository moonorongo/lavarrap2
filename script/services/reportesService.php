<?php 

    class ReportesService {
    
        private $reportes = null;
        private $caja = null;

        function __construct($reportes, $caja) {
            $this->reportes = $reportes;
            $this->caja = $caja;
        }
    
        public function reportePrincipal($month, $year) {
            $out = Array();
            $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
            for($day=1; $day <=$numDays; $day++) {
                $fecha = $year ."-". $month ."-". $day;
                $out[] = Array( "fecha" => $fecha, 
                                "facturado" => $this->reportes->facturadoDiario($fecha),
                                "cobrado" => $this->caja->cajaDiaria($fecha)); 
            }
            
            /* codigo anterior
            $lista = $this->reportes->reportePrincipalFacturado($fechaInicial, $fechaFinal);
            $diaAnterior = $lista[0]["fechaPedido"];
            $sumatoriaFacturado = 0;
            $out = Array();

            foreach($lista as $key => $item) {
                if($diaAnterior != $item["fechaPedido"]) {
                    $out[] = Array("fecha" => $diaAnterior, "facturado" => $sumatoriaFacturado,
                                   "cobrado" => $this->caja->cajaDiaria($diaAnterior)); 
                    if($diaAnterior == "2013-11-13") {
                        $aa = null;
                    }
                    $diaAnterior = $item["fechaPedido"];
                    $sumatoriaFacturado = 0;
                }
                
                if ($item["facturado"] != null) $sumatoriaFacturado += $item["facturado"];
                
                if($key == count($lista) - 1) {
                    $out[] = Array("fecha" => $item["fechaPedido"], 
                                   "facturado" => $sumatoriaFacturado,
                                   "cobrado" => $this->caja->cajaDiaria($item["fechaPedido"]));
                }
            }
            */
            return $out;
        }
        

        
        

        
        
        
        
    }    
?>