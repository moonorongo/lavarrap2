<?php
    require_once($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php/Logger.php");
    Logger::configure($_SERVER["DOCUMENT_ROOT"] ."/script/inc/log4php.xml");

    class Pedidos {
        
        private $mysql = null;
        private $columns = Array("p.codigo", "c.nombres","c.apellido");
        private $codigoSucursal = null;
        private $log = null;

                
        function __construct($mysql) {
            global $_SUCURSAL;
            $this->log = Logger::getLogger('rootLogger');
            $this->codigoSucursal = $_SUCURSAL;
            $this->mysql = $mysql;
        }
        
        public function create($modelData) {
            $sql = "INSERT INTO pedidos (nombre) VALUES (null)"; 
            $result = $this->mysql->query($sql);
            $modelData["codigo"] = $this->mysql->getLastId();
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
        

        
        
/*
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
                ORDER BY p.fechaRetiro DESC LIMIT 300";

//AND YEAR(NOW()) = YEAR(p.fechaPedido) 
                
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
  */  

        public function count($entregado, $fechaPedido, $sSearch) {
            $searchCondition = " ( ";
            foreach ($this->columns as $key => $value) {
                $or = ($key == 0)? " ":" OR ";
                $searchCondition .= $or ."(UPPER(". $value .") LIKE '%". $sSearch ."%') ";
            }
            $searchCondition .= " ) ";
            
            $aBuscar = (strlen($sSearch) == 0)? " (1 = 1) " : $searchCondition;
            
            if($entregado == 0) {
                $whereFechaPedido = ($fechaPedido != "")? " (DATE(fechaPedido) = '$fechaPedido') " : " (1 = 1) ";
            } else {
                $whereFechaPedido = ($fechaPedido != "")? " (DATE(fechaRetiro) = '$fechaPedido') " : " (1 = 1) ";
            }

            $sql = 
                "SELECT 
                   count(p.codigo) AS total
                FROM  pedidos p 
                INNER JOIN clientes c ON p.codigoCliente = c.codigo 
                WHERE 
                 (p.codigoSucursal = $this->codigoSucursal)
                 AND (p.entregado = $entregado)
                 AND $whereFechaPedido 
                 AND $aBuscar 
                 AND (p.activo = 1)
                 AND (p.nombre IS NULL)";
            
            $result = $this->mysql->query($sql);
            
            if ($row = $result->fetch_assoc()) {
                $out = $row["total"];
            }
            return $out;
        }


        public function getPagedSorted($sSearch, $start, $length, $entregado, $fechaPedido) {

            if($entregado == 0) {
                $whereFechaPedido = ($fechaPedido != "")? " DATE(p.fechaPedido) = '$fechaPedido' " : " true ";
            } else {
                $whereFechaPedido = ($fechaPedido != "")? " DATE(p.fechaRetiro) = '$fechaPedido' " : " true ";
            }

            $sSearch = strtoupper($sSearch);
            // Armo query busqueda en base a configuracion columns
            $searchCondition = " ( ";
            foreach ($this->columns as $key => $value) {
                $or = ($key == 0)? " ":" OR ";
                $searchCondition .= $or ."(UPPER(". $value .") LIKE '%". $sSearch ."%') ";
            }
            $searchCondition .= " ) ";
            
            $aBuscar = (strlen($sSearch) == 0)? " (1 = 1) " : $searchCondition;
       
            $out = Array();
            
            $sql =  "SELECT 
                        p.codigo, p.fechaPedido, c.nombres, c.apellido, c.telefono, p.fechaRetiro, 
                        (SELECT count(codigoProveedor) FROM serviciosPedidos WHERE codigoPedido = p.codigo AND codigoProveedor is not null) AS _cantDerivaciones,
                        (SELECT count(codigo) FROM serviciosPedidos WHERE codigoPedido = p.codigo AND codigoEstado > 2)  AS _cantProcesado,
                        (SELECT count(codigo) FROM serviciosPedidos WHERE codigoPedido = p.codigo)  AS _cantTotal,
                        pr.prefijoCodigo, c.direccion 
                    FROM  pedidos p 
                    INNER JOIN clientes c ON p.codigoCliente = c.codigo 
                    INNER JOIN proveedores pr ON p.codigoSucursal = pr.codigo
                    WHERE 
                      p.codigoSucursal = $this->codigoSucursal 
                      AND p.entregado = $entregado 
                      AND p.activo = 1 
                      AND p.nombre IS NULL 
                      AND $whereFechaPedido
                      AND $aBuscar 
                    ORDER BY p.fechaPedido DESC, 
                             p.CODIGO DESC 
                    LIMIT $start, $length";

//            $this->log->warn($sql);
                    
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