<?php
/*  Utilizacion: 
        $Ticket::soloControl = muestra solo talon de control, sin subtotales ni textos aclaratorios
        $Ticket::anticipo = el anticipo pagado por el cliente
        $Ticket::ListTareas(array(
            array(  'codigo' => '', 
                    'tarea' => '', 
                    'cliente' => '', 
                    'monto' => 0),
            .
            .
            .
        ))
        
        $Ticket::aRetirar = fecha de retiro
        $Ticket::cliente = nombre del cliente
        $Ticket::codigo = codigo del pedido (si esta seteado pone el codigo en la esq sup derecha, si no pone TAREAS)

*/    

    include ($_SERVER["DOCUMENT_ROOT"] .'/script/inc/fpdf.php');

    class Ticket extends FPDF {
       
        public $soloControl = false;
        public $anticipo = 0;
        public $aRetirar;
        public $cliente;
        public $codigo = "";
        public $direccion = "Washington y Maipu";
        public $zona = "Villa Mitre";
        public $telefono = "4811139";
        public $clientesData;
        
        function Header(){
            $this->SetMargins(5,5);
            $this->Line(5,24,100,24);
            $this->Image($_SERVER['DOCUMENT_ROOT'] ."/static/img/logo_pdf.png", 5,6,60);
            
            $this->SetFont('Courier', 'BU', 13);
            if($this->codigo == "") {
                $this->Text(105 - $this->GetStringWidth("TAREAS") - 5, 8, "TAREAS");
            } else {
                $this->Text(105 - $this->GetStringWidth($this->codigo) - 5, 8, $this->codigo);
            }
            
            $this->SetY(10);
            $this->SetFont('Helvetica', '', 9);
            // poner estos datos de DB
            $this->Multicell(0,3.5,utf8_decode("$this->direccion\n$this->zona\nTelefono: $this->telefono"),0,'R');
        }
        
        
        function Footer($offset = 0) {
            if(!$this->soloControl) {
                $this->Line(5,$offset + 123,100,$offset + 123);
                $this->SetFont('Helvetica', '', 7);
                $this->SetY($offset + 126);
                $this->Multicell(0,2,utf8_decode("Las prendas que no sean retiradas en un plazo mayor a 60 días, serán donadas a una entidad benefica sin derecho a reclamo alguno. \nSometer una prenda al proceso de blanqueado puede ocasionar una variacion en el tono de sus colores.")
                ,0,'J');
                $this->Line(70,$offset + 140,90,$offset + 140);
            }
        }

        

        
        function talonCliente($offset = 0) {
            $imprimeTelefono = ($offset != 0)? utf8_decode($this->clientesData["telefono"]) : "";
            $this->SetY($offset + 26);
            $this->SetFont('Helvetica', 'B', 10);
            
            $this->Write(4, utf8_decode($this->cliente) ." - ". $imprimeTelefono); 
            $this->SetX(82); 
            $this->Write (4,"Ad: $". $this->anticipo);
            $this->Ln();
            
            $this->Write (4,$this->clientesData["direccion"]);
            $this->SetX(66); 
            $ftemp = explode(" ",$this->modelData["fechaPedido"]);
            $this->Write (4,"Fecha:  ". swapDateFormat($ftemp[0])); // fecha
            $this->Ln();
            
            $this->Write (4,$this->codigo);
            $this->SetX(66); 
            $this->Write (4,"Retira: ". $this->aRetirar);

        }        
        
        
        

        private function ListTareasPedido($data, $offset = 0) {
            $this->SetY($offset + 38);

            $texto = "";
            $this->SetFont('Helvetica', '', 8);
            foreach($data as $row) {
                $texto .= $row['cantidad'] ."x". utf8_decode($row['_descripcion']) ." ($". $row['_subTotal'] .")\n";
            }
            $this->Multicell(0,4,utf8_decode($texto));
            $this->printFooter($offset);
        }


        
        private function printFooter($offset = 0) {
            $this->Line(5,$offset + 62,100,$offset + 62);
            $this->SetFont('Helvetica', '', 7);
            $this->SetY($offset + 64);
            $this->Multicell(0,2,utf8_decode("Las prendas que no sean retiradas en un plazo mayor a 60 días, serán donadas a una entidad benefica sin derecho a reclamo alguno. \nSometer una prenda al proceso de blanqueado puede ocasionar una variacion en el tono de sus colores.")
            ,0,'J');
            $this->Line(70,$offset + 77,90,$offset + 77);
        }
        

        
        public function printObs($observaciones) {
            $offset = 40;
            $this->Line(5,$offset + 62,100,$offset + 62);
            $this->SetFont('Helvetica', '', 7);
            $this->SetY($offset + 64);
            $this->Multicell(0,2,utf8_decode($observaciones),0,'J');
        }
        
        
        
        function ListTareas($data, $montoPagado = 0) {
            $this->SetY(28);
            $acumulador = 0;
            // Header
            $montoTitle = ($this->soloControl)? "Fecha" : "Monto";
            $this->SetFont('Helvetica', 'B', 8);
            $this->Cell(23,5,"Codigo","B", 0, "L");
            $this->Cell(32,5,"Tarea","B", 0, "L");
            $this->Cell(29,5,"Cliente","B", 0, "L");
            $this->Cell(11,5, $montoTitle, "B", 0, "L");
            $this->Ln();

            $this->SetFont('Helvetica', '', 8);
            foreach($data as $row) {
                $montoMostrar = ($this->soloControl)? "" : number_format($row['cantidad'] * $row['_valor'],2);
                $monto = ($this->soloControl)? 0 : $row['cantidad'] * $row['_valor'];
                $this->Cell(23,5, $row['_prefijoCodigo'] . $row['codigoPedido'],"B", 0, "L");
                $this->Cell(32,5, substr($row['cantidad'] ."x". utf8_decode($row['_descripcion']),0,17), "B", 0, "L");
                $this->Cell(29,5, utf8_decode(substr($row['_nombreCliente'],0,15)), "B", 0, "L");
                if($this->soloControl) {
                    $this->Cell(11,5, $row['_fechaPedido'], "B", 0, "R");
                } else {
                    $this->Cell(11,5, $montoMostrar, "B", 0, "R");
                }
                
                $this->Ln();
                $acumulador += $monto;
            }
            
            if(!$this->soloControl) {
                $this->Cell(78, 5, "Subtotal: ", "", 0, "R");
                $this->Cell(17, 5, number_format($acumulador,2) , "", 0, "R");
                $this->Ln();
                $this->Cell(78, 5, "Anticipo: ", "", 0, "R");
                $this->Cell(17, 5, number_format($this->anticipo,2) , "", 0, "R");
                $this->Ln();
                
                $this->SetFont('Helvetica', 'B', 10);            
                $this->Cell(58, 5, $this->modelCliente["nombres"] ." ". $this->modelCliente["apellido"] , "", 0, "L");
                $this->Cell(20, 5, "TOTAL: ", "", 0, "R");
                $this->Cell(17, 5, number_format($acumulador - $this->anticipo,2) , "", 0, "R");
                $this->Ln();

                $this->Cell(58, 5, $this->modelCliente["direccion"] , "", 0, "L");
                $this->Cell(20, 5, "Entregado: ", "", 0, "R");
                $this->Cell(17, 5, number_format($montoPagado,2) , "", 0, "R");
                $this->Ln();
                
                $this->SetFont('Helvetica', 'B', 10);            
                $this->Cell(58, 5, $this->modelCliente["telefono"] , "", 0, "L");
                $this->Cell(20, 5, "Su vuelto: ", "", 0, "R");
                $this->Cell(17, 5, number_format($montoPagado + $this->anticipo - $acumulador,2) , "", 0, "R");
                $this->Ln();
                
            }
        }


        
        
        
        
        
        function ListTareasRemito($data) {
            $this->SetY(28);
            $acumulador = 0;
            // Header
            $this->SetFont('Helvetica', 'B', 8);
            $this->Cell(23,5,"Codigo","B", 0, "L");
            $this->Cell(32,5,"Tarea","B", 0, "L");
            $this->Cell(29,5,"Cliente","B", 0, "L");
            $this->Cell(11,5, "Monto", "B", 0, "L");
            $this->Ln();

            $this->SetFont('Helvetica', '', 8);
            foreach($data as $row) {
                $montoMostrar = number_format($row['cantidad'] * $row['_valor'],2);
                $monto = $row['cantidad'] * $row['_valor'];
                $this->Cell(23,5, $row['_prefijoCodigo'] . $row['codigoPedido'],"B", 0, "L");
                $this->Cell(32,5, substr($row['cantidad'] ."x". utf8_decode($row['_descripcion']),0,17), "B", 0, "L");
                $this->Cell(29,5, utf8_decode(substr($row['_nombreCliente'],0,15)), "B", 0, "L");
                $this->Cell(11,5, $montoMostrar, "B", 0, "R");
                $this->Ln();
                $acumulador += $monto;
            }

            $this->SetFont('Helvetica', 'B', 10);            
            $this->Cell(78, 5, "TOTAL: ", "", 0, "R");
            $this->Cell(17, 5, number_format($acumulador,2) , "", 0, "R");
            $this->Ln();

        }
        
        
        
        
        
        
        
        public function generateWithServices() {
            $codigo = (is_null($this->modelData['codigoTalon']) || empty($this->modelData['codigoTalon']))? $this->modelData['codigo'] : $this->modelData['codigoTalon'];

            $this->codigo = $this->proveedorModel["prefijoCodigo"] . $codigo;
            $this->cliente = $this->modelData['_nombreCliente'];
            $this->aRetirar = swapDateFormat($this->modelData['fechaRetiro']);
            $this->anticipo = $this->modelData['anticipo'];
            $this->soloControl = true; // hack. para que no imprima footer, lo imprime cada ->talonCliente
            $this->AddPage();
            $this->talonCliente();
            $this->ListTareasPedido($this->modelData['listaServicios']);
            $this->Line(0,80,105,80); // separador tickets
            $this->talonCliente(55);                    
            $this->ListTareasPedido($this->modelData['listaServicios'], 55);
            $this->Output($_SERVER["DOCUMENT_ROOT"] ."/static/download/". session_id() .".pdf",'F');            
        }
        
        
        
        public function generate() {
            $codigo = (is_null($this->modelData['codigoTalon']) || empty($this->modelData['codigoTalon']))? $this->modelData['codigo'] : $this->modelData['codigoTalon'];

            $this->codigo = $this->proveedorModel["prefijoCodigo"] . $codigo;
            $this->cliente = $this->modelData['_nombreCliente'];
            $this->aRetirar = swapDateFormat($this->modelData['fechaRetiro']);
            $this->anticipo = $this->modelData['anticipo'];
            $this->soloControl = true; // hack. para que no imprima footer, lo imprime cada ->talonCliente
            $this->AddPage();
            $this->talonCliente();
            $this->printFooter();
            $this->Line(0,80,105,80); // separador tickets
            $this->talonCliente(55);                    
            $this->printFooter(53);
            $this->Output($_SERVER["DOCUMENT_ROOT"] ."/static/download/". session_id() .".pdf",'F');            
        }
        
        
        
        
        
        




    } // END CLASS



?>