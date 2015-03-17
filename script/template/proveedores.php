<script type="text/template" id="proveedoresTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 75%">Descripci&oacute;n</th>
                            <th style="text-align: left; width: 25%">Es Sucursal</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="editProveedorPopup">
                <div id="proveedoresModificarContainer"></div>
            </div>
        </div>            
</script>







<script type="text/template" id="proveedoresModificarTemplate">
    <form id="proveedoresForm">
        <label for="descripcion" class="labelForm">Descripci&oacute;n</label>
            <input type="text" id="descripcion" value="<@= descripcion @>" class="focus obligatorio" title="Descripcion" tabindex="1"/>
        <br /><br />
        
        <label for="direccion" class="labelForm">Direcci&oacute;n</label>
            <input type="text" id="direccion" value="<@= direccion @>" class="" title="Direccion" tabindex="2"/>
        <br /><br />
        
        <label for="zona" class="labelForm">Zona</label>
            <input type="text" id="zona" value="<@= zona @>" class="" title="Zona" tabindex="3"/>
        <br /><br />
        
        <label for="telefono" class="labelForm">Tel&eacute;fono</label>
            <input type="text" id="telefono" value="<@= telefono @>" class="" title="Telefono" tabindex="4"/>
        <br /><br />

        
        <input type="checkbox" id="esSucursal" title="Indica si el proveedor es una sucursal de Lavarrap" 
            <@= (esSucursal==1)? 'checked':'' @> tabindex="5" /> Es sucursal

        <div class="popupButtons">
            <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
            <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
        </div>    
    </form>
</script>