<script type="text/template" id="ABMButtonsCuentaCorrienteTemplate">
    <div class="ABMButtons">
        <button class="ABMButton" id="ingresarPagoCuentaCorriente">Pago</button>
        <button class="ABMButton" id="ingresarCorreccionCuentaCorriente">Correccion</button>
        <select id="codigoClienteCuentaCorriente" style="width: 140px; margin-left: 15px;">
            <option value="-99">Seleccione Cliente...</option>
        <?php 
            global $_SUCURSAL;
            $mysql = Mysql::getInstance();
            $mysql->connect();
            $stmt = $mysql->getStmt("SELECT codigo, CONCAT(nombres, ' ', apellido) AS _nombreApellido FROM clientes
                                    where tieneCuentaCorriente = 1 AND activo = 1 AND codigoSucursal = $_SUCURSAL");
            $stmt -> execute();
            $stmt -> bind_result($codigo, $_nombreApellido);
            
            while($stmt -> fetch()) {
                echo('<option value="'. $codigo .'">'. $_nombreApellido .'</option>');
            }
            $stmt->close();
            $mysql->close();
        ?>

        </select>

        <select id="monthCuentaCorriente" style="margin-left: 20px">
<?php      $currentMonth = date('n'); 
            for($i = 0; $i < 12; $i++) {
                $selected = ($currentMonth == $i + 1)? " selected ": "";
                echo("<option value='$i' $selected >". mes($i) ."</option>");
             } ?>
        </select>             

        <select id="yearCuentaCorriente">
<?php      $currentYear = date('Y');
            for($i = 2013; $i < 2030; $i++) {
                if($i <= $currentYear) {
                $selected = ($currentYear == $i)? " selected ": "";
                echo("<option value='$i' $selected >". $i ."</option>");
                } else { break; }
             } ?>
        </select>
        
        <strong style="margin-left: 15px;">Saldo:</strong> $ <span id="saldoCC"></span>
        
    </div>
</script>