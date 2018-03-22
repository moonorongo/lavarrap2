<?php 
    class Mysql {
        
        private static $instancia;
        private $database = null;
        private $username = null;
        private $password = null;
        private $url = null;
        private $db = null;
        
        private function __construct() {
            global $defaults_database;
            
            $this->url = $defaults_database["url"];
            $this->database = $defaults_database["database"];
            $this->username= $defaults_database["username"];
            $this->password = $defaults_database["password"];
        }
        
        
        public static function getInstance()
           {
              if (!self::$instancia instanceof self) {
                 self::$instancia = new self;
              }
              return self::$instancia;
           }        

        
        public function connect() {
            $this->db = new mysqli($this->url , $this->username, $this->password , $this->database );
            if ($this->db->connect_errno) {
                echo "Fallo al conectar a MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error;
            }
        }


        public function begin() {
            $this->db->begin_transaction();
        }


        public function commit() {
            $this->db->commit();
        }


        public function rollback() {
            $this->db->rollback();
        }

        
        
        public function getDb() {
            return $this->db;
        }
        
        public function getStmt($sql) {
            return $this->db->prepare($sql);
        }
    
    
        public function query($query) {
            return $this->db->query($query);
        }
    
        public function close() {
            $this->db->close();
        }
        
        public function getLastId() {
            return $this->db->insert_id;
        }
        
    
    }


?>