<?php
require_once '../../BD/BD.php';
//modulo que sirve para insertar valores a la tabla tecnicos_fallas
class ModeloTecnicoFalla{
    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    public function insertarDiagnosticoTecnico($carnetTecnico, $idFalla, $diagnostico, $observacion) {
    $sql = "INSERT INTO tecnicos_fallas (carnet_tecnico, id_falla, diagnostico, observaciones, fecha_asignacion)
            VALUES (?, ?, ?, ?, NOW())";//sentencia sql para insertar los valores
    $resultado = $this->conn->prepare($sql);//preparamos sentencia
    
    if (!$resultado) throw new Exception("Error preparando consulta: " . $this->conn->error);//por si hay un error
    
    $resultado->bind_param("ssss", $carnetTecnico, $idFalla, $diagnostico, $observacion);//vinculamos los parametros 
    
    if (!$resultado->execute()) {
        throw new Exception("Error ejecutando inserción: " . $resultado->error);//por si hay un error de inserción
    }

    return true;//retornamos verdadero si todo sale bien
}
}
?>