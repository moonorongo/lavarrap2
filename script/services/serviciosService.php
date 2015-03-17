<?php 

    class ServiciosService {
    
        private $servicios = null;
        private $insumosServicios = null;
        private $insumos = null;
        private $codigoSucursal = null;

        function __construct($servicios, $insumosServicios, $insumos) {
            $this->servicios = $servicios;
            $this->insumosServicios = $insumosServicios;
            $this->insumos = $insumos;
            
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
        }
    
        
        
        public function getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $sEcho, $soloActivos, $fechaVigencia) {
    
            $sEcho++;
            $lista = $this->servicios->getPagedSorted($sSearch, $fieldOrder, $dirOrder, $start, $length, $soloActivos, $fechaVigencia);
            
            $out = Array();
            $out["iTotalRecords"] = $this->servicios->count($soloActivos, $fechaVigencia);
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();

            foreach($lista as $row) {
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => $row["descripcion"],
                    "1" => $row["valor"]
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
        
        
        public function delete($codigo) {
            $this->insumosServicios->deleteByCodigoServicio($codigo);
            $this->servicios->delete($codigo);
        }
        
            

        
        
        public function get($codigo) {
            $out = $this->servicios->get($codigo);
            $out["listaInsumos"] = $this->insumosServicios->getByCodigoServicio($codigo);
            $out["listaInsumosCombo"] = $this->insumos->listAll();
            return $out;
        }
        
        
        public function getListaInsumos() {
            $out["listaInsumosCombo"] = $this->insumos->listAll();
            return $out;
        }

        
        public function create($model) {
            $model = $this->servicios->create($model);
            foreach($model["listaInsumos"] as $insumo):
                $insumo["codigoServicio"] = $model["codigo"];
                if(!$insumo["deleted"]) $this->insumosServicios->create($insumo);
            endforeach;
        }
        
        
        public function update($model) {
            if (!$this->servicios->isUsed($model["codigo"])) {
                $this->servicios->update($model);
                foreach($model["listaInsumos"] as $insumo):
                    // condicion borrado
                    if(($insumo["deleted"]) && ($insumo["codigo"] != -1)) {
                        $this->insumosServicios->delete($insumo["codigo"]);
                    }
                    
                    // condicion actualizado
                    if((!$insumo["deleted"]) && ($insumo["codigo"] != -1)) {
                        $this->insumosServicios->update($insumo);
                    }
                    
                    // condicion creado
                    if((!$insumo["deleted"]) && ($insumo["codigo"] == -1)) {
                        $this->insumosServicios->create($insumo);
                    }
                endforeach;
                return 'true';
            } else {
                return 'false';
            }
        }
        

        
        
        public function copiarServicios($codigoSucursal) {
            $out = $this->servicios->listAll();
            foreach($out as $s) {
                $servicioData = $this->get($s["codigo"]);
                $servicioData["codigoSucursal"] = $codigoSucursal;
                foreach($servicioData["listaInsumos"] as $i => $listaInsumos) {
                    $servicioData["listaInsumos"][$i]["codigoServicio"] = $servicioData["codigo"];
                    $servicioData["listaInsumos"][$i]["deleted"] = 0;
                }
                $this->create($servicioData);
            }
        }

        

        
        public function nuevaFechaVigencia($fecha) {
            $out = $this->servicios->listAllVigente();
            foreach($out as $s) {
                $servicioData = $this->get($s["codigo"]);
                $servicioData["codigoSucursal"] = $this->codigoSucursal;
                $servicioData["fechaVigencia"] = $fecha;
                foreach($servicioData["listaInsumos"] as $i => $listaInsumos) {
                    $servicioData["listaInsumos"][$i]["deleted"] = 0;
                }
                $this->create($servicioData);
                
            }
        }
        
        
    }
    
?>