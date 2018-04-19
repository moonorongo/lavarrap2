<?php
    class Reportes {
        
        private $mysql = null;
        private $codigoSucursal = null;
        
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        
        
        
        public function facturadoDiario($fecha) {
            $stmt = $this->mysql->getStmt("SELECT sum(s.valor * sp.cantidad) AS facturado FROM serviciosPedidos sp
                INNER JOIN servicios s ON sp.codigoServicio = s.codigo
                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                WHERE p.nombre is null AND p.codigoSucursal = $this->codigoSucursal 
                    AND DATE(p.fechaPedido) = DATE('$fecha')");

            $stmt->execute();
            $stmt->bind_result($facturado);
            $stmt->fetch();

            $stmt->close();            
            return $facturado;            
        }
        
        
        public function reporteInsumos($fechaInicial, $fechaFinal) {
            $outConsumidos = Array();
            $stmtConsumidos = $this->mysql->getStmt("
                                            SELECT codigo, SUM(subTotalInsumo) AS totalInsumoConsumido FROM (
                                                SELECT i.codigo, SUM(sp.cantidad) * ins.cantidad AS subTotalInsumo 
                                                FROM servicios s
                                                INNER JOIN serviciosPedidos sp ON sp.codigoServicio = s.codigo
                                                INNER JOIN insumosServicios ins ON ins.codigoServicio = s.codigo
                                                INNER JOIN insumos i ON ins.codigoInsumo = i.codigo
                                                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                                                WHERE s.codigoSucursal = ? AND s. activo = 1 
                                                AND DATE(fechaPedido) BETWEEN ? AND ?
                                                GROUP BY i.codigo, ins.cantidad
                                                ) t
                                            GROUP BY codigo                
                                        ");
            
            
            $stmtConsumidos->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmtConsumidos->execute();
            
            $stmtConsumidos->bind_result($codigoInsumo, $totalInsumoConsumido);

            while($stmtConsumidos->fetch()) {
                //$tmpRow = array($codigoInsumo => $totalInsumoConsumido);
                $outConsumidos[$codigoInsumo] = $totalInsumoConsumido;
            }
            
            $stmtConsumidos->close();            
            
            
            
            $out = Array();
            $stmt = $this->mysql->getStmt("SELECT i.descripcion, SUM(ii.cantidad) AS totalIngresado, i.codigo AS codigoInsumo FROM insumosIngresos ii
                                            INNER JOIN insumos i ON ii.codigoInsumo = i.codigo
                                            WHERE ( 
                                                ii.codigoSucursal = ? AND 
                                                ii.fechaIngreso >= ? AND 
                                                ii.fechaIngreso <= ADDDATE(?, INTERVAL 1 DAY) 
                                            ) 
                                            GROUP BY i.descripcion, i.codigo
                                            ORDER BY i.descripcion
                                        ");
            
            
            $stmt->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmt->execute();
            
            $stmt->bind_result($descripcion, $totalIngresado, $codigoInsumo);

            while($stmt->fetch()) {
                $tmpRow = array("descripcion" => $descripcion, 
                                "totalIngresado" => $totalIngresado,
                                "totalConsumido" => $outConsumidos[$codigoInsumo]
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            
            return $out;
        }        
        
        
        
        
        
            
        
        public function reporteDerivaciones($fechaInicial, $fechaFinal) {
            $out = Array();
            
            $stmt = $this->mysql->getStmt("
                SELECT sp.codigoServicio, sp.codigoProveedor, s.descripcion AS _descripcionServicio, SUM(sp.cantidad) AS cantidad, pr.descripcion AS  _descripcionProveedor
                FROM serviciosPedidos sp
                INNER JOIN servicios s ON sp.codigoServicio = s.codigo
                INNER JOIN proveedores pr on sp.codigoProveedor = pr.codigo
                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                WHERE sp.codigoProveedor IS NOT null AND 
                    sp.codigoProveedor != ? AND 
                    p.fechaPedido >= ? AND 
                    p.fechaPedido <= ADDDATE(?, INTERVAL 1 DAY) 
                GROUP BY sp.codigoServicio, sp.codigoProveedor
                ORDER BY sp.codigoProveedor
            ");
            
            $stmt->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmt->execute();
            
            $stmt->bind_result($codigoServicio, $codigoProveedor, $descripcionServicio, $cantidad, $descripcionProveedor);

            while($stmt->fetch()) {
                $tmpRow = array("descripcionServicio" => $descripcionServicio, 
                                "codigoServicio" => $codigoServicio,
                                "codigoProveedor" => $codigoProveedor,
                                "cantidad" => $cantidad,
                                "descripcionProveedor" => $descripcionProveedor
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            return $out;
        }                
        
        

        
        
        
        
        
        public function reporteServiciosRealizados($fechaInicial, $fechaFinal) {
            $out = Array();
            
            $stmt = $this->mysql->getStmt("
                SELECT sp.codigoServicio, s.descripcion AS _descripcionServicio, SUM(sp.cantidad) AS cantidad
                FROM serviciosPedidos sp
                INNER JOIN servicios s ON sp.codigoServicio = s.codigo
                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                WHERE sp.codigoProveedor IS null AND 
                    sp.codigoEstado = 4 AND
                    p.codigoSucursal = ? AND 
                    p.fechaPedido >= ? AND 
                    p.fechaPedido <= ADDDATE(?, INTERVAL 1 DAY) 
                GROUP BY sp.codigoServicio
            ");
            
            $stmt->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmt->execute();
            
            $stmt->bind_result($codigoServicio, $descripcionServicio, $cantidad);

            while($stmt->fetch()) {
                $tmpRow = array("descripcionServicio" => $descripcionServicio, 
                                "codigoServicio" => $codigoServicio,
                                "cantidad" => $cantidad
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            return $out;
        }                
        

        
        
        
        
        
        public function reporteListaServicios($fechaInicial, $fechaFinal) {
            $out = Array();
            
            $stmt = $this->mysql->getStmt("
                SELECT DATE(p.fechaPedido) AS fechaPedido, CONCAT_WS(' ',c.nombres,c.apellido) AS nombresApellido, 
                s.descripcion AS descripcionServicio ,sp.cantidad, e.descripcion AS descripcionEstado
                FROM serviciosPedidos sp
                INNER JOIN servicios s ON sp.codigoServicio = s.codigo
                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                INNER JOIN estados e ON sp.codigoEstado = e.codigo
                INNER JOIN clientes c ON p.codigoCliente = c.codigo
                WHERE p.codigoSucursal = ? AND 
                DATE(p.fechaPedido) BETWEEN ? AND ?
                ORDER BY p.fechaPedido DESC
            ");
            
            $stmt->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmt->execute();
            
            $stmt->bind_result($fechaPedido, $nombresApellido, $descripcionServicio, $cantidad, $descripcionEstado);

            while($stmt->fetch()) {
                $tmpRow = array("fechaPedido" => $fechaPedido, 
                                "nombresApellido" => $nombresApellido,
                                "descripcionServicio" => $descripcionServicio,
                                "cantidad" => $cantidad,
                                "descripcionEstado" => $descripcionEstado
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            return $out;
        }                
        
        


        
        public function reporteCumpleanos($fechaInicial) {
            $out = Array();
            $aFecha = explode('-',$fechaInicial);
            
            // primer pasada, los que quedan por cumplir
            $stmt = $this->mysql->getStmt("
                SELECT CONCAT_WS(' ',nombres, apellido) as nombresApellidos, fechaNacimiento, direccion, telefono, MONTH(fechaNacimiento) - 1 AS mes FROM clientes
                WHERE 
                ( fechaNacimiento IS NOT NULL AND codigoSucursal = ? AND activo = 1 ) AND
                ( (MONTH(fechaNacimiento) = ? AND DATE(fechaNacimiento) >= ?) OR (MONTH(fechaNacimiento) > ?) )
                ORDER BY MONTH(fechaNacimiento), DATE(fechaNacimiento) ASC;
            ");
            
            $stmt->bind_param("iiii",$this->codigoSucursal, $aFecha[1], $aFecha[2], $aFecha[1]);
            $stmt->execute();
            
            $stmt->bind_result($nombresApellido, $fecha, $direccion, $telefono, $mes);

            while($stmt->fetch()) {
                $tmpRow = array("nombresApellido" => $nombresApellido, 
                                "fecha" => $fecha,
                                "direccion"=> $direccion,
                                "telefono" => $telefono,
                                "mes" => $mes
                    );
                $out[] = $tmpRow;
            }

            $stmt->close();            

            
            // segunda pasada, los cumplieron, para el año que viene
            $stmt2 = $this->mysql->getStmt("
                SELECT CONCAT_WS(' ',nombres, apellido) as nombresApellidos, fechaNacimiento, direccion, telefono, MONTH(fechaNacimiento) - 1 AS mes FROM clientes
                WHERE 
                ( fechaNacimiento IS NOT NULL AND codigoSucursal = ? AND activo = 1 ) AND
                ( (MONTH(fechaNacimiento) = ? AND DATE(fechaNacimiento) < ?) OR (MONTH(fechaNacimiento) < ?) )
                ORDER BY MONTH(fechaNacimiento), DATE(fechaNacimiento) ASC;
            ");
            
            $stmt2->bind_param("iiii",$this->codigoSucursal, $aFecha[1], $aFecha[2], $aFecha[1]);
            $stmt2->execute();
            
            $stmt2->bind_result($nombresApellido, $fecha, $direccion, $telefono, $mes);

            while($stmt2->fetch()) {
                $tmpRow = array("nombresApellido" => $nombresApellido, 
                                "fecha" => $fecha,
                                "direccion"=> $direccion,
                                "telefono" => $telefono,
                                "mes" => $mes
                    );
                $out[] = $tmpRow;
            }
            
            
            $stmt2->close();            
            return $out;
        }         
        
        
        
        public function reporteConsumos($fechaInicial, $fechaFinal) {
            $out = Array();
            
            $stmt = $this->mysql->getStmt("
                SELECT CONCAT_WS(' ',c.nombres, c.apellido) as nombresApellido, SUM(sp.cantidad * s.valor) AS consumo
                FROM serviciosPedidos sp 
                INNER JOIN pedidos p ON sp.codigoPedido = p.codigo
                INNER JOIN servicios s on sp.codigoServicio = s.codigo
                INNER JOIN clientes c on p.codigoCliente = c.codigo
                WHERE p.codigoSucursal = ? AND DATE(p.fechaPedido) BETWEEN ? AND ?
                GROUP BY c.codigo
                ORDER BY SUM(sp.cantidad * s.valor) DESC
            ");
            
            $stmt->bind_param("iss",$this->codigoSucursal, $fechaInicial, $fechaFinal);
            $stmt->execute();
            
            $stmt->bind_result($nombresApellido, $consumo);

            while($stmt->fetch()) {
                $tmpRow = array("nombresApellido" => $nombresApellido, 
                                "consumo" => $consumo
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            return $out;
        }         
        
        
        
        
         public function exportarListaClientes() {
            $out = Array();
            
            $stmt = $this->mysql->getStmt("
                SELECT CONCAT_WS(', ', apellido, nombres) as nombresApellido, direccion, telefono, fechaNacimiento, email FROM clientes
                WHERE codigoSucursal = ? AND activo = 1
                ORDER BY apellido 
            ");
            
            $stmt->bind_param("i",$this->codigoSucursal);
            $stmt->execute();
            
            $stmt->bind_result($nombresApellido, $direccion, $telefono, $fechaNacimiento, $email);

            while($stmt->fetch()) {
                $tmpRow = array("nombresApellido" => $nombresApellido, 
                                "direccion" => $direccion,
                                "telefono" => $telefono,
                                "fechaNacimiento" => ($fechaNacimiento != null)? swapDateFormat($fechaNacimiento) : "",
                                "email" => $email
                    );
                $out[] = $tmpRow;
            }
            
            $stmt->close();            
            return $out;
        }        
        




        
        
    }

    
    
?>