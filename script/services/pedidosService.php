<?php 

    class PedidosService {
    
        private $pedidos = null;
        private $serviciosPedidos = null;
        private $servicios = null;
        private $proveedores = null;
        private $caja = null;

        function __construct($pedidos, $serviciosPedidos, $servicios, $proveedores, $caja = null) {
            $this->pedidos = $pedidos;
            $this->serviciosPedidos = $serviciosPedidos;
            $this->servicios = $servicios;
            $this->proveedores = $proveedores;
            $this->caja = $caja;
        }
    
        
        public function getPagedSorted($sSearch, $start, $length, $sEcho, $entregado, $fechaPedido) {
    
            $sEcho++;
            $lista = $this->pedidos->getPagedSorted($sSearch, $start, $length, $entregado, $fechaPedido);
            
            $out = Array();
            $out["iTotalRecords"] = $this->pedidos->count($entregado, $fechaPedido, $sSearch);
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();
            
            foreach($lista as $row) {
                if($row["_cantTotal"] == 0) $row["_cantTotal"] = 1;

                $columnaCodigo = (is_null($row["codigoTalon"]) || empty($row["codigoTalon"]) )? '('.str_pad($row["codigo"], 8, '0', STR_PAD_LEFT).')' : str_pad($row["codigoTalon"], 8, '0', STR_PAD_LEFT);

                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => $columnaCodigo,
                    "1" => $row["fechaPedido"][0],
                    "2" => $row["nombres"] ." ". $row['apellido'],
                    "3" => $row["direccion"],
                    "4" => $row["telefono"],
                    "5" => $row["fechaRetiro"],
                    "6" => ($row["_cantDerivaciones"] > 0)? '<i class="icon-exclamation-sign"></i>':'',
                    "7" => number_format(($row["_cantProcesado"] / $row["_cantTotal"]) * 100, 2),
                    "8" => (empty($row['codigoCuentaCorriente']))? '' : 'CC'
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
        

        public function getPagedSortedTemplates($sSearch, $start, $length, $sEcho) {
    
            $sEcho++;
            $lista = $this->pedidos->getPagedSortedTemplates($sSearch, $start, $length);
            
            $out = Array();
            $out["iTotalRecords"] = $this->pedidos->countTemplates($sSearch);
            $out["iTotalDisplayRecords"] = $out["iTotalRecords"];
            $out["sEcho"] = $sEcho;
            $out["aaData"] = Array();
            
            foreach($lista as $row) {
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => str_pad($row["codigo"], 8, '0', STR_PAD_LEFT),
                    "1" => $row["fechaPedido"][0],
                    "2" => $row["nombreTemplate"],
                    "3" => "", // $row["direccion"],
                    "4" => "", // $row["telefono"],
                    "5" => $row["fechaPedido"][0],
                    "6" => "", // ($row["_cantDerivaciones"] > 0)? '<i class="icon-exclamation-sign"></i>':'',
                    "7" => "", // number_format(($row["_cantProcesado"] / $row["_cantTotal"]) * 100, 2)
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }

        
        
        public function listTemplates() {
            $lista = $this->pedidos->listTemplates();
            $out = Array();
            foreach($lista as $row) {
                $row["listaServicios"] = $this->serviciosPedidos->getByCodigoPedido($row['codigo']);
                $out[] = $row;
            }
            return $out;
        }
        
        
        
        public function delete($codigo) {
            $this->serviciosPedidos->deleteByCodigoPedido($codigo);
            $this->caja->deleteByCodigoPedido($codigo);
            $this->pedidos->delete($codigo);
        }
        
        
        public function get($codigo) {
            $out = $this->pedidos->get($codigo);
            $out["listaServicios"] = $this->serviciosPedidos->getByCodigoPedido($codigo);
            $out["listaServiciosCombo"] = $this->servicios->listAllVigente();
            $out["listaProveedoresCombo"] = $this->proveedores->listAll(true);
            return $out;
        }
        

        public function getListaServicios() {
            $out = $this->servicios->listAllVigente();
            return $out;
        }

        public function getListaProveedores() {
            $out = $this->proveedores->listAll(true);
            return $out;
        }
        
        
        
        public function create($model) {
            $model = $this->pedidos->create($model);
            foreach($model["listaServicios"] as $servicio):
                $servicio["codigoPedido"] = $model["codigo"];
            
                if( isset($model["nombre"]) ) {
                    $servicio["codigoEstado"] = 99; // plantilla, pongo 99 porq no quiero q aparezca nunca
                }
                if(!$servicio["deleted"]) $this->serviciosPedidos->create($servicio);
            endforeach;                

            return $model;
        }

        
        public function update($model) {
            $this->pedidos->update($model);
            foreach($model["listaServicios"] as $servicio):
                $servicio["codigoPedido"] = $model["codigo"];
                // condicion borrado
                if(($servicio["deleted"]) && ($servicio["codigo"] != -1)) {
                    $this->serviciosPedidos->delete($servicio["codigo"]);
                }
                
                // condicion actualizado
                if((!$servicio["deleted"]) && ($servicio["codigo"] != -1)) {
                    $this->serviciosPedidos->update($servicio);
                }
                
                // condicion creado
                if((!$servicio["deleted"]) && ($servicio["codigo"] == -1)) {
                    $this->serviciosPedidos->create($servicio);
                }
            endforeach;
        }

    }
?>