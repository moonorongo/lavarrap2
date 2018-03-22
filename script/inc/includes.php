<?php


// INCLUDE SECTION ------------------------------------------------------------------------------------
// DAOs
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/mysql.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/login.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/clientes.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/servicios.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/insumosServicios.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/insumos.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/proveedores.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/pedidos.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/cuentaCorriente.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/serviciosPedidos.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/caja.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/reportes.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/dao/pedidosCaja.php");
    
    
// Services    
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/clientesService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/serviciosService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/insumosService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/proveedoresService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/pedidosService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/cuentaCorrienteService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/tareasService.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/services/reportesService.php");
    
// Auxiliares
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/fn.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/class.pdf_tickets.php");
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/underscore.php"); 
    
    
    
?>
