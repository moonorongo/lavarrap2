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
            <input type="checkbox" id="verEntregado" /> Entregados
            <input type="text" id="fechaPedidos" style="width: 70px;" />
            <button class="ABMButton" id="reset" style="padding: 3px 5px;font-size: 14px;margin-left: 8px;"><i class="icon-refresh"></i></button>
        <?php } ?>
    </div>
</script>