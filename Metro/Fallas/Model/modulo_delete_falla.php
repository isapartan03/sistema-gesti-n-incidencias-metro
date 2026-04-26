<?php
require_once '../../BD/BD.php';

class ModeloDeleteFalla {
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

        //sql que editará el status de falla cuando querramos finalizar la falla
    private function cambiarStatus($id) {
        $sql = "UPDATE falla SET Falla_status = 0 WHERE ID_Falla = ?";
        $resultado = $this->conn->prepare($sql);
        $resultado->bind_param("s", $id);
        return $resultado->execute();
    }

    //sql para actualizar la fecha de cierre de la falla
    private function actualizarFecha_Cierre($id){
        $sql = "UPDATE reporte SET F_cierre = NOW() WHERE ID_Falla = ?";
        $resultado = $this->conn->prepare($sql);
        $resultado->bind_param("s", $id);
        return $resultado->execute();
    }

    private function obtenerIdUsuarioCierre($id){
       session_start();
       $idUsuario = $_SESSION['id'] ?? null;

       if (!$idUsuario){
        error_log("No se pudo obtener ID de usuario de la sesión");
        return null;
       }

       $sql ="UPDATE falla SET ID_Usuario_cierre = ? WHERE ID_Falla = ?";
       $resultado = $this->conn->prepare($sql);

       if (!$resultado) {
        error_log("Error al preparar consulta: " . $this->conn->error);
        return null;
        }
        $resultado->bind_param("is", $idUsuario, $id);

        if (!$resultado->execute()) {
        error_log("Error al registrar usuario cierre: " . $resultado->error);
        $resultado->close();
        return null;
        }
    
        $resultado->close();
        return $idUsuario;
    }

    public function obtenerDatosParaCorreo($idFalla){
    $sql = <<<SQL
SELECT
  f.ID_Falla,
  e.Nombre         AS NombreEquipo,
  e.N_Ambiente     AS N_Ambiente,
  est.Nombre       AS NombreEstacion,
  c.correo         AS CorreoCoordinacion,
  per2.nombres     AS TecnicoCierre,
  c.Nombre
FROM falla f
JOIN equipos e   ON e.ID_Equipos = f.ID_Equipos
JOIN estacion est ON est.ID_Estacion = e.ID_Estacion
JOIN coordinacion c ON c.ID_Coordinacion = e.ID_Coordinacion

LEFT JOIN (
    SELECT tf.id_falla, tf.carnet_tecnico
    FROM tecnicos_fallas tf
    ORDER BY tf.fecha_asignacion DESC
) AS tfrec ON tfrec.id_falla = f.ID_Falla

LEFT JOIN persona per2 ON per2.carnet = tfrec.carnet_tecnico

WHERE f.ID_Falla = ?
LIMIT 1
SQL;

    // 1) Intentamos preparar y capturamos el error si falla
    if (!($stmt = $this->conn->prepare($sql))) {
        throw new Exception("Error en prepare de obtenerDatosParaCorreo: " 
                          . $this->conn->error);
    }

    // 2) Vinculamos y ejecutamos
    $stmt->bind_param('s', $idFalla);
    $stmt->execute();

    // 3) Recuperamos el resultado
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

    //try-catch en php

    public function finalizarFalla($id) {
        $this->conn->begin_transaction(); //Inicia una transacción de base de datos. Todo lo que se haga desde aquí hasta commit() se aplica solo si no hubo errores
    
        try {
            if (!$this->cambiarStatus($id)) {

                throw new Exception("Error al desactivar falla"); //se lanza una excepción (exception) al ocurrir un error y salta al catch más cercano
                //throw: "lanzar una excepción".
                //new Exception: crea un objeto de tipo Exception con un mensaje personalizado que explica el problema.
            }
    
            if (!$this->actualizarFecha_Cierre($id)) {
                
                throw new Exception("Error al actualizar fecha de cierre"); //se lanza una excepción (exception) al ocurrir un error y salta al catch más cercano
                //throw: "lanzar una excepción".
                //new Exception: crea un objeto de tipo Exception con un mensaje personalizado que explica el problema.
            }

            if (!$this->obtenerIdUsuarioCierre($id)) {
                
                throw new Exception("Error al seleccionar el id del usuario"); //se lanza una excepción (exception) al ocurrir un error y salta al catch más cercano
                //throw: "lanzar una excepción".
                //new Exception: crea un objeto de tipo Exception con un mensaje personalizado que explica el problema.
            }
    
            $this->conn->commit(); //Confirma todos los cambios de la transacción. Aquí ya es seguro guardar porque ambas operaciones se completaron sin errores.
            return true; //resultado exitoso
            
        } catch (Exception $e) { //Si ocurre algún error (ya sea al desactivar o al actualizar la fecha):

            $this->conn->rollback(); //Revierte cualquier cambio hecho hasta ahora (es el rollback())
            error_log($e->getMessage()); //egistra el error en el log del servidor (error_log(...)).
            return false;// resultado fallido
        }
    }
}
?>
