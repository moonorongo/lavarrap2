<script type="text/template" id="TFDButtonsTemplate">
    <div class="ABMButtons">
        <button class="ABMButton enableDisable" id="iniciarLavado">Iniciar</button>
        <button class="ABMButton enableDisable" id="finalizarLavado">Finalizar</button>
        <button class="ABMButton enableDisable" id="derivar">Derivar</button>
        &nbsp;&nbsp;
        Mostrar: 
        <select id="filtroCodigoEstado" style="width: 100px">
        <?php if(isAdmin()) { ?>
            <option value="-1">Todos</option>
        <?php } ?>
            <option value="0">Para lavar</option>
            <option value="2">Lavando</option>
            <option value="3">Para retirar</option>
        <?php if(isAdmin()) { ?>
            <option value="4">Retirado</option>
        <?php } ?>
        </select>        
    </div>
</script>