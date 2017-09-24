<script type="text/template" id="mainAppTemplate">

    <div class="menuBar">
        <div id="mainWrapper" class="centered">
            <div id="logo">
                <img src="/static/img/logo.png" />
            </div>
            <div class="floatLeft" style="margin: 13px 0 0 15px;line-height: 24px;color: white;">
            
                <?php if(isAdmin()) { ?><a href="#" id="cambiarSucursal" class="switchLink"><?php } ?>
                <span id="descripcionSucursal"><?php echo($_SESSION['DESCRIPCION']); ?></span>
                <?php if(isAdmin()) { ?></a><?php } ?>
                    
                <?php if(isAdmin()) { ?>
                <select class="hide" id="sucursalCombo">
                    <?php
                        $mysql = Mysql::getInstance();
                        $mysql->connect();
                        $result = $mysql->query("SELECT codigo, descripcion FROM proveedores WHERE activo = 1 and esSucursal = 1");
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($row['codigo'] == $_SUCURSAL)? " selected ":"";
                    ?>
                    <option value="<?php echo $row['codigo']; ?>" <?php echo $selected; ?> ><?php echo $row['descripcion']; ?></option>
                    <?php } ?>
                </select>
                <?php } ?>
            </div>
            <div id="menu" class="menu">
                <ul>
                <?php if(isAdmin()) { ?>                    
                    <li><a id="reportes">Reportes <i class="icon-angle-down"></i></a>
                        <ul>
                            <li><a id="rPrincipal">Principal</a></li>
                            <li><a id="cajaFacturado">Caja contra Facturado</a></li>
                            <li><a id="rInsumos">Insumos I/C</a></li>
                            <li><a id="rDerivaciones">Derivaciones</a></li>
                            <li><a id="rServiciosRealizados">Cant. Servicios</a></li>
                            <li><a id="rListaServicios">Lista servicios</a></li>
                        </ul>
                    </li>
                    <li><a id="configuracion">Configuraci&oacute;n <i class="icon-angle-down"></i></a> 
                        <ul>
                            <li><a id="servicios">Servicios</a></li>
                            <li><a id="proveedores">Proveedores</a></li>
                            <li><a id="insumos">Insumos</a></li>
                            <li><a>Caja <i class="icon-angle-right"></i></a>
                                <ul>
                                    <li><a id="ingresoCaja">Registrar ingreso</a></li>
                                    <li><a id="egresoCaja">Registrar egreso</a></li>
                                    <li><a id="cerrarCajaMes">Cerrar Caja Mes</a></li> 
                                    <li><a id="movimientosCaja">Movimientos de Caja</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php } ?>    
                    <li><a id="pedidos">Pedidos</a></li>
                    <li><a id="tareas">Tareas</a></li>
                    <li><a id="clientes">Clientes <i class="icon-angle-down"></i></a>
                        <ul>
                            <li><a id="cuentaCorriente">Cuenta corriente</a></li>
                            <li><a id="rCumpleanos">Cumplea&ntilde;os</a></li>
                            <li><a id="rConsumos">Consumos</a></li>
                            <li><a id="exportarListaClientes">Exportar</a></li>
                        </ul>
                    </li>                    
                    <li><a id="salir" href="logout.php">Salir</a></li>
                </ul>
                
            </div>

        </div>
    </div>
   
   <div id="mainApp" class="mainApp">
        <div id="sectionContainer" class="centered mainAppBackground"></div>
   </div>
   
   <div id="cajaPopup"><div id="cajaContainer"></div></div>
   <div id="waitingPopup" style="text-align: center">
       <i class="icon-spinner icon-spin icon-large" style="font-size: 48px"></i>
   </div>
   
    
</script> 



