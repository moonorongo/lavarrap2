    <!-- MODEL -->
    <script src="static/js/model/main.js"></script>
    <script src="static/js/model/clientes.js"></script>
    <script src="static/js/model/insumosServicios.js"></script>
    <script src="static/js/model/pedidos.js"></script>
    <script src="static/js/model/cuentaCorriente.js"></script>
    <script src="static/js/model/serviciosPedidos.js"></script>
    <script src="static/js/model/tareas.js"></script>
<?php if(isAdmin()) { ?>
    <script src="static/js/model/servicios.js"></script>
    <script src="static/js/model/proveedores.js"></script>
    <script src="static/js/model/insumos.js"></script>
    <script src="static/js/model/movimientosCaja.js"></script>
    <script src="static/js/model/reportes.js"></script>
    <script src="static/js/model/logCaja.js"></script>
    <script src="static/js/model/logPedidos.js"></script>
<?php } ?>

</body>
</html>
