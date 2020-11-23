<?php
    use Firebase\JWT\JWT;
    require('../dto/usuarioDTO.php');
    require('../conexion.php');
    require('../auth.php');

    class UsuarioBL {
        private $conn;
        private $usuarioDTO;
        
        public function __construct() {
            $this->conn = new Conexion();
            $this->usuarioDTO = new UsuarioDTO();
        }

        public function login($usuarioDTO) {
            $this -> conn -> OpenConnection();
            $connsql = $this->conn ->getConnection();
            $sqlQuery = "SELECT * FROM usuario WHERE username = :usuario AND password = :contrasena";

            try{
                if($connsql) {

                    $connsql->beginTransaction();
                    $sqlStatment = $connsql->prepare($sqlQuery);
                    
                    $sqlStatment->bindParam(':usuario', $usuarioDTO->usuario);
                    $sqlStatment->bindParam(':contrasena', $usuarioDTO->contrasena);
                    $sqlStatment->execute();
        
                    $response = $sqlStatment->fetch(PDO::FETCH_OBJ);
                    $connsql->commit();
                    
                    $this->usuarioDTO->usuario = $usuarioDTO->usuario;
                    $this->usuarioDTO->contrasena = $usuarioDTO->contrasena;
                    $this->usuarioDTO->token = '';

                    if($response) {
                        $this->usuarioDTO->token = Auth::SignIn([
                            'username' => $usuarioDTO->usuario,
                            'password' => $usuarioDTO->contrasena
                        ]);
                    }
                    
                    return $this->usuarioDTO;
                }
            } catch(PDOException $e) {
                $connsql -> rollBack();
            }
        }
    }
?>