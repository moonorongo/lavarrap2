<script type="text/template" id="insumosTemplate">
        <div style="width: 980px;">
            <div class="dataTableWrapper">
                <table class="display dataTable" id="datatable" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 50%">Descripci&oacute;n</th>
                            <th style="text-align: left; width: 25%">Cant. Mes</th>
                            <th style="text-align: left; width: 25%">Ultima Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- dataTableWrapper -->
            
            <div id="editInsumoPopup">
                <div id="insumosModificarContainer"></div>
            </div>
        </div>
</script>







<script type="text/template" id="insumosModificarTemplate">
    <form id="insumosForm">
        <label for="descripcion">Descripci&oacute;n
            <input type="text" id="descripcion" value="<@= descripcion @>" class="focus obligatorio" title="Descripcion" tabindex="1"/>
        </label>

        <div class="popupButtons">
            <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
            <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
        </div>    
    </form>
</script>