<?php 

    class ReportesService {
    
        private $reportes = null;
        private $caja = null;
        private $pedidos = null;

        function __construct($reportes, $caja, $pedidos) {
            $this->reportes = $reportes;
            $this->caja = $caja;
            $this->pedidos = $pedidos;
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
            
            return $out;
        }
        


    }    
?>