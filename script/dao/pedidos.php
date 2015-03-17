<?php
    class Pedidos {
        
        private $mysql = null;
        private $codigoSucursal = null;
                
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }


        
        private function getNewId() {
            $result = $this->mysql->query("SELECT codigo FROM pedidos ORDER BY codigo desc limit 1");
            if($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $out = $row["codigo"] + 1;
                }
            } else {
                $out = 1;
            }
            return $out;
        }
        
        

        
        
        public function create($modelData) {
        
            $newId = $this->getNewId();
            $sql = "INSERT INTO pedidos (codigo) VALUES ($newId)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $newId;
            $this->update($modelData);
            
            return $modelData;
        }

        
        
        public function update($modelData) {
            
            $sql = "UPDATE pedidos SET 
                    fechaRetiro = ? ,
                    codigoCliente = ?, 
                    codigoSucursal = ?,
                    nombre = ?,
                    anticipo = ?,
                    observaciones = ? WHERE codigo = ?"; 
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt->bind_param("siisdsi", $modelData['fechaRetiro'], 
                                      $modelData['codigoCliente'], 
                                      $this->codigoSucursal, 
                                      $modelData['nombre'],
                                      $modelData['anticipo'],
                                      $modelData['observaciones'],
                                      $modelData['codigo']);
            $stmt->execute();
        }


        
        public function delete($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE pedidos SET activo = 0 WHERE codigo = ?");
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
        }
        


        
        
        public function get($codigo) {
            
            $stmt = $this->mysql->getStmt("SELECT p.fechaPedido, p.fechaRetiro, p.codigoCliente, 
                p.activo, CONCAT(c.nombres, ' ', c.apellido) as _nombreCliente, p.anticipo, p.nombre, p.observaciones
                FROM pedidos p INNER JOIN clientes c ON p.codigoCliente = c.codigo 
                WHERE p.codigo = ?");
            
            $stmt -> bind_param("i", $codigo);
            $stmt -> execute();
            $stmt -> bind_result($fechaPedido, $fechaRetiro, $codigoCliente, $activo, $_nombreCliente, $anticipo, $nombre, $observaciones);
            
            $stmt -> fetch();
            $out = array("codigo" => $codigo,
                           "fechaPedido" => $fechaPedido,
                           "fechaRetiro" => $fechaRetiro,
                           "codigoCliente" => $codigoCliente,
                           "activo" => $activo,
                           "_nombreCliente" => $_nombreCliente,
                           "anticipo" => $anticipo,
                           "nombre" => $nombre,
                           "observaciones" => $observaciones
                          );
            
            $stmt -> close();
            return $out;
        }
        

        
        

        public function listAll($entregado, $fechaPedido) {
            
            if($entregado == 0) {
                $whereFechaPedido = ($fechaPedido != "")? " DATE(p.fechaPedido) = '$fechaPedido'" : " true";
            } else {
                $whereFechaPedido = ($fechaPedido != "")? " DATE(p.fechaRetiro) = '$fechaPedido'" : " true";
            }
            
            $sql = "SELECT p.codigo, p.fechaPedido, c.nombres, c.apellido, c.telefono, p.fechaRetiro, 
                (SELECT count(codigoProveedor) FROM serviciosPedidos WHERE codigoPedido = p.codigo AND codigoProveedor is not null) AS _cantDerivaciones,
                (SELECT count(codigo) FROM serviciosPedidos WHERE codigoPedido = p.codigo AND codigoEstado > 2)  AS _cantProcesado,
                (SELECT count(codigo) FROM serviciosPedidos WHERE codigoPedido = p.codigo)  AS _cantTotal,
                pr.prefijoCodigo, c.direccion 
                FROM pedidos p 
                INNER JOIN clientes c ON p.codigoCliente = c.codigo 
                INNER JOIN proveedores pr ON p.codigoSucursal = pr.codigo
                WHERE p.codigoSucursal = $this->codigoSucursal 
                      AND p.entregado = $entregado 
                      AND p.activo = 1 
                      AND p.nombre IS NULL 
                      AND $whereFechaPedido 
                      AND YEAR(NOW()) = YEAR(p.fechaPedido) 
                ORDER BY p.fechaRetiro DESC LIMIT 300";

            $out = Array();
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> execute();
            $stmt -> bind_result($codigo, $fechaPedido, $nombres, $apellido, $telefono, $fechaRetiro, $_cantDerivaciones, $_cantProcesado, $_cantTotal, $prefijoCodigo, $direccion);
            

            while($stmt -> fetch()) {
                $tmpRow = array("codigo" => $codigo,
                           "fechaPedido" => explode(" ", $fechaPedido),
                           "nombres" => $nombres,
                           "apellido" => $apellido,
                           "telefono" => $telefono,
                           "fechaRetiro" => $fechaRetiro,
                           "_cantDerivaciones" => $_cantDerivaciones,
                           "_cantProcesado" => $_cantProcesado,
                           "_cantTotal" => $_cantTotal,
                           "prefijoCodigo" => $prefijoCodigo,
                            "direccion" => $direccion
                          );
                $out[] = $tmpRow;
            }
            
            $stmt -> close();
            return $out;
        }        
    

        
        
        
        
        public function listTemplates() {
            
            $sql = "SELECT p.codigo, p.nombre
                FROM pedidos p 
                WHERE p.codigoSucursal = $this->codigoSucursal 
                      AND p.activo = 1 
                      AND p.nombre IS NOT NULL 
                ORDER BY p.nombre ASC";
            
            $out = Array();
            
            $stmt = $this->mysql->getStmt($sql);
            $stmt -> execute();
            $stmt -> bind_result($codigo, $nombre);
            

            while($stmt -> fetch()) {
                $tmpRow = array("codigo" => $codigo,
                           "nombre" => $nombre
                          );
                $out[] = $tmpRow;
            }
            
            $stmt -> close();
            return $out;
        }        
        
        
        
        
        
        
        
        public function entregar($codigo) {
            $stmt = $this->mysql->getStmt("UPDATE pedidos 
                                            SET entregado = 1, fechaRetiro='". date("Y-m-d") ."' WHERE codigo = ?");
            
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
        }
        
        

        

        
        
        
    }
?>