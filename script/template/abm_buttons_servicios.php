<script type="text/template" id="ABMButtonsServiciosTemplate">
    <div class="ABMButtons">
        <button class="ABMButton" id="nuevo">Nuevo</button>
        <button class="ABMButton enableDisable" id="modificar">Modificar</button>
        <button class="ABMButton enableDisable" id="borrar">Borrar</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <strong>Fecha Vigencia:</strong>
<?php 
        $mysql = Mysql::getInstance();
        $mysql->connect();
        $servicios = new Servicios($mysql);
        $listaFechasVigencia = $servicios->listFechasVigencia();
        $ultimaFechaVigencia = $listaFechasVigencia[0]["fechaVigencia"];
?>
            <span id="fechaVigencia"><?= $ultimaFechaVigencia ?></span>
<?php   $mysql->close(); ?>            
        
        
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button class="ABMButton" id="nuevaFechaVigencia">Nueva Fecha Vigencia</button>
    </div>
</script>