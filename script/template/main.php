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
                        $result = $mysql->query("SELECT codigo, descripcion FROM proveedores WHERE activo = 1 and esSucursal = 1 and codigo <> 9");
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
                    <li class="root-menu"><a id="reportes">Reportes <i class="icon-angle-down"></i></a>
                        <ul>
                            <li><a id="rPrincipal" class="menu-item">Principal</a></li>
                            <li><a id="cajaFacturado" class="menu-item">Caja contra Facturado</a></li>
                            <li><a id="rInsumos" class="menu-item">Insumos I/C</a></li>
                            <li><a id="rDerivaciones" class="menu-item">Derivaciones</a></li>
                            <li><a id="rServiciosRealizados" class="menu-item">Cant. Servicios</a></li>
                            <li><a id="rListaServicios" class="menu-item">Lista servicios</a></li>
                            <li><a id="resumenCajaMes" class="menu-item">Resumen Caja Mes</a></li>
                        </ul>
                    </li>
                    <li class="root-menu"><a id="configuracion">Configuraci&oacute;n <i class="icon-angle-down"></i></a> 
                        <ul>
                            <li><a id="servicios" class="menu-item">Servicios</a></li>
                            <li><a id="proveedores" class="menu-item">Proveedores</a></li>
                            <li><a id="insumos" class="menu-item">Insumos</a></li>
                            <li><a>Caja <i class="icon-angle-right"></i></a>
                                <ul>
                                    <li><a id="logCaja" class="menu-item">Ver log de Caja</a></li>
                                    <li><a id="ingresoCaja" class="menu-item">Registrar ingreso</a></li>
                                    <li><a id="egresoCaja" class="menu-item">Registrar egreso</a></li>
                                    <li><a id="cerrarCajaMes" class="menu-item">Cerrar Caja Mes</a></li> 
                                    <li><a id="movimientosCaja" class="menu-item">Movimientos de Caja</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php } ?>    
                    <li class="root-menu"><a id="pedidos"  class="menu-item">Pedidos</a></li>
                    <li class="root-menu"><a id="tareas" class="menu-item">Tareas</a></li>
                    <li class="root-menu"><a id="clientes" class="menu-item">Clientes <i class="icon-angle-down"></i></a>
                        <ul>
                            <li><a id="cuentaCorriente" class="menu-item">Cuenta corriente</a></li>
                            <li><a id="rCumpleanos" class="menu-item">Cumplea&ntilde;os</a></li>
                            <li><a id="rConsumos" class="menu-item">Consumos</a></li>
                            <li><a id="exportarListaClientes" class="menu-item">Exportar</a></li>
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



