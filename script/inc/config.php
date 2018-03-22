<?php 

// CONFIGURATION SECTION ------------------------------------------------------------------------------
    $defaults_database = Array("database" => "lavarrap", 
                            "url" => "localhost", 
                            "username" => "root", 
                            "password" => "");

                 
    function isAdmin() {
        return ($_SESSION['ADMIN'] == 1)? true:false;
    }

    
    if((isset($_REQUEST['SUCURSAL'])) && (isAdmin())) {
        $_SUCURSAL = $_REQUEST['SUCURSAL'];
        $_SESSION['SUCURSAL'] = $_SUCURSAL;
    } else {
        $_SUCURSAL = $_SESSION['SUCURSAL'];
    }
    
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/includes.php");

?>
