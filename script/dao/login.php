<?php
    class Login {
        
        private $mysql = null;
        
        function __construct($mysql) {
            $this->mysql = $mysql;
        }
        
        function check($user, $password) {
            $stmt = $this->mysql->getStmt("SELECT u.codigoSucursal, u.esAdministrador, count(u.codigo) AS logged, p.descripcion
                                            FROM usuarios u
                                            INNER JOIN proveedores p ON p.codigo = u.codigoSucursal 
                                            WHERE nombre = ? AND clave = ?  GROUP BY u.codigoSucursal, u.esAdministrador, p.descripcion");
            
            $stmt->bind_param("ss",$user, $password);
            $stmt->execute();
            
            $stmt->bind_result($codigoSucursal, $esAdministrador, $logged, $descripcion);

            $stmt->fetch();
            $out = array("LOGGED" => $logged,
                           "ADMIN" => $esAdministrador,
                           "SUCURSAL" => $codigoSucursal,
                           "DESCRIPCION" => $descripcion
                          );
            
            $stmt->close();            
            return $out;            
        }
        
    }
?>
