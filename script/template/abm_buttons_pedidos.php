<script type="text/template" id="ABMButtonsPedidosTemplate">
    <div class="ABMButtons">
        <button class="ABMButton" id="nuevo">Nuevo</button>
        <button class="ABMButton" id="nuevoDesdePlantilla">Desde Plantilla</button>
        <button class="ABMButton enableDisable" id="modificar">Modificar</button>
        <?php if(isAdmin()) { ?>
            <button class="ABMButton enableDisable" id="borrar">Borrar</button>
        <?php } ?>
        
        <button class="ABMButton enableDisable" id="entregar" style="margin-left: 20px;">Entregar</button>

        <?php if(isAdmin()) { ?>
            <select id="filtroDt">
                <option value="0">Sin Entregar</option>
                <option value="1">Entregados</option>
                <option value="2">Plantillas</option>
            </select>

            <input type="text" id="fechaPedidos" style="width: 70px;" class="filtroFecha" />
            <button class="ABMButton filtroFecha" id="reset" style="padding: 3px 5px;font-size: 14px;margin-left: 8px;"><i class="icon-refresh"></i></button>


        <?php } ?>
    </div>

</script>