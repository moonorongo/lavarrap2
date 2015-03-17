<script type="text/template" id="serviciosTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 75%">Descripcion</th>
                            <th style="text-align: left; width: 25%">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="editServicioPopup">
                <div id="serviciosModificarContainer" class="popupContainer"></div>
            </div>
        </div>            
</script>







<script type="text/template" id="serviciosModificarTemplate">
    <form id="serviciosForm">
        <label for="descripcion">Descripcion
            <input type="text" id="descripcion" value="<@= descripcion @>" class="focus obligatorio" title="Descripcion" tabindex="1"/>
        </label>
        <label for="valor" style="margin-left: 97px;">Valor
            <input type="text" 
                   id="valor" 
                   value="<@= valor @>" 
                   class="obligatorio onlyNumbers submitOnEnter" 
                   title="Valor" 
                   style="width: 50px"
                   tabindex="2" />
        </label>
        
        <div id="insumosServiciosContainer">
            <ul class="headerLista" style="width: 434px;">
                <li>
                    <div class="floatLeft" style="width: 195px">Descripci&oacute;n</div>
                    <div class="floatLeft" style="width: 55px">Cant.</div>
                    <div class="floatLeft" style="width: 169px; text-align: center">Acciones</div>                
                </li>
            </ul>        
            <ul id="listaInsumosServiciosContainer" class="listaItemsContainer"></ul>
            <ul class="ulRow"> 
                <div class="view">
                    <button class="button" id="addItem" tabindex=999><i class="icon-plus"></i></button>
                </div>
            </ul>
        </div>

        <div class="popupButtons">
            <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
            <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
        </div>    
    </form>
</script>





<script type="text/template" id="InsumosServiciosRowTemplate">
    <div class="view">
        <div class="floatLeft" style="width: 195px"><@= _descripcion  @>&nbsp;</div>
        <div class="floatLeft" style="width: 55px"><@= cantidad @>&nbsp;</div>
        <div class="floatLeft" style="width: 155px; text-align: right">
            <button id="edit" class="button smallButton" title="Editar"><i class="icon-edit"></i></button> 
            <button id="delete" class="button smallButton" title="Borrar"><i class="icon-trash"></i></button>
        </div>
    </div>
    <div class="edit">
        <div class="floatLeft" style="width: 195px"">
            <select id="codigoInsumo" class="focusThis" tabindex="5" style="width: 180px">
                <@ _.each(main.servicios.model.get("listaInsumosCombo"), function(e){ @>
                  <option value="<@= e.codigo @>"><@= e.descripcion @></option>
                <@ }) @>
            </select>
        </div>
        <div class="floatLeft" style="width: 55px">
            <input type="text" id="cantidad" tabindex="6" value="<@= cantidad @>" style="width: 30px" />
        </div>
        <div class="floatLeft" style="width: 155px; text-align: right">
            <button id="ok" class="button smallButton" tabindex="7"><i class="icon-ok"></i></button>
            <button id="cancel" class="button smallButton" tabindex="8" ><i class="icon-remove"></i></button>
        </div>
    </div>
</script>


