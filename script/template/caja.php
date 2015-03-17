<script type="text/template" id="movimientosCajaTemplate">
    <div class="ABMButtons buttonsMovimientosCaja" style="float: none;">
        <select id="month">
<?php      $currentMonth = date('n'); 
            for($i = 0; $i < 12; $i++) {
                $selected = ($currentMonth == $i + 1)? " selected ": "";
                echo("<option value='$i' $selected >". mes($i) ."</option>");
             } ?>
        </select>             

        <select id="year">
<?php      $currentYear = date('Y');
            for($i = 2013; $i < 2030; $i++) {
                if($i <= $currentYear) {
                $selected = ($currentYear == $i)? " selected ": "";
                echo("<option value='$i' $selected >". $i ."</option>");
                } else { break; }
             } ?>
        </select>     
        
        <span class="saldoDeCajaContainer"> Saldo de caja: $ <span id="saldoDeCaja"></span></span>

    </div>
    
    <div style="width: 980px;  margin-top: 5px;">
        <div style="background-color: white; overflow: hidden; overflow-y: scroll;" class="dataTableWrapperCaja">
            <table class="collectionView" id="movimientosCajaContainer">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 15%">Fecha</th>
                        <th style="text-align: center; width: 10%">Ingreso</th>
                        <th style="text-align: center; width: 10%">Egreso</th>
                        <th style="text-align: left; width: 65%">Observaciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="movimientosCajaPopup">
            <div id="movimientosCajaContainer"></div>
        </div>
    </div>    
</script>



<script type="text/template" id="movimientosCajaRowTemplate">
    <td style="text-align: center"><@= wcat.swapDateFormat(fecha) @></td>
    <@ if(monto < 0) { @>
        <td style="text-align: center">&nbsp;</td>
        <td style="text-align: center">$ <@= Math.abs(monto).toFixed(2) @></td>
    <@ } else { @>
        <td style="text-align: center">$ <@= monto @></td>
        <td style="text-align: center">&nbsp;</td>
    <@ } @>
    <td style="text-align: left"><@= observaciones @></td>
</script>






<script type="text/template" id="cajaTemplate">
    <span style="margin-bottom:8px">Monto: </span><input type="text" id="monto" style="width:50px;" class="obligatorio onlyNumbers focusThis" tabindex=1 />
    <br />
    Observaciones<br />
    <textarea id="observaciones" style="width:260px; height: 80px"></textarea>
    
    <div class="popupButtons">
        <button id="aceptar" class="editButtons hideButton"><i class="icon-ok"></i>Aceptar</button>
        <button id="cancelar" class="editButtons hideButton"><i class="icon-remove"></i>Cancelar</button>
    </div>        
</script>



<script type="text/template" id="cerrarCajaTemplate">
        <select id="monthCerrarCaja">
<?php      $currentMonth = date('n'); 
            for($i = 0; $i < 12; $i++) {
                $selected = ($currentMonth == $i)? " selected ": "";
                echo("<option value='$i' $selected >". mes($i) ."</option>");
             } ?>
        </select>             

        <select id="yearCerrarCaja">
<?php      $currentYear = date('Y');
            for($i = 2013; $i < 2030; $i++) {
                if($i <= $currentYear) {
                    $selected = ($currentYear == $i)? " selected ": "";
                    echo("<option value='$i' $selected >". $i ."</option>");
                } else {
                    break;
                }
             } ?>
        </select>     

</script>
