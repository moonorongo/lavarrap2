<?php 
    session_start();
    
    if(!isset($_SESSION['LOGGED'])) {
        header('Location: http://'. $_SERVER['SERVER_NAME']); 
    } else {
    
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/config.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/header.php");

        
        // - - - - - - - - - - - TEMPLATES - - - - - - - - - - - - -     
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/main.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/clientes.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/servicios.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/abm_buttons.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/abm_buttons_pedidos.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/abm_buttons_insumos.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/abm_buttons_cuentaCorriente.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/abm_buttons_servicios.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/tfd_buttons.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/insumos.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/proveedores.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/pedidos.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/cuentaCorriente.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/tareas.php");
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/template/caja.php");
        
        
        
        require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/footer.php");

    }    

?>

