<?php
    class ServiciosPedidos {
        
        private $mysql = null;
        private $codigoSucursal = null;
                
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM serviciosPedidos ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $out = $row["codigo"] + 1;
                }
            } else {
                $out = 1;
            }
            return $out;
        }
        
        
        public function listAll($codigoEstado) {
            
            $condicionEstado = ($codigoEstado != -1)? " AND (sp.codigoEstado = ". $codigoEstado .")" : " AND (sp.codigoEstado < 4)";

            $sql = "SELECT sp.codigo, p.fechaRetiro AS _fechaRetiro, 
                    s.descripcion AS _descripcionServicio, 
                    sp.cantidad, 
                    pr.descripcion AS _descripcionDerivacion, 
                    e.descripcion AS _descripcionEstado, 
                    p.codigoSucursal, 
                    sp.codigoProveedor, 
                    pr2.descripcion AS _descripcionSucursal,
                    sp.codigoEstado,
                    pr2.prefijoCodigo,
                    sp.codigoPedido,
                    pr.esSucursal
             FROM serviciosPedidos sp 
             INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
             INNER JOIN servicios s ON sp.codigoServicio = s.codigo
             INNER JOIN estados e ON sp.codigoEstado = e.codigo
             INNER JOIN proveedores pr2 ON p.codigoSucursal = pr2.codigo
             LEFT JOIN proveedores pr ON sp.codigoProveedor = pr.codigo
             WHERE ((p.codigoSucursal = $this->codigoSucursal) OR 
                   (sp.codigoProveedor = $this->codigoSucursal AND p.codigoSucursal <> $this->codigoSucursal)) ". $condicionEstado;
            

            
            $out = Array();
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> execute();
            
            $stmt -> bind_result($codigo, 
                    $_fechaRetiro, 
                    $_descripcionServicio, 
                    $cantidad, 
                    $_descripcionDerivacion, 
                    $_descripcionEstado,
                    $codigoSucursal,
                    $codigoProveedor,
                    $_descripcionSucursal,
                    $codigoEstado,
                    $prefijoCodigo,
                    $codigoPedido,
                    $esSucursal);
            
            while($stmt -> fetch()) {
                $tmpRow = array("codigo" => $codigo,
                           "_fechaRetiro" => $_fechaRetiro,
                           "_descripcionServicio" => $_descripcionServicio,
                           "cantidad" => $cantidad,
                           "_descripcionDerivacion" => $_descripcionDerivacion,
                           "_descripcionEstado" => $_descripcionEstado,
                           "codigoSucursal" => $codigoSucursal,
                           "codigoProveedor" => $codigoProveedor,
                           "_descripcionSucursal" => $_descripcionSucursal,
                           "codigoEstado" => $codigoEstado,
                           "prefijoCodigo" => $prefijoCodigo,
                           "codigoPedido" => $codigoPedido,
                           "esSucursal" => $esSucursal
                          );
                $out[] = $tmpRow;
            }
            
            $stmt -> close();
            return $out;
        }        
        

        
        
        
        
        public function get($codigo) {
            
            $stmt = $this->mysql->getStmt("SELECT sp.codigoServicio, sp.codigoPedido, sp.cantidad,sp.codigoProveedor,sp.codigoEstado,    
                                            s.descripcion AS _descripcion, 
                                            pr.prefijoCodigo AS _prefijoCodigo, 
                                            CONCAT(c.nombres, ' ', c.apellido) AS _nombreCliente ,
                                            s.valor AS _valor,
                                            p.fechaPedido AS _fechaPedido,
                                            p.observaciones
                                            FROM serviciosPedidos sp
                                            INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                                            INNER JOIN clientes c ON p.codigoCliente = c.codigo
                                            INNER JOIN proveedores pr ON p.codigoSucursal = pr.codigo
                                            INNER JOIN servicios s ON sp.codigoServicio = s.codigo
                                            WHERE sp.codigo = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
            
            $stmt -> bind_result($codigoServicio, 
                    $codigoPedido,
                    $cantidad,
                    $codigoProveedor,
                    $codigoEstado,
                    $_descripcion,
                    $_prefijoCodigo,
                    $_nombreCliente,
                    $_valor,
                    $_fechaPedido,
                    $observaciones);
            

            $stmt -> fetch();
            $ftemp = explode(" ",$_fechaPedido);
            $out = array(
                        "codigo" => $codigo,
                        "codigoServicio" => $codigoServicio, 
                        "codigoPedido" => $codigoPedido,
                        "cantidad" => $cantidad,
                        "codigoProveedor" => $codigoProveedor,
                        "codigoEstado" => $codigoEstado,
                        "_descripcion" => $_descripcion,
                        "_prefijoCodigo" => $_prefijoCodigo,
                        "_nombreCliente" => $_nombreCliente,
                        "_valor" => $_valor,
                        "_fechaPedido" => $ftemp[0],
                        "observaciones" => $observaciones
                    );
            
            $stmt -> close();
            return $out;
        }
        
        
        
        
        
        
        public function getByCodigoPedido($codigoPedido) {
            $out = Array();
            $result = $this->mysql->query("SELECT _sp.*, s.descripcion AS _descripcion , 
                    p.descripcion AS _descripcionProveedor, (_sp.cantidad * s.valor) AS _subTotal 
                    FROM serviciosPedidos _sp 
                    INNER JOIN servicios s ON _sp.codigoServicio = s.codigo 
                    LEFT JOIN proveedores p ON p.codigo = _sp.codigoProveedor
                    WHERE _sp.codigoPedido = ". $codigoPedido);
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            return $out;
        }
        

        
        public function delete($codigo) {
            // esto no es lo mas optimo... 
            $stmt = $this->mysql->getStmt("DELETE FROM serviciosPedidos WHERE codigo =  ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        
        
        public function deleteByCodigoPedido($codigoPedido) {
            $stmt = $this->mysql->getStmt("DELETE FROM serviciosPedidos WHERE codigoPedido =  ?");
            $stmt -> bind_param("i", $codigoPedido);
            $stmt -> execute();
        }
        

        

        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO serviciosPedidos (codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
            $this->update($modelData);
        }

        
  
        
        public function update($modelData) {
            
            $sql = "UPDATE serviciosPedidos SET 
                   codigoServicio = ?,
                   codigoPedido = ?,
                   cantidad = ?,
                   codigoProveedor = ?,
                   codigoEstado = ? 
                   WHERE codigo = ? ";
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("iiiiii", 
                        $modelData['codigoServicio'], 
                        $modelData['codigoPedido'], 
                        $modelData['cantidad'], 
                        $modelData['codigoProveedor'], 
                        $modelData['codigoEstado'], 
                        $modelData['codigo']);
            
            $stmt -> execute();
        }
        
        
        
        public function cambiarEstado($tareasSeleccionadas,$codigoEstado) {
            
            $sql = "UPDATE serviciosPedidos SET codigoEstado = $codigoEstado WHERE codigo = ? ";
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("i", $codigo);
            
            foreach($tareasSeleccionadas AS $codigo) {
                $stmt -> execute();
            }
        }
        
        
        public function derivar($tareasSeleccionadas,$codigoProveedor) {
            $sql = "UPDATE serviciosPedidos SET codigoProveedor = $codigoProveedor WHERE codigo = ? ";
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> bind_param("i", $codigo);
            
            foreach($tareasSeleccionadas AS $codigo) {
                $stmt -> execute();
            }
        }
        
        
        
        
        
    }
  

?>
