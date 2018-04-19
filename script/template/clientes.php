<script type="text/template" id="clientesTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 25%">Nombre</th>
                            <th style="text-align: left; width: 25%">Apellido</th>
                            <th style="text-align: left; width: 30%">Direcci&oacute;n</th>
                            <th style="text-align: center; width: 20%">Telefono</th>
<!--                            <th style="text-align: center; width: 15%">Cumplea&ntilde;os</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="editClientePopup">
                <div id="clientesModificarContainer"></div>
            </div>
        </div>
</script>







<script type="text/template" id="clientesModificarTemplate">
    <form id="clientesForm">
        <label for="nombres">Nombres </label>
        <input type="text" id="nombres" value="<@= nombres @>" class="focus obligatorio" title="Nombre" tabindex="1" style="margin: 4px 0;" />
        <br />
        
        <label for="apellido">Apellido </label>
        <input type="text" id="apellido" value="<@= apellido @>" class="obligatorio" title="Apellido" tabindex="2" style="margin: 4px 0;" />
        <br />
        
        <label for="direccion">Direcci&oacute;n </label>
        <input type="text" id="direccion" value="<@= direccion @>" class="obligatorio" title="Direccion" tabindex="3" style="margin: 4px 0;" />
        <br />
        
        <label for="telefono">Tel&eacute;fono </label>
        <input type="text" id="telefono" value="<@= telefono @>" class="obligatorio" title="Telefono" tabindex="4" style="margin: 4px 0;" />
        <br />

        <label for="email">Email </label>
        <input type="text" id="email" value="<@= email @>" class="obligatorio" title="Email" tabindex="5" style="margin: 4px 0;" />
        <br />
        
        <label for="fechaNacimiento">Cumplea&ntilde;os </label>
        <input type="text" id="fechaNacimiento" value="<@= (!_.isEmpty(fechaNacimiento))? wcat.swapDateFormat(fechaNacimiento) : '' @>" class="obligatorio submitOnEnter" title="Fecha de nacimiento" tabindex="6" style="margin: 4px 0;" />
        <br />
        
        <input type="checkbox" id="tieneCuentaCorriente" <@= (tieneCuentaCorriente == 1)? "checked":"" @> /> tiene Cuenta Corriente

        <div class="popupButtons">
            <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
            <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
        </div>    
    </form>
</script>