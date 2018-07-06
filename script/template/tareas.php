<script type="text/template" id="tareasTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 18%">C&oacute;digo pedido</th>
                            <th style="text-align: left; width: 8%">F. retiro</th>
                            <th style="text-align: left; width: 26%">Descripci&oacute;n</th>
                            <th style="text-align: left; width: 35%">Derivaci&oacute;n</th>
                            <th style="text-align: left; width: 13%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="tareasModificarPopup">
                <div id="tareasModificarContainer" class="popupContainer"></div>
            </div>
        </div>            
</script>


<script type="text/template" id="tareasModificarTemplate">
    
    <@ if(codigoEstado == 2) { @>
        Desea iniciar <@= (cantidadTareasSeleccionadas != 1)? cantidadTareasSeleccionadas:'' @> tarea<@= (cantidadTareasSeleccionadas != 1)? 's':'' @>?
        <br />
    <@ } @>
    
    <@ if(codigoEstado == 3) { @>
        Desea finalizar <@= (cantidadTareasSeleccionadas != 1)? cantidadTareasSeleccionadas:'' @> tarea<@= (cantidadTareasSeleccionadas != 1)? 's':'' @>?
        <br />
    <@ } @>

    <@ if(codigoEstado == -1) { @>
        Derivar a 
        <select id="codigoProveedor" style="width: 179px">
            <?php
                global $_SUCURSAL;
                $mysql = Mysql::getInstance();
                $mysql->connect();
                $result = $mysql->query("SELECT codigo, descripcion FROM proveedores WHERE activo = 1 and (codigo <> $_SUCURSAL) and (codigo <> 9)");
                while ($row = $result->fetch_assoc()) {
            ?>
            <option value="<?php echo $row['codigo']; ?>"><?php echo $row['descripcion']; ?></option>
            <?php } ?>
        </select>
    <@ } @>
    




    <div class="popupButtons">
        <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
        <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
    </div>    
</script>    