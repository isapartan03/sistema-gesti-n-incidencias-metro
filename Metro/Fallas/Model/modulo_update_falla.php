<?php
require_once '../../BD/BD.php';

class ModeloUpdateFalla {
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    public function obtenerFallaPorID($id) {
    try {
        // Consulta modificada con JOIN
        $sql = "SELECT f.*, e.Nombre AS NombreEquipo 
                FROM falla f 
                LEFT JOIN equipos e ON e.ID_Equipos = f.ID_Equipos
                WHERE f.ID_Falla = ?";
                
        $resultado = $this->conn->prepare($sql);
        
        if (!$resultado) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        
        $resultado->bind_param("s", $id);
        
        if (!$resultado->execute()) {
            throw new Exception("Error ejecutando consulta: " . $resultado->error);
        }
        
        return $resultado->get_result()->fetch_assoc();
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}
}
?>