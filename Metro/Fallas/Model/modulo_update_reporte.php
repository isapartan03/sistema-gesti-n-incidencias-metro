<?php
require_once '../../BD/BD.php';

class ModeloUpdateReporte {
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    public function obtenerReportePorFallaID($idFalla) { 
        try {
            $sql = "SELECT * FROM reporte WHERE ID_Falla = ?"; //sql de lo que querramos hacer
            $resultado = $this->conn->prepare($sql);//preparamos sql
            
            if (!$resultado) {//por si hay un error en el sql
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            $resultado->bind_param("s", $idFalla);//vinculamos el parameto ?
            
            if (!$resultado->execute()) {//por si hay un error en el bind_param
                throw new Exception("Error ejecutando consulta: " . $resultado->error);
            }
            
            return $resultado->get_result()->fetch_assoc();//retornamos en el resultado obtenido y un array asociativo
            
        } catch (Exception $e) {//por si hay un error
            error_log($e->getMessage());
            return null;
        }
    }
    
    public function actualizarReporte($idReporte, $idCoordinacion, $idJustificacion, $ubicacion, $observacion,  $diagnostico) {
        $this->conn->begin_transaction();//iniciamos transición con la base de datos
        
        try {
            $sql = "UPDATE reporte SET 
                        ID_Coordinacion = ?, 
                        ID_Justificacion = ?, 
                        Ubicacion = ?, 
                        Observaciones = ?, 
                        Diagnostico = ?
                    WHERE ID_reporte = ?";//sql de lo que querramos hacer
                    
            $resultado = $this->conn->prepare($sql);//preparamos el sql
            
            if (!$resultado) {//por si hay un error en el sql
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            $resultado->bind_param("issssi", $idCoordinacion, $idJustificacion, $ubicacion, $observacion, $diagnostico, $idReporte);//inculamos todos los parametros ?
            
            if (!$resultado->execute()) {//si hay un error en la ejecución del sql saldrá este mensaje
                throw new Exception("Error ejecutando actualización: " . $resultado->error);
            }
            
            $this->conn->commit();//Confirma todos los cambios de la transacción
            return true;
            
        } catch (Exception $e) {//si hay un error saldrá esté mensaje
            $this->conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }
}
?>