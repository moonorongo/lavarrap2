<?php 
    $success = true;
    $pedidosCajaDao = new PedidosCaja($mysql);
    $caja = new Caja($mysql);
	$codigo = (isset($_REQUEST["codigo"]) && !empty($_REQUEST["codigo"]))? $_REQUEST["codigo"] : null;

	if(!is_null($codigo)) {
    	$pedidosCajaDao->delete($codigo);
		$caja->delete($codigo);
	} else {
		$success = false;
	}

	echo '{"success" : '. $success .'}';

