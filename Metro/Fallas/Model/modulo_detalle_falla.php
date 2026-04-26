<?php
require_once '../../BD/BD.php';

class DetalleFalla{
    private $conn;

    public function __construct() {
        $conexion = new BD();
        $this->conn = $conexion->getConex();
    }

    //está función sirve para mostrar todos los valores de falla/reporte
    public function ObtenerFallaPorID($idfalla){
        $sql="SELECT f.ID_Falla, per.nombres, u.Username, uc.Username AS UsuarioCierre, f.descripcion, f.Falla_Status, e.Nombre AS NombreEquipo, r.ID_reporte, r.Ubicacion, r.Diagnostico, r.Observaciones, c.Nombre AS Coordinacion, j.descripcion AS Justificacion, DATE_FORMAT(r.F_apertura, '%Y-%m-%d %H:%i') AS F_apertura, DATE_FORMAT(r.F_cierre, '%Y-%m-%d %H:%i') AS F_cierre, pr.Codigo AS PrioridadCodigo
        FROM falla f
        JOIN personal p ON p.carnet = f.ID_Personal
        JOIN persona per ON per.carnet = p.carnet
        JOIN usuario u ON u.ID = f.ID_Usuario
        JOIN equipos e ON e.ID_Equipos = f.ID_Equipos
        LEFT JOIN usuario uc ON uc.ID = f.ID_Usuario_cierre
        LEFT JOIN reporte r ON r.ID_Falla = f.ID_Falla
        LEFT JOIN coordinacion c ON c.ID_Coordinacion = r.ID_Coordinacion
        LEFT JOIN justificacion j ON j.ID = r.ID_Justificacion
        LEFT JOIN prioridad pr ON pr.Codigo = f.ID_Prioridad
        WHERE f.ID_Falla = ?";//setencia sql para lo que necesitamos
        $resultado=$this->conn->prepare($sql);//preparamos sentencia
        $resultado->bind_param('s',$idfalla);//vinculamos los parametros
        $resultado->execute();//ejecutamos sentencia
        return $resultado->get_result()->fetch_assoc();//obtenemos infromación y array asociativo
    }

    //con está función obtenemos el último tecnico quien diagnostico x falla
    public function ObtenerTecnicoFalla($idfalla){
        $sql ="SELECT tf.carnet_tecnico, p.nombres, p.apellidos
            FROM tecnicos_fallas tf
            JOIN persona p ON tf.carnet_tecnico = p.carnet
            WHERE tf.id_falla = ?
            ORDER BY tf.fecha_asignacion DESC
            LIMIT 1";//setencia sql para lo que necesitamos

            $resultado=$this->conn->prepare($sql);//preparamos sentencia
            $resultado->bind_param('s',$idfalla);//vinculamos los parametros
            $resultado->execute();//ejecutamos sentencia
            return $resultado->get_result()->fetch_assoc();//obtenemos infromación y array asociativo
    }

    //con está función obtenemos todo los tecnicos que diagnosticaron x falla
    public function obtenerHistorialTecnicos($idFalla) {
    $sql = "SELECT DATE_FORMAT(tf.fecha_asignacion, '%Y-%m-%d %H:%i') AS Fecha, p.nombres, p.apellidos
            FROM tecnicos_fallas tf
            JOIN persona p ON tf.carnet_tecnico = p.carnet
            WHERE tf.id_falla = ?
            ORDER BY tf.fecha_asignacion DESC";//setencia sql para lo que necesitamos
    $stmt = $this->conn->prepare($sql);//preparamos sentencia
    $stmt->bind_param("s", $idFalla);//vinculamos los parametros
    $stmt->execute();//ejecutamos sentencia
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);//obtenemos infromación y array de toda la información
    }
}

?>