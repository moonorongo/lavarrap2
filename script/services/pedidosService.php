<?php 

    class PedidosService {
    
        private $pedidos = null;
        private $serviciosPedidos = null;
        private $servicios = null;
        private $proveedores = null;

        function __construct($pedidos, $serviciosPedidos, $servicios, $proveedores) {
            $this->pedidos = $pedidos;
            $this->serviciosPedidos = $serviciosPedidos;
            $this->servicios = $servicios;
            $this->proveedores = $proveedores;
        }
    
        
        
        public function listAll($entregado = 0, $fechaPedido = "") {
    
            $lista = $this->pedidos->listAll($entregado, $fechaPedido);
            
            $out = Array();
            $out["aaData"] = Array();

            foreach($lista as $row) {
                // correccion para evitar division por cero
                if($row["_cantTotal"] == 0) $row["_cantTotal"] = 1;
                
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => str_pad($row["codigo"], 8, '0', STR_PAD_LEFT),
                    "1" => $row["fechaPedido"][0],
                    "2" => $row["nombres"] ." ". $row['apellido'],
                    "3" => $row["direccion"],
                    "4" => $row["telefono"],
                    "5" => $row["fechaRetiro"],
                    "6" => ($row["_cantDerivaciones"] > 0)? '<i class="icon-exclamation-sign"></i>':'',
                    "7" => number_format(($row["_cantProcesado"] / $row["_cantTotal"]) * 100, 2)
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

/*        
// Metodos utilizados por Cuenta Corriente - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
        public function listAllCuentaCorriente($codigoCliente) {
    
            $lista = $this->pedidos->listAllCuentaCorriente($codigoCliente);
            
            $out = Array();
            $out["aaData"] = Array();

            foreach($lista as $row) {
                
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => str_pad($row["codigo"], 8, '0', STR_PAD_LEFT),
                    "1" => $row["fechaRetiro"],
                    "2" => $row["_valor"],
                    "3" => $row["aCobrar"]
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
  */      
    
    }

?>