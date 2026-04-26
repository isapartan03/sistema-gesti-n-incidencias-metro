<?php
require_once '../../BD/BD.php';

class ModeloFalla {
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    //obtener equipos
    public function obtenerEquipos() {
        try {
            $datos = [];
            $sql = "SELECT ID_Equipos, Nombre FROM equipos
            WHERE active = 1 AND Status = 1"; //sql para obtener todos los equipos
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si lanza un error
            }
            
            while ($fila = $resultado->fetch_assoc()) {//array asociativo de todos los equipos
                $datos[] = $fila;//variable que toma los valores de la fila
            }
            
            return $datos;//retorna en datos
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerPrioridades() { 
        try {
            $datos = [];
            $sql = "SELECT Codigo FROM prioridad"; //sql para obtener los valores que queremos
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

    public function obtenerEstaciones() {
        try {
            $datos = [];
            $sql = "SELECT ID_Estacion, Nombre FROM estacion"; //sql para obtener todos los equipos
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si lanza un error
            }
            
            while ($fila = $resultado->fetch_assoc()) { //array asociativo de todos los equipos
                $datos[] = $fila; //variable que toma los valores de la fila
            }
            
            return $datos; //retorna en datos
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerCoordinaciones() {
        try {
            $datos = [];
            $sql = "SELECT ID_Coordinacion, Nombre FROM coordinacion"; //sql para obtener todos los equipos
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si lanza un error
            }
            
            while ($fila = $resultado->fetch_assoc()) { //array asociativo de todos los equipos
                $datos[] = $fila; //variable que toma los valores de la fila
            }
            
            return $datos; //retorna en datos
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerUsuarios() {
        try {
            $datos = [];
            $sql = "SELECT ID, Username FROM usuario"; //sql para obtener todos los equipos
            $resultado = $this->conn->query($sql); 
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si lanza un error
            }
            
            while ($fila = $resultado->fetch_assoc()) { //array asociativo de todos los equipos
                $datos[] = $fila; //variable que toma los valores de la fila
            }
            
            return $datos;//retorna en datos 
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerSupervisores() {
        try {
            $datos = [];
            $sql = "SELECT personal.carnet, persona.nombres 
                    FROM personal 
                    INNER JOIN persona ON persona.carnet = personal.carnet
                    WHERE active = 1 AND gerencia = 'OPERACIONES'"; //sql para obtener todos los supervisores
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $this->conn->error); //por si lanza un error
            }
            
            while ($fila = $resultado->fetch_assoc()) { //array asociativo de todos los equipos
                $datos[] = $fila; //variable que toma los valores de la fila
            }
            
            return $datos;//retorna en datos  
            
        } catch (Exception $e) { //por si hay un error
            error_log($e->getMessage());
            return [];
        }
    }
}
?>