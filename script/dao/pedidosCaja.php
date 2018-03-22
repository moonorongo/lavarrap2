<?php
    class PedidosCaja {
        
        private $mysql = null;
                
        function __construct($mysql) {
            $this->mysql = $mysql;
        }
        
       
        public function insertOrUpdate($codigoCaja, $codigoPedido) {
            $sql = "INSERT INTO pedidosCaja (codigoPedido, codigoCaja) VALUES(?, ?) ON DUPLICATE KEY UPDATE codigoPedido=?, codigoCaja=?";
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("iiii", $codigoPedido, $codigoCaja, $codigoPedido, $codigoCaja);
            $stmt->execute();
        }
    }

    
  

?>
