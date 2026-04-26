<?php
require_once '../../BD/BD.php';
$conn = new BD(); 

class CoorModel
{
    
private $conx;
//---------------------------------------------------------------------------Constructor a la conexion
function __construct($conexion)
{
	$this->conx = $conexion;
}    
//---------------------------------------------------------------------------


public function obtenerCoordinador($id){
    
   $sql="SELECT p.carnet, p.nombres, p.apellidos, c.Nombre_Grado, t.gerencia
            FROM persona p
            JOIN personal t ON p.carnet = t.carnet
            JOIN codigo_grado c ON t.codGrado = c.ID_Grado
            WHERE t.active = 1  AND p.carnet = ?;";
   $stmt=$this->conx->prepare($sql);
   $stmt->bind_param("i",$id);
   $stmt->execute();
  return  $result = $stmt->get_result()->fetch_assoc();
    
}

public function obtenerCodgrado() {
    $sql = "SELECT ID_Grado, Nombre_Grado FROM codigo_grado WHERE 1";
    $result = $this->conx->query($sql);

    if (!$result) {
        echo "Error: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//------------------------------------------------------------------------ Crear un nuevo Coordinador
public function crearCoord($name, $lastN, $carnet, $codG, $gerencia)
{
// VERIFICA QUE EL CARNET NO ESTE REPETIDO
    if ($this->repeat($carnet)) {
		return 'repetido';
    }

    $this->conx->begin_transaction(); // Inicia la transacción

    try {
        // Insertar en Persona
        $sql1 = "INSERT INTO persona (nombres, apellidos, carnet) VALUES (?, ?, ?)";
        $stmt1 = $this->conx->prepare($sql1);
        if (!$stmt1) {
//            echo "Error preparando la inserción en Persona";
            return 'error';
        }

        $stmt1->bind_param("ssi", $name, $lastN, $carnet);
        if (!$stmt1->execute()) {
//        echo "Error ejecutando inserción en Persona";
            return 'error';
        }

        // Insertar en personal
        $sql2 = "INSERT INTO personal (carnet, codGrado, gerencia) VALUES (?, ?, ?)";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) {
//            echo "Error preparando la inserción en personal";
            return 'error';
        }

        $stmt2->bind_param("iss", $carnet, $codG, $gerencia);
        if (!$stmt2->execute()) {
           // echo "Error ejecutando inserción en Coordinador";
            return 'error';
        }

        $this->conx->commit(); // Confirma los cambios
    	return 'exito';

    } catch (Exception $e) {
        $this->conx->rollback(); // Revertir cambios si hay un error
//        echo "Transacción fallida: " . $e->getMessage();
        return 'error';
    }
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Verificar carnet #####
public function repeat($carnet)
{
	$carnet = strtoupper($carnet);

	$sql = "SELECT COUNT(*) AS total FROM persona WHERE carnet = ?";
	$result = $this->conx->prepare($sql);

	if (!$result) {
//		echo "Error a la hora de sentencia vereficar: " . $this->conx->error;
		return 'error';	
	}

	$result->bind_param("i", $carnet);

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
//---------------------------------------------------------------------------Mostrar los Coordinadores
public function getAll() {
    $sql = "SELECT p.carnet, p.nombres, p.apellidos, c.Nombre_Grado, t.gerencia
            FROM persona p
            JOIN personal t ON p.carnet = t.carnet
            JOIN codigo_grado c ON t.codGrado = c.ID_Grado
			WHERE t.active = 1;";
    $result = $this->conx->query($sql);
    if (!$result) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Editar Coordinadores
public function editar($carnet, $nombre, $apellido, $codG, $gerencia) {
    $this->conx->begin_transaction();

    try {
        // Actualizar tabla persona
        $sql1 = "UPDATE persona SET nombres = ?, apellidos = ? WHERE carnet = ?";
        $stmt1 = $this->conx->prepare($sql1);
        if (!$stmt1) throw new Exception("Error en UPDATE persona");

        $stmt1->bind_param("sss", $nombre, $apellido, $carnet);
        if (!$stmt1->execute()) throw new Exception("Error ejecutando UPDATE persona");

        // Actualizar tabla tec
        $sql2 = "UPDATE personal SET codGrado = ?, gerencia = ?  WHERE carnet = ?";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) throw new Exception("Error en UPDATE tec");

        $stmt2->bind_param("isi", $codG,$gerencia, $carnet);
        if (!$stmt2->execute()) throw new Exception("Error ejecutando UPDATE Coordinadores");

        $this->conx->commit();
        return true;

    } catch (Exception $e) {
        $this->conx->rollback();
        echo "Error en edición: " . $e->getMessage();
		return 'error';	
    }
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Eliminar Coordinador
public function delete($carnet)
{
    $sql = "UPDATE personal SET active = 0 WHERE carnet = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
    echo "error preparando la sentencia de eliminar";
    return 'error';	
    }

    $stmt->bind_param("i", $carnet);
     
    if (!$stmt->execute()) {
    echo "Error ejecutando: " .$stmt->error;
		return 'error';	
    }

	return 'exito';
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Buscar Coordinador
public function buscarCoor($carnet = null, $nombre = null, $gerencia = null) 
{
    //Si recibe los campos vacios muestra todo    
$sql = "SELECT 
            p.carnet, 
            p.nombres, 
            p.apellidos, 
            c.Nombre_Grado, 
            t.gerencia,
            t.active
        FROM persona p
        JOIN personal t ON p.carnet = t.carnet
        JOIN codigo_grado c ON t.codGrado = c.ID_Grado
        WHERE 1=1";

    
    $params = [];
    $types = "";

    if ($carnet !== null) {
        $sql .= " AND p.carnet = ?";
        $params[] = $carnet;
        $types .= "i";
    }

    if ($nombre !== null) {
        $sql .= " AND p.nombres LIKE  ?";
        $params[] = '%' . $nombre . '%';
        $types .= "s";
    }

    if ($gerencia !== null) {
        $sql .= " AND t.gerencia LIKE ?";
        $params[] = '%' . $gerencia . '%';
        $types .= "s";
    }

    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
        echo "Error en prepare: " . $this->conx->error;
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