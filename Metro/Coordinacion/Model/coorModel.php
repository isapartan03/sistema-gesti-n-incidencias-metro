<?php
//require_once '../../Metro-ModulosV1.7/BD/BD.php'; ///bd johny
require_once '../../BD/BD.php';
$conn = new BD();

class CoordModel
{
	private $conx;
//---------------------------------------------------------------------------Constructor a la conexion
	function __construct($conexion)
	{
		$this->conx = $conexion;
	}
//---------------------------------------------------------------------------
public function obtenerCoordinadores()
{
    $sql = "SELECT  p.carnet, pe.nombres
        FROM personal p
        INNER JOIN persona pe ON p.carnet = pe.carnet
        WHERE p.active = 1";
    $result = $this->conx->query($sql);

    if (!$result) {
        echo "Error: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------crear una coordinacion

public function obtenerCoord($id){
    
   $sql="SELECT * FROM coordinacion WHERE ID_Coordinacion = ?;";
   $stmt=$this->conx->prepare($sql);
   $stmt->bind_param("i",$id);
   $stmt->execute();
  return  $result = $stmt->get_result()->fetch_assoc();
    
}


public function crearCoord($nombre, $correo, $carnet)
{
    if ($this->repeat($nombre)) {
        return 'repetido';
    }

    $sql = "INSERT INTO coordinacion (Nombre, correo, carnet_personal) VALUES (?, ?, ?)";
    $result = $this->conx->prepare($sql);

    if (!$result) {
        return 'error';
    }

    $result->bind_param("ssi", $nombre, $correo, $carnet);

    if (!$result->execute()) {
        return 'error';
    }

    return 'exito';
}

//---------------------------------------------------------------------------
//---------------------------------------------------------------------------verificar que una coordinacion no este repetida
public function repeat($nombre)
{
	$nombre = strtoupper($nombre);

	$sql = "SELECT COUNT(*) AS total FROM coordinacion WHERE Nombre = ?";
	$result = $this->conx->prepare($sql);

	if (!$result) {
//		echo "Error a la hora de sentencia vereficar: " . $this->conx->error;
        return 'error';
	}

	$result->bind_param("s", $nombre);

	if (!$result->execute()) {
//		echo "Error en el execute" . $result->error;
        return 'error';
	} else {
	
	$resultado = $result->get_result();	
	$fila = $resultado->fetch_assoc();
	
	return $fila['total'] > 0;
	} 
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Editar coordinacion
public function editar($id, $newname, $newcorreo, $newcarnet)
{
    $sql = "UPDATE coordinacion SET Nombre = ?, correo = ?, carnet_personal = ? WHERE ID_Coordinacion = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        return 'error';
    }

    $stmt->bind_param("ssii", $newname, $newcorreo, $newcarnet, $id); // nombre, correo, carnet, id

    if (!$stmt->execute()) {
        return 'error';
    }

    return 'exito';
}

//--------------------------------------------------------------------------- Eliminar coordinación (marcar como inactiva)
//--------------------------------------------------------------------------- DESACTIVAR COORDINACIÓN Y SUS EQUIPOS
public function delete($id)
{
    $this->conx->begin_transaction();

    try {
        // Desactivar la coordinación
        $sql1 = "UPDATE coordinacion SET active = 0 WHERE ID_Coordinacion = ?";
        $stmt1 = $this->conx->prepare($sql1);
        if (!$stmt1) throw new Exception("Error preparando desactivación de coordinación");

        $stmt1->bind_param("i", $id);
        if (!$stmt1->execute()) throw new Exception("Error ejecutando desactivación de coordinación");

        // Desactivar todos los equipos relacionados
        $sql2 = "UPDATE equipos SET active = 0 WHERE ID_Coordinacion = ?";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) throw new Exception("Error preparando desactivación de equipos");

        $stmt2->bind_param("i", $id);
        if (!$stmt2->execute()) throw new Exception("Error ejecutando desactivación de equipos");

        $this->conx->commit();
        return true;

    } catch (Exception $e) {
        $this->conx->rollback();
        return false;
    }
}

//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- CONTAR EQUIPOS POR ESTACION

public function contarEquiposPorCoordinacion($idEstacion)
{
    $sql = "SELECT COUNT(*) AS total FROM equipos WHERE ID_Coordinacion = ? AND active = 1";
    $stmt = $this->conx->prepare($sql);
    if (!$stmt) {
//        echo "Error al preparar la consulta de conteo.";
        return 0;
    }

    $stmt->bind_param("i", $idEstacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    return $data['total'];
}
//---------------------------------------------------------------------------

//--------------------------------------------------------------------------- Verificar si existe ID
public function existeID($id)
{
    $sql = "SELECT COUNT(*) AS total FROM coordinacion WHERE ID_Coordinacion = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        //echo "Error en el prepare (verificar existencia): " . $this->conx->error;
        return 'error';
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    return $fila['total'] > 0;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Mostrar coordinaciones activas
public function getAll()
{
    $sql = "SELECT c.ID_Coordinacion, c.Nombre, c.correo , pe.nombres
FROM coordinacion c 
INNER JOIN personal p ON p.carnet = c.carnet_personal
INNER JOIN persona pe ON p.carnet = pe.carnet
WHERE c.active = 1";

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
//---------------------------------------------------------------------------Buscar coordinacin por id o por nombre
public function buscarCoord($id = null, $nombre = null) 
{
    $sql = "SELECT c.ID_Coordinacion, c.Nombre, c.correo, pe.nombres
            FROM coordinacion c
            INNER JOIN personal p ON p.carnet = c.carnet_personal
            INNER JOIN persona pe ON p.carnet = pe.carnet
            WHERE c.active = 1";
    
    $params = [];
    $types = "";

    if ($id !== null) {
        $sql .= " AND c.ID_Coordinacion = ?";
        $params[] = $id;
        $types .= "i";
    }

    if ($nombre !== null) {
        $sql .= " AND c.Nombre LIKE ?";
        $params[] = '%' . $nombre . '%';
        $types .= "s";
    }

    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        return 'error';
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

//---------------------------------------------------------------------------
}
?>