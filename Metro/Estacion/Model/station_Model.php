<?php
require_once '../../BD/BD.php';
$conn = new BD();

class Station_Model 
{
private $conx;
//Constructor a la conexion
    function __construct($conexion)
    {
        $this->conx = $conexion;
    }   
//---------------------------------------------------------------------------crear una Estacion
public function crearEstacion($nombre)
{ 

	if ($this->repeat($nombre)) {
		return 'repetido';
}

	$sql = "INSERT INTO estacion (Nombre) VALUES (?)";
	$result = $this->conx->prepare($sql);

	if (!$result) {
	//	echo "Error en prepare: " . $this->conx->error;
		return 'error';	
	}
	
	$result->bind_param("s", $nombre);

	if (!$result->execute()) {
//		echo "Error en execute: " . $result->error;
		return 'error';	
	}

	return 'exito';
}


public function obtenerEstacion($id)
{
    $sql = "SELECT ID_Estacion, Nombre FROM estacion WHERE active = 1 and ID_Estacion = ?; ";
    $stmt = $this->conx->prepare($sql);
    $stmt->bind_param("i",$id);
    if (!$stmt) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

//---------------------------------------------------------------------------verificar que una estacion no este repetida
public function repeat($nombre)
{
	$nombre = strtoupper($nombre);

	$sql = "SELECT COUNT(*) AS total FROM estacion WHERE Nombre = ?";
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

//---------------------------------------------------------------------------mostrar todas las estaciones
public function getAll()
{
    $sql = "SELECT ID_Estacion, Nombre FROM estacion WHERE active = 1";
    $stmt = $this->conx->prepare($sql);
    if (!$stmt) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
//Editar estacion
public function editar($id, $newname)
{
    $sql = "UPDATE estacion SET Nombre = ? WHERE ID_Estacion = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
  //      echo "Error preparando la sentencia editar.";
    		return 'error';	
    }

    $stmt->bind_param("si", $newname, $id); // string, int

    if (!$stmt->execute()) {
//        echo "Error ejecutando: " . $stmt->error;
  		return 'error';	
    }

    return 'exito';
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- eliminar estacion
public function delete($id)
{
    // Iniciar transacción por seguridad
    $this->conx->begin_transaction();

    try {
        // 1. Desactivar la estación
        $sql1 = "UPDATE estacion SET active = 0 WHERE ID_Estacion = ?";
        $stmt1 = $this->conx->prepare($sql1);
        if (!$stmt1) throw new Exception("Error preparando la eliminación de estación");

        $stmt1->bind_param("i", $id);
        if (!$stmt1->execute()) throw new Exception("Error ejecutando la eliminación de estación");

        // 2. Desactivar todos los equipos asociados
        $sql2 = "UPDATE equipos SET active = 0 WHERE ID_Estacion = ?";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) throw new Exception("Error preparando la desactivación de equipos");

        $stmt2->bind_param("i", $id);
        if (!$stmt2->execute()) throw new Exception("Error ejecutando la desactivación de equipos");

        // Confirmar la transacción
        $this->conx->commit();
        return true;

    } catch (Exception $e) {
        // Revertir si hay error
        $this->conx->rollback();
//        echo "Error: " . $e->getMessage();
		return 'error';	
    }
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- CONTAR EQUIPOS POR ESTACION

public function contarEquiposPorEstacion($idEstacion)
{
    $sql = "SELECT COUNT(*) AS total FROM equipos WHERE ID_Estacion = ? AND active = 1";
    $stmt = $this->conx->prepare($sql);
    if (!$stmt) {
  //      echo "Error al preparar la consulta de conteo.";
    		return 'error';	
    }

    $stmt->bind_param("i", $idEstacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    return $data['total'];
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Buscar coordinacin por id o por nombre
public function buscarStation($id = null, $nombre = null) 
{

    //Si recibe los campos vacios muestra todo
    $sql = "SELECT * FROM estacion WHERE 1=1 AND active = 1";
    $params = [];
    $types = "";

    if ($id !== null) {
        $sql .= " AND ID_Estacion = ?";
        $params[] = $id;
        $types .= "i";
    }

    if ($nombre !== null) {
        $sql .= " AND Nombre LIKE ?";
        $params[] = '%' .  $nombre . '%';
        $types .= "s";
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
}
?>