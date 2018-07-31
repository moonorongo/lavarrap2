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
        


        public function reporteCajaFacturado($mes, $anio) {
  
            $listMovimientos = $this->caja->listMovimientosVentas($mes, $anio);
            $listMovsGrouped = array();


            foreach ($listMovimientos as $item) {
                
                if(strpos($item['observaciones'], 'operacion') !== false) {
                    preg_match('/(?:\d*\.)?\d+/', $item['observaciones'], $matched);
                    $pedidoId = $matched[0];

                    if(array_key_exists($pedidoId, $listMovsGrouped)) {
                        $listMovsGrouped[$pedidoId]['monto'] += $item['monto'];
                    } else {
                        $listMovsGrouped[$pedidoId] = array( 
                                                        'monto' => $item['monto'],
                                                        'fecha' => $item['fecha']
                                                      );
                    }
                }
            }


            $out = array();

            foreach ($listMovsGrouped as $key => $value) {
                $rowData = $this->pedidos->getMontoPedido($key);
                $out[$key] = array(
                    'fechaCaja' => $value['fecha'],
                    'fechaPedido' => $rowData['fechaPedido'],
                    'nombreApellido' => $rowData['nombreApellido'],
                    'montoFacturado' => $rowData['facturado'],
                    'montoCobrado' => $value['monto']
                );
            }
            return $out;
        }
    }    
?>