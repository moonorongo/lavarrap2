<script type="text/template" id="pedidosTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 9%">C&oacute;digo</th>
                            <th style="text-align: left; width: 9%">Fecha</th>
                            <th style="text-align: left; width: 24%">Nombre</th>
                            <th style="text-align: left; width: 24%">Direcci&oacute;n</th>
                            <th style="text-align: left; width: 10%">Tel&eacute;fono</th>
                            <th style="text-align: left; width: 9%">F. retiro</th>
                            <th style="text-align: left; width: 8%">Derivaci&oacute;n</th>
                            <th style="text-align: left; width: 6%">%</th>
                            <th style="text-align: left; width: 10%">CC</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="editPedidoPopup">
                <div id="pedidosModificarContainer" class="popupContainer"></div>
            </div>
            
            <div id="entregarPedidoPopup">
                <div id="entregarPedidoContainer" class="popupContainer"></div>
            </div>  
        </div>            
</script>







<script type="text/template" id="pedidosModificarTemplate">
    <form id="pedidosForm">
        <div class="<@= (esPlantilla)? 'hide' : '' @>">
            <label for="codigoTalon">C&oacute;digo
                <input type="text" id="codigoTalon" value="<@= codigoTalon @>" 
                class="obligatorio" title="Codigo de talon" tabindex="0" style="width: 65px;margin-right: 8px;" />
            </label>

            <label for="fechaRetiro">A retirar
                <input type="text" id="fechaRetiro" value="<@= wcat.swapDateFormat(fechaRetiro) @>" 
                class="obligatorio" title="Fecha de retiro" tabindex="10" style="width: 65px;margin-right: 8px;" />
            </label>

            <label for="codigoCliente" style="margin-left: 20px">Cliente 
                <select id="codigoCliente" style="display:none" class="obligatorio" title="Cliente" <@= (!isNew)? " disabled " : "" @> ></select>
                <input id="searchCliente" style="width: 130px;" class="focusThis" value="<@= _nombreCliente @>" <@= (!isNew)? " disabled " : "" @> />
            </label>
            <button class="button smallButton" id="addCliente" title="Agregar Cliente" <@= (!isNew)? " disabled " : "" @> ><i class="icon-plus"></i></button>
            
            <label for="anticipo" style="margin-left: 20px">Ant. 
                <input type="text" id="anticipo" value="<@= anticipo @>" 
                            class="onlyNumbers" 
                            min="-1" 
                            title="Anticipo" 
                            tabindex="12" 
                            style="width: 35px" />
            </label>
            <input type="checkbox" id="conDebito" style="margin-left: 10px" />
            <label for="conDebito">Deb.</label>
        </div>
        
        
        <div id="serviciosPedidosContainer">
            <ul class="headerLista" style="width: 704px;">
                <li>
                    <div class="floatLeft" style="width: 295px">Descripci&oacute;n</div>
                    <div class="floatLeft" style="width: 55px; text-align: center">Cant.</div>
                    <div class="floatLeft" style="width: 55px; text-align: center" id="valor">Valor</div>
                    <div class="floatLeft" style="width: 135px; text-align: left" id="valor">Derivar a</div>
                    <div class="floatLeft" style="width: 138px; text-align: left">Acciones</div>                
                </li>
            </ul>
            <ul id="listaServiciosPedidosContainer" class="listaItemsContainer" style="width: 703px; height: 250px"></ul>
            <ul class="ulRow"> 
                <div class="view" style="width: 500px;float: left;">
                    <button class="button" id="addItem" tabindex=1 title="Agregar Servicio"><i class="icon-plus"></i> Servicio</button>
                </div>
                <div class="view" style="width: 180px; float: left; text-align: right; font-weight: bold;">
                    TOTAL: $ <span id="totalFactura"></span>
                </div>
            </ul>
        </div>

        <div id="editClientePopup">
            <div id="clientesModificarContainer"></div>
        </div>        
        
        <div class="clear"></div>
        
        <div style="height: 100px; width: 703px" class="<@= (esPlantilla)? 'hide' : '' @>">
            <textarea id="observaciones" style="width: 675px; height: 70px; margin-top: 15px; margin-bottom: 15px; resize: none" placeholder="Ingrese aqui observaciones"><@= observaciones @></textarea>
        </div>
        
        
        <div class="popupButtons">
            <button id="soloImprimir" class="editButtons hideButton <@= (esPlantilla)? 'hiddenImportant' : '' @>"><i class="icon-print"></i>Imprimir</button>
            <?php if(isAdmin()) { ?>
                <@ if(isNew && esPlantilla) { @>
                <button id="guardarTemplate" class="editButtons hideButton"><i class="icon-save"></i> como plantilla</button>
                <@ } @>
            <?php } ?>
            <button id="imprimir" class="editButtons hideButton <@= (esPlantilla)? 'hiddenImportant' : '' @>"><i class="icon-print"></i>Ok &amp; Imprimir</button>
            <button id="aceptar" class="editButtons hideButton <@= (isNew && esPlantilla)? 'hiddenImportant' : '' @>"><i class="icon-ok"></i>Ok</button>
            <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
        </div>    
    </form>
</script>





<script type="text/template" id="serviciosPedidosRowTemplate">
    <div class="view">
        <div class="floatLeft singleRow" style="width: 300px"><@= _descripcion  @>&nbsp;</div>
        <div class="floatLeft" style="width: 55px; text-align: center"><@= cantidad @>&nbsp;</div>
        <div class="floatLeft" style="width: 50px; text-align: right; padding-right: 10px" id="valor"><@= _subTotal.toFixed(2) @></div>
        <div class="floatLeft" style="width: 140px"><@= _descripcionProveedor  @>&nbsp;</div>
        <div class="floatLeft" style="width: 105px; text-align: center">
            <button id="edit" class="button smallButton" title="Editar"><i class="icon-edit"></i></button> 
            <button id="delete" class="button smallButton" title="Borrar"><i class="icon-trash"></i></button>
        </div>
    </div>
    <div class="edit">
        <div class="floatLeft" style="width: 300px" >
            <select id="codigoServicio" class="focusThis" tabindex="2" style="width: 280px">
                <option value="none">Seleccione un servicio...</option>
                <@ _.each(main.pedidos.model.get("listaServiciosCombo"), function(e){ @>
                    <@ var selected = (e.codigo == codigoServicio)? 'selected': ''; @>
                    <option value="<@= e.codigo @>" <@= selected @>><@= e.descripcion @> $<@= e.valor @></option>
                <@ }) @>
            </select>
        </div>
        
        <div class="floatLeft" style="width: 55px; text-align: center">
            <input type="text" id="cantidad" tabindex="3" value="<@= cantidad @>" style="width: 30px" />
        </div>
        
        <div class="floatLeft" style="width: 50px; text-align: right; padding-right: 10px"><@= _subTotal.toFixed(2) @></div>
        
        <div class="floatLeft" style="width: 140px"">
            <select id="codigoProveedor" tabindex="4" style="width: 120px">
                <option value="-1">Sin derivaci&oacute;n</option>
                <@ _.each(main.pedidos.model.get("listaProveedoresCombo"), function(e){ @>
                    <@ var selected = (e.codigo == codigoProveedor)? 'selected': ''; @>
                    <option value="<@= e.codigo @>" <@= selected @>><@= e.descripcion @></option>
                <@ }) @>
            </select>
        </div>
        
        <div class="floatLeft" style="width: 105px; text-align: center">
            <button id="ok" class="button smallButton" tabindex="5" title="Aceptar"><i class="icon-ok"></i></button>
            <button id="cancel" class="button smallButton" tabindex="6" title="Cancelar"><i class="icon-remove"></i></button>
        </div>
    </div>
</script>




<script type="text/template" id="pedidoIncompletoAlertaTemplate">
    El pedido tiene tareas sin realizar! <br /><br />
    
    <@ _.each(listaServicios.toJSON(), function(s) { @>
    <@= s.cantidad @> x <@= s._descripcion @> <strong>(<@= (s.codigoEstado != '3')? 'Sin Finalizar' : 'Finalizado' @>)</strong> <br />
    <@ }) @>
</script>





<script type="text/template" id="entregarPedidoTemplate">
    <div style="width: 162px; float: left">
        <div><input type="radio" name="tipoCliente" checked value="1" /> Consumidor final</div>
        <div><input type="radio" name="tipoCliente" value="2" /> Cuenta Corriente</div>
    </div>                    
    <div style="width: 160px; float: left" id="conDebitoContainer">
        <input type="checkbox" id="conDebito" /> Con d&eacute;bito
    </div>                    

    <table class="serviciosEntregaPedido" id="serviciosEntregaPedido" >
        <tr>
            <th style="width: 230px">Servicio</th>
            <th style="width: 60px;">Monto</th>
        </tr>
    <@ _.each(listaServicios.toJSON(), function(s) { @>
        <tr>
            <td><div style="inline-block; width: 230px;" class="singleRow"><@= s.cantidad @> x <@= s._descripcion @></div></td>
            <td style="text-align: right"><@= s._subTotal.toFixed(2) @></td>
        </tr>
    <@ }) @>
        <tr class="sinBordeInferior detalleConsumidorFinal">
            <td style="text-align: right">Total servicios </td>
            <td style="text-align: right"><@= totalServicios.toFixed(2) @></td>
        </tr>
        <tr class="sinBordeInferior detalleConsumidorFinal">
            <td style="text-align: right">Anticipo </td>
            <td style="text-align: right"><@= anticipo.toFixed(2) @></td>
        </tr>
        <tr class="sinBordeInferior detalleConsumidorFinal">
            <td style="text-align: right">A cobrar </td>
            <td style="text-align: right"><@= aCobrar.toFixed(2) @></td>
        </tr>
        <@ if(aCobrar != 0) { @>
            <tr class="sinBordeInferior detalleConsumidorFinal">
                <td style="text-align: right">Entrega </td>
                <td style="text-align: right"><input type="text" id="montoPagado" value="<@= montoPagado @>" class="onlyNumbers focusThis" title="Anticipo" style="width: 63px" tabindex = 1 /></td>
            </tr>
            <tr class="sinBordeInferior detalleConsumidorFinal">
                <td style="text-align: right">Su Vuelto </td>
                <td style="text-align: right"><span id="vuelto"><@= (_.isNaN(vuelto))? "" : vuelto.toFixed(2) @></span></td>
            </tr>
        <@ } @>
    </table>
    
    
    <div class="popupButtons">
        <button id="aceptar" class="editButtons hideButton" tabindex = 2><i class="icon-print"></i>Aceptar &amp; Imprimir</button>
        <button id="cancelar" class="editButtons hideButton" tabindex = 3><i class="icon-remove"></i>Cancelar</button>
    </div>    

</script>



<script type="text/template" id="seleccionarPlantillaTemplate">
    <select id="codigoTemplate" style="width: 275px;">
    <@ _.each(collection, function(t){ @>
    <option value="<@= t.codigo @>"><@= t.nombre @></option>
    <@ }) @>
    </select>
</script>