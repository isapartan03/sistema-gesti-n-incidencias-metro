<?php
require_once '../../BD/BD.php';
$conn = new BD();

class ModelE 
{
    private $conx;
//---------------------------------------------------------------------------Constructor a la conexion
function __construct($conexion)
{
    $this->conx = $conexion;
}    
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Buscar las Corrdinaciones (para el formulario)
public function obtenerCoordinaciones() {
    $sql = "SELECT ID_Coordinacion, Nombre FROM coordinacion WHERE active = 1";
    $result = $this->conx->query($sql);

    if (!$result) {
        echo "Error: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------


public function obtenerEquipo($id)
{
        $sql = "SELECT e.ID_Equipos, e.Nombre, e.N_Ambiente,
        es.Nombre AS EstacionNombre,
        c.Nombre AS CoordinacionNombre,
        e.Status
    FROM equipos e
    INNER JOIN estacion es ON e.ID_Estacion = es.ID_Estacion
    INNER JOIN coordinacion c ON e.ID_Coordinacion = c.ID_Coordinacion
    WHERE e.active = 1 and e.ID_Equipos= ?; ";


    $stmt = $this->conx->prepare($sql);
    $stmt->bind_param("i",$id);
    if (!$stmt) {
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
//--------------------------------------------------------------------------- Buscar las ESTACIONES (para l formulario)
public function obtenerEstacion() {
    $sql = "SELECT ID_Estacion, Nombre FROM estacion WHERE active = 1";
    $result = $this->conx->query($sql);

    if (!$result) {
        echo "Error: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------crear un equipo
public function crearE($id, $name, $numberA, $idCoord, $idEsta )
{ 

    if ($this->repeat($id, $name, $numberA, $idCoord, $idEsta)) {
        return 'repetido';
}

    $sql = "INSERT INTO equipos (ID_Equipos, Nombre, N_Ambiente, ID_Coordinacion, ID_Estacion) VALUES (?,?, ?, ?, ?)";
    $result = $this->conx->prepare($sql);

    if (!$result) {
        //echo "Error en prepare: " . $this->conx->error;
        return 'error'; 
    }
    
    $result->bind_param("sssii", $id,  $name, $numberA, $idCoord, $idEsta);

    if (!$result->execute()) {
//      echo "Error en execute: " . $result->error;
        return 'error'; 
    }

    return 'exito';
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- VERIFICAR QUE UN EQUIPO ESTE EN LA BDD
public function repeat($id, $name, $numberA, $idCoord, $idEsta)
{
    $sql = "SELECT * FROM equipos WHERE ID_Equipos = ? AND Nombre = ? AND N_Ambiente = ? AND ID_Coordinacion = ? AND ID_Estacion = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
       // echo "Error en prepare de repeat: " . $this->conx->error;
        return 'error'; 
    }

    $stmt->bind_param("ssiii", $id ,$name, $numberA, $idCoord, $idEsta);
    $stmt->execute();
    $stmt->store_result();

    return $stmt->num_rows > 0; // Si hay filas, significa que se repite
}

//---------------------------------------------------------------------------mostrar todas los equipos
public function getAll()
{
        $sql = "SELECT e.ID_Equipos, e.Nombre, e.N_Ambiente,
        es.Nombre AS EstacionNombre,
        c.Nombre AS CoordinacionNombre,
        e.Status
    FROM equipos e
    INNER JOIN estacion es ON e.ID_Estacion = es.ID_Estacion
    INNER JOIN coordinacion c ON e.ID_Coordinacion = c.ID_Coordinacion
    WHERE e.active = 1";


    $stmt = $this->conx->prepare($sql);
    if (!$stmt) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- EDITAR EQUIPO
public function edit($id, $name, $name_a, $idCoord, $idEsta)
{
        $sql = "UPDATE equipos
                SET Nombre = ?,
                    N_Ambiente = ?,
                    ID_Coordinacion = ?,
                    ID_Estacion = ?
                 WHERE ID_Equipos = ?;
    ";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        //echo "Error preparando la sentencia editar.";
        return 'error'; 
    }

    $stmt->bind_param("ssiii",$name,$name_a, $idCoord, $idEsta,$id); 

    if (!$stmt->execute()) {
       // echo "Error ejecutando: " . $stmt->error;
        return 'error'; 
    }
    return 'exito';
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- ELIMINAR UN EQUIPO  (MARCAR COMO INACTIVO)
public function delete($id)
{
    $sql = "UPDATE equipos SET active = 0 WHERE ID_Equipos = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
    echo "error preparando la sentencia de eliminar";
        return 'error'; 
    }

    $stmt->bind_param("i", $id);
     
    if (!$stmt->execute()) {
        echo "no s eejecuto nada ";
    echo "Error ejecutando: " .$stmt->error;
        return 'error'; 
    }
    return 'exito';
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- BUSCAR EQUIPO
// 
public function buscarEquipo($name = null, $numberA = null, $idCoord = null, $idEsta = null, $estatus=null)
{
    $sql = "SELECT 
                e.ID_Equipos, 
                e.Nombre, 
                e.N_Ambiente, 
                es.Nombre AS EstacionNombre, 
                c.Nombre AS CoordinacionNombre, 
                e.Status
            FROM equipos e
            INNER JOIN estacion es ON e.ID_Estacion = es.ID_Estacion
            INNER JOIN coordinacion c ON e.ID_Coordinacion = c.ID_Coordinacion
            WHERE e.active = 1";

    $params = [];
    $types = "";

    if ($name !== null && $name !== "") {
        $sql .= " AND e.Nombre LIKE ?";
        $params[] = "%" . $name . "%";
        $types .= "s";
    }

    if ($numberA !== null && $numberA !== "") {
        $sql .= " AND e.N_Ambiente LIKE ?";
        $params[] = "%" . $numberA . "%";
        $types .= "s";
    }

    if ($idCoord !== null && $idCoord !== "") {
        $sql .= " AND e.ID_Coordinacion = ?";
        $params[] = $idCoord;
        $types .= "i";
    }

    if ($idEsta !== null && $idEsta !== "") {
        $sql .= " AND e.ID_Estacion = ?";
        $params[] = $idEsta;
        $types .= "i";
    }

    if ($estatus!==null && $estatus!=="") {

        $sql .= " AND e.Status = ?";
        $params[] = $estatus;
        $types .= "i";
    }

    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        echo "Error en prepare: " . $this->conx->error;
        return false;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------

public function cambiarEstado($id, $nuevoEstado) {
    $stmt = $this->conx->prepare("UPDATE equipos SET Status = ? WHERE ID_Equipos = ?");
    
    // Usar bind_param en lugar de pasar array a execute()
    $stmt->bind_param("is", $nuevoEstado, $id); // "i" para entero, "s" para string
    
    return $stmt->execute();
}


}
?>