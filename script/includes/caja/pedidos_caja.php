<?php 
    $success = true;
    $pedidosCajaDao = new PedidosCaja($mysql);
    $caja = new Caja($mysql);

    $cursor = $caja->listAll();


    $i = 0;
    $mysql->begin();

    while($row = $cursor->fetch_assoc()) {
	    preg_match('/(?:\d*\.)?\d+/', $row['observaciones'], $matched);
	    $pedidoId = intval($matched[0]);

	    if($pedidoId != 0) {
	    	$pedidosCajaDao->insertOrUpdate($row['codigo'], $pedidoId);
	    	$i++;
	    }

	    if ($i > 400) {
	    	$mysql->commit(); 
	    	$mysql->begin();
	    	$i = 0;
	    	echo 'Commited 400' . "\n\r";
	    	flush();
	    }
    }

   $mysql->commit(); 

    

