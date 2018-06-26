<?php 

    class CuentaCorrienteService {
    
        private $cuentaCorriente = null;

        function __construct($cuentaCorriente) {
            $this->cuentaCorriente = $cuentaCorriente;
        }
    
        

        public function listAll($codigoCliente, $month = -1, $year = -1) {
    
            $lista = $this->cuentaCorriente->listAll($codigoCliente, $month, $year);
            
            $out = Array();
            $out["aaData"] = Array();

            foreach($lista as $row) {
                
                $newRow = Array(
                    "DT_RowId" => $row["codigo"],
                    "0" => str_pad($row["codigo"], 8, '0', STR_PAD_LEFT),
                    "1" => $row["fechaRetiro"],
                    "2" => $row["_valor"],
                    "3" => $row["monto"]
                );
                $out["aaData"][] = $newRow;
            }
            
            return $out;
        }
  
    
        
        
        
        public function actualizarSaldo($model, $codigoCliente) {
            $success = true;
            
            // esto elimina entradas  que tengan codigoPedido = NULL ()
            // que representan saldo a favor del cliente.
            $success = $this->cuentaCorriente->eliminarSaldos($codigoCliente);
            
            // esto actualiza los montos de cada codigoCC (codigo PK)
            // se recibe por get, lo arma previamente la otra llamada
            $itemsAfectados = $model->itemsAfectados;
            foreach($itemsAfectados as $item) {
                $success = $this->cuentaCorriente->updateMonto($item->codigoCuentaCorriente, $item->monto);
            }
            
            // si quedo un saldo positivo lo adiciona a la cta corriente
            // se recibe de la otra llamada
            if($model->aFavorDelCliente != 0) { 
                $ccData = Array("codigoCliente" => $codigoCliente, "codigoPedido" => null, "monto" => -$model->aFavorDelCliente);
                $this->cuentaCorriente->create($ccData);
            }
            
            return $success;
        }
        
        
        public function corregirSaldo($cantidad, $codigoCliente) {
            $model = $this->cuentaCorriente->getSaldoCliente($codigoCliente);
            if($model["codigo"] != null) { // si tiene saldo, lo adiciono-resto
                $model["monto"] += $cantidad;
                $this->cuentaCorriente->updateMonto($model["codigo"], $model["monto"]);
            } else { // si no tiene saldo lo creo
                $this->cuentaCorriente->create(Array("codigoCliente" => $codigoCliente,"codigoPedido" => null,"monto" => $cantidad));
            }
            
            
        }
        
        
        
    }

?>