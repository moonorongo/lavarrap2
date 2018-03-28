<?php
/*
    Reportes Controller
*/    
    session_start();
    if(!isset($_SESSION['LOGGED'])) { // si no esta logueado
        header('HTTP/1.0 401 Unauthorized');
        echo ('NO AUTORIZADO: click <a href="http://'. $_SERVER['SERVER_NAME'] .'">aqui</a> para entrar');
    } else { // si esta logueado
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
        header('Content-Type: application/json');
    
        $mysql = Mysql::getInstance();
        $mysql->connect();
        $reportes = new Reportes($mysql);
        $caja = new Caja($mysql);
        $pedidos = new Pedidos($mysql);

        $reportesService = new ReportesService($reportes, $caja, $pedidos);

        $action = (isset($_REQUEST["action"]))? $_REQUEST["action"] : null;
        $fechaInicial = $_REQUEST["fechaInicial"];
        $fechaFinal = $_REQUEST["fechaFinal"];

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=". $action ."_". $fechaInicial ."_". $fechaFinal .".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $reportTitle = Array("rPrincipal" => "Reporte principal", 
                            "rInsumos" => "Insumos consumidos contra ingresados", 
                            "rDerivaciones" => "Derivaciones realizadas", 
                            "rServiciosRealizados" => "Servicios realizados", 
                            "rListaServicios" => "Servicios",
                            "rCumpleanos" => "Cumpleaños",
                            "rConsumos" => "Consumos realizados",
                            "exportarListaClientes" => "Listado Clientes",
                            "cajaFacturado" =>  "Caja contra facturado",
                            "resumenCajaMes" => "Resumen Caja Mes"
                            );
        ?>
    <table>
        <tr>
            <td style="font-size: 24px"><h1><?= utf8_decode($reportTitle[$action]); ?></h1></td>
        </tr>
    </table>
        
    <?php    
        if($action == 'rPrincipal') {

            $out = $reportesService->reportePrincipal($fechaInicial,$fechaFinal);
            $totalFacturado = 0;
            $totalCobrado = 0;
            ?>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Facturado</th>
                    <th>Cobrado</th>
                </tr>
            <?php foreach($out as $item) { 
                $totalFacturado += floatval($item['facturado']);
                $totalCobrado += floatval($item['cobrado']);
            ?>
                <tr>
                    <td><?= swapDateFormat($item['fecha']); ?></td>
                    <td style="text-align: right"><?= number_format($item['facturado'], 2, ",", ""); ?></td>
                    <td style="text-align: right"><?= number_format($item['cobrado'], 2, ",", ""); ?></td>
                </tr>
            <?php } ?>
                <tr>
                    <th style="text-align: right">Totales: </th>
                    <th style="text-align: right"><?= number_format($totalFacturado, 2, ",", ""); ?></th>
                    <th style="text-align: right"><?= number_format($totalCobrado, 2, ",", ""); ?></th>
                </tr>
        <?php } // if rPrincipal

        
        
        
        
        
        
        if($action == 'rInsumos') {
            $out = $reportes->reporteInsumos($fechaInicial,$fechaFinal);
            ?>
            <table>
                <tr>
                    <th>Descripcion</th>
                    <th>Total ingresado</th>
                    <th>Total consumido</th>
                </tr>
            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= utf8_decode($item['descripcion']); ?></td>
                    <td style="text-align: right"><?= number_format($item['totalIngresado'], 2, ",", ""); ?></td>
                    <td style="text-align: right"><?= number_format($item['totalConsumido'], 2, ",", ""); ?></td>
                </tr>
            <?php } 
        } // if rInsumos
        
        

        
        
        
        
        if($action == 'rDerivaciones') {
            $out = $reportes->reporteDerivaciones($fechaInicial, $fechaFinal);
            $codigoAnterior = $out[0]["codigoProveedor"];
            ?>
            <table>
                <tr>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: left;"><?= utf8_decode($out[0]["descripcionProveedor"]) ?></th>
                </tr>
            <?php foreach($out as $item) { 
                if($codigoAnterior != $item["codigoProveedor"]) {
                    $codigoAnterior = $item["codigoProveedor"];
            ?>
                <tr>
                    <th colspan="2" style="text-align: left;"><?= utf8_decode($item["descripcionProveedor"]) ?></th>
                </tr>
            <?php } ?>
                <tr>
                    <td><?= utf8_decode($item['descripcionServicio']); ?></td>
                    <td style="text-align: right"><?= $item['cantidad']; ?></td>
                </tr>
            <?php } 
        } // if rDerivaciones
        

        
        
        
        
        
        if($action == 'rServiciosRealizados') {
            $out = $reportes->reporteServiciosRealizados($fechaInicial, $fechaFinal);
            ?>
            <table>
                <tr>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                </tr>

            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= utf8_decode($item['descripcionServicio']); ?></td>
                    <td style="text-align: right"><?= $item['cantidad']; ?></td>
                </tr>
            <?php } 
        } // if rServiciosRealizados
        

        
        
        
        
        
        
        if($action == 'rListaServicios') {
            $out = $reportes->reporteListaServicios($fechaInicial, $fechaFinal);
            ?>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>

            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= $item['fechaPedido']; ?></td>
                    <td><?= utf8_decode($item['nombresApellido']); ?></td>
                    <td><?= utf8_decode($item['descripcionServicio']); ?></td>
                    <td style="text-align: right"><?= $item['cantidad']; ?></td>
                    <td><?= utf8_decode($item['descripcionEstado']); ?></td>
                </tr>
            <?php } 
        } // if rServiciosRealizados
        

        
        
        
        
        
        if($action == 'rCumpleanos') {
            if($fechaInicial == "") $fechaInicial = date("Y-m-d");
            $out = $reportes->reporteCumpleanos($fechaInicial);
            $currentMes = intval($out[0]["mes"]);
            ?>
            <table>
                <tr>
                    <th>Cliente</th>
                    <th>D&iacute;a</th>
                    <th>Direcci&oacute;n</th>
                    <th>Tel&eacute;fono</th>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: left"><h2><?= mes($currentMes); ?></h2></th>
                </tr>
            <?php foreach($out as $item) { ?>
                <?php 
                    if($currentMes != intval($item["mes"])) { 
                        $currentMes = intval($item["mes"]);
                        echo('<tr><td colspan="4" style="text-align: left"><h2>'. mes($currentMes) .'</h2></th></tr>');
                    } 
                    
                    $dia = explode('-',$item['fecha']);
                ?>
                <tr>
                    <td><?= utf8_decode($item['nombresApellido']); ?></td>
                    <td><?= $dia[2]; ?></td>
                    <td><?= utf8_decode($item['direccion']); ?></td>
                    <td><?= $item['telefono']; ?></td>
                </tr>
            <?php } 
        } // if rCumpleanos
        
        
        

        if($action == 'rConsumos') {
            $out = $reportes->reporteConsumos($fechaInicial, $fechaFinal);
            ?>
            <table>
                <tr>
                    <th>Cliente</th>
                    <th>Consumo</th>
                </tr>

            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= utf8_decode($item['nombresApellido']); ?></td>
                    <td style="text-align: right"><?= number_format($item['consumo'], 2, ",", ""); ?></td>
                </tr>
            <?php } 
        } // if rConsumos

        
        
        
        if($action == 'exportarListaClientes') {
            $out = $reportes->exportarListaClientes();
            ?>
            <table>
                <tr>
                    <th>Cliente</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Fecha de Nacimiento</th>
                </tr>

            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= utf8_decode($item['nombresApellido']); ?></td>
                    <td><?= utf8_decode($item['direccion']); ?></td>
                    <td><?= utf8_decode($item['telefono']); ?></td>
                    <td><?= $item['fechaNacimiento']; ?></td>
                </tr>
            <?php } 
        } // if exportarListaClientes
        


        if($action == 'cajaFacturado') {
            $mes = $fechaInicial;

            $out = $reportesService->reporteCajaFacturado($mes, $fechaFinal);

            $fechaAnterior = null;
            $totalCobrado = 0; 
            $totalFacturado = 0; 
            $totalCobradoDia = 0;
            $totalFacturadoDia = 0;
            $totalCobradoPedidosMesAnterior = 0;
            $totalFacturadoPedidoMesAnterior = 0;
            ?>
            <table>
                <tr>
                    <th style="text-align: left;">Nro</th>
                    <th style="text-align: left;">F.Pedido</th>
                    <th style="text-align: left;">Cliente</th>
                    <th style="text-align: right;">Facturado</th>
                    <th style="text-align: right;">Cobrado</th>
                    <th>Es Mes Ant.</th>
                </tr>

            <?php
                foreach ($out as $pedidoId => $item) { 
                    $fechaPedido = new DateTime(date($item['fechaPedido']));
                    $mesFechaPedido = intval($fechaPedido->format('m'));

                    if(is_null($fechaAnterior)) {
                        echo '<tr><td colspan="5">'. swapDateFormat($item['fechaCaja']) .'</td></tr>';
                    } else {
                        if($fechaAnterior != $item['fechaCaja']) {
                            echo '<tr>
                                      <td colspan="3" style="text-align: right;">Total Dia</td>
                                      <td style="text-align: right;">'. $totalFacturadoDia .'</td>
                                      <td style="text-align: right;">'. $totalCobradoDia .'</td>
                                  </tr>
                                  <tr><td colspan="5">'. swapDateFormat($item['fechaCaja']) .'</td></tr>';

                            $totalFacturadoDia = 0;
                            $totalCobradoDia = 0;
                        }
                    } ?>
                    
                    <tr>
                        <td><?= $pedidoId ?></td>
                        <td><?= swapDateFormat($item['fechaPedido']) ?> </td>
                        <td><?= $item['nombreApellido'] ?> </td>
                        <td style="text-align: right;"><?= $item['montoFacturado'] ?></td>
                        <td style="text-align: right;"><?= $item['montoCobrado'] ?></td>
                        <td style="text-align: center"><?= ($mesFechaPedido != $mes)? '<<---mes anterior' : '' ?></td>
                    </tr>
            <?php 
                    $totalCobradoPedidosMesAnterior += ($mesFechaPedido != $mes)? $item['montoCobrado'] : 0;
                    $totalFacturadoPedidoMesAnterior += ($mesFechaPedido != $mes)? $item['montoFacturado'] : 0;

                    $totalFacturadoDia += $item['montoFacturado']; 
                    $totalCobradoDia += $item['montoCobrado'];

                    $totalFacturado += $item['montoFacturado'];
                    $totalCobrado += $item['montoCobrado'];

                    $fechaAnterior = $item['fechaCaja'];
                }

                echo '<tr>
                          <td colspan="3" style="text-align: right;">Total Dia</td>
                          <td style="text-align: right;">'. $totalFacturadoDia .'</td>
                          <td style="text-align: right;">'. $totalCobradoDia .'</td>
                      </tr>';
                echo '<tr>
                          <td colspan="3" style="text-align: right;">Total Mes</td>
                          <td style="text-align: right;">'. $totalFacturado .'</td>
                          <td style="text-align: right;">'. $totalCobrado .'</td>
                      </tr>';
                echo '<tr>
                          <td colspan="3" style="text-align: right;"><strong>Correspondientes a pedidos del Mes anterior</strong></td>
                          <td style="text-align: right;">'. $totalFacturadoPedidoMesAnterior .'</td>
                          <td style="text-align: right;">'. $totalCobradoPedidosMesAnterior .'</td>
                      </tr>';
        } // if cajaFacturado


        // Resumen Caja Mes
        if($action == 'resumenCajaMes') {
            $fecha  = "$fechaFinal-$fechaInicial-01"; // fechaInicial = month, fechaFinal = year... :(
            $out = $caja->listEgresosMes($fecha);
            $totalIngresos = 0;
            $totalEgresos = 0;
            ?>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Ingreso</th>
                    <th>Egreso</th>
                    <th>Observaciones</th>
                </tr>

            <?php foreach($out as $item) { ?>
                <tr>
                    <td><?= swapDateFormat(explode(' ', $item['fecha'])[0]); ?></td>
                    <td><?= ($item['monto'] > 0)? $item['monto'] : '' ?></td>
                    <td><?= ($item['monto'] < 0)? abs($item['monto']) : '' ?></td>
                    <td><?= utf8_decode($item['observaciones']); ?></td>
                </tr>
            <?php 
                if($item['monto'] > 0) {
                    $totalIngresos += $item['monto'];
                } else {
                    $totalEgresos += abs($item['monto']);
                }
            } //  end foreach 
            ?>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>TOTALES:</td>
                <td><?= $totalIngresos ?></td>
                <td><?= $totalEgresos ?></td>
                <td></td>
            </tr>
            <?php
        } // if resumenCajaMes

        $mysql->close();    
    } // if logged    
?>
        </table>