<?php
require_once '../../BD/BD.php';

class ModeloReporte {
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    public function obtenerJustificaciones() { 
        try {
            $datos = [];
            $sql = "SELECT ID, descripcion FROM justificacion"; //sql para obtener los valores que queremos
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si hay un error
            }
            
            while ($row = $resultado->fetch_assoc()) { //array asociativo de que obtenemos del sql
                $datos[] = $row; //igualamos los valores de la fila a la variable
            }
            
            return $datos; //retornamos el variable
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerTecnicos() {
        try {
            $datos = [];
            $sql = "SELECT t.carnet, p.nombres, p.apellidos FROM tec t
                    JOIN persona p ON t.carnet = p.carnet
                    WHERE active = 1";//sql para obtener los valores que queremos
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si hay un error
            }
            
            while ($row = $resultado->fetch_assoc()) { //array asociativo de que obtenemos del sql
                $datos[] = $row; //igualamos los valores de la fila a la variable
            }
            
            return $datos; //retornamos el variable
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

     public function obtenerFallaPorID($idFalla) { 
        try {
            // Obtener el ID del equipo asociado a la falla
            $sql = "SELECT ID_Equipos FROM falla WHERE ID_Falla = ?";
            $resultado = $this->conn->prepare($sql); 
            
            if (!$resultado) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            $resultado->bind_param("s", $idFalla);
            
            if (!$resultado->execute()) {
                throw new Exception("Error ejecutando consulta: " . $resultado->error);
            }
            
            $result = $resultado->get_result();
            $info = $result->fetch_assoc();
            $resultado->close();
            
            return $info;
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function obtenerDatosEquipo($idEquipo) {
        try {
            // Obtener datos del equipo incluyendo coordinación
            $sql = "SELECT e.N_Ambiente, est.Nombre AS Nombre_Estacion, c.ID_Coordinacion, c.Nombre AS Nombre_Coordinacion
                    FROM equipos e
                    JOIN Coordinacion c ON e.ID_Coordinacion = c.ID_Coordinacion
                    JOIN estacion est ON e.ID_Estacion = est.ID_Estacion
                    WHERE ID_Equipos = ?";
                    
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $idEquipo);
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }
            
            $data = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $data;
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

}
?>