<?php
require_once '../../BD/BD.php';
$conn = new BD();

class tecModel
{
    
private $conx;
//---------------------------------------------------------------------------Constructor a la conexion
function __construct($conexion)
{
    $this->conx = $conexion;
}    
//------------------------------------------------------------------------
//---------------------------------------------------------------------------
public function obtenerCodgrado() {
    $sql = "SELECT ID_Grado, Nombre_Grado FROM codigo_grado WHERE 1";
    $result = $this->conx->query($sql);

    if (!$result) {
        echo "Error: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}



public function obtenerTecnico($id) {
    $sql = "SELECT p.carnet, p.nombres, p.apellidos, c.Nombre, co.Nombre_Grado, t.id_coord
            FROM persona p
            JOIN tec t ON p.carnet = t.carnet
            JOIN coordinacion c ON t.id_coord = c.ID_Coordinacion
            JOIN codigo_grado co ON t.codGrado=co.ID_Grado
            WHERE t.active = 1 and t.carnet=?;";
    $stmt = $this->conx->prepare($sql);
    $stmt->bind_param("i",$id);

    if (!$stmt) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc(); 
}

//------------------------------------------------------------------------ Crear un nuevo tecnico
public function crearTec($name, $lastN, $carnet, $codG, $idCoord)
{
// VERIFICA QUE EL CARNET NO ESTE REPETIDO
    if ($this->repeat($carnet)) {
//        echo "EL carnet '$carnet' ya existe";
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
        return 'error'; 
            // echo "Error ejecutando inserción en Persona";
        }

        // Insertar en tec
        $sql2 = "INSERT INTO tec (carnet, codGrado, id_coord) VALUES (?, ?, ?)";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) {
//        echo "Error preparando la inserción en Tec";
        return 'error'; 
        }

        $stmt2->bind_param("isi", $carnet, $codG, $idCoord);
        if (!$stmt2->execute()) {
//            echo "Error ejecutando inserción en Tec";
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
//--------------------------------------------------------------------------- Buscar las oordinaciones
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
//--------------------------------------------------------------------------- Verificar carnet
public function repeat($carnet)
{
    $carnet = strtoupper($carnet);

    $sql = "SELECT COUNT(*) AS total FROM persona WHERE carnet = ?";
    $result = $this->conx->prepare($sql);

    if (!$result) {
//      echo "Error a la hora de sentencia vereficar: " . $this->conx->error;
        return 'error'; 
    }

    $result->bind_param("i", $carnet);

    if (!$result->execute()) {
//      echo "Error en el execute" . $result->error;
        return 'error'; 
    } else {
    
    $resultado = $result->get_result(); 
    $fila = $resultado->fetch_assoc();
    
    return $fila['total'] > 0;
    } 
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Mostrar los tecnicos
public function getAll() {
    $sql = "SELECT p.carnet, p.nombres, p.apellidos, c.Nombre, co.Nombre_Grado
            FROM persona p
            JOIN tec t ON p.carnet = t.carnet
            JOIN coordinacion c ON t.id_coord = c.ID_Coordinacion
            JOIN codigo_grado co ON t.codGrado=co.ID_Grado
            WHERE t.active = 1;";
    $result = $this->conx->query($sql);
    if (!$result) {
        echo "Error preparando: " . $this->conx->error;
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Editar Tecnico
public function editar($carnet, $nombre, $apellido, $codG, $idCoord) {
    $this->conx->begin_transaction();

    try {
        // Actualizar tabla persona
        $sql1 = "UPDATE persona SET nombres = ?, apellidos = ? WHERE carnet = ?";
        $stmt1 = $this->conx->prepare($sql1);
        if (!$stmt1) throw new Exception("Error en UPDATE persona");

        $stmt1->bind_param("sss", $nombre, $apellido, $carnet);
        if (!$stmt1->execute()) throw new Exception("Error ejecutando UPDATE persona");

        // Actualizar tabla tec
        $sql2 = "UPDATE tec SET codGrado = ?, id_coord=? WHERE carnet = ?";
        $stmt2 = $this->conx->prepare($sql2);
        if (!$stmt2) throw new Exception("Error en UPDATE tec");

        $stmt2->bind_param("sss", $codG, $idCoord, $carnet);
        if (!$stmt2->execute()) throw new Exception("Error ejecutando UPDATE tec");

        $this->conx->commit();
        return true;

    } catch (Exception $e) {
        $this->conx->rollback();
//        echo "Error en edición: " . $e->getMessage();
        return 'error'; 
    }
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Eliminar Tecnico
public function delete($carnet)
{
    $sql = "UPDATE tec SET active = 0 WHERE carnet = ?";
    $stmt = $this->conx->prepare($sql);

    if (!$stmt) {
//    echo "error preparando la sentencia de eliminar";
    return 'error'; 
    }

    $stmt->bind_param("i", $carnet);
     
    if (!$stmt->execute()) {
//    echo "Error ejecutando: " .$stmt->error;
    return 'error'; 
    }

    return true; 
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Buscar Tecnicos
public function buscarTec($carnet = null, $nombre = null, $apellido = null) 
{
    $sql = "SELECT 
                p.carnet, 
                p.nombres, 
                p.apellidos, 
                t.codGrado, 
                t.active,
                co.Nombre_Grado,
                c.Nombre
            FROM persona p
            JOIN tec t ON p.carnet = t.carnet
            JOIN coordinacion c ON t.id_coord = c.ID_Coordinacion
            JOIN codigo_grado co ON co.ID_Grado = t.codGrado
            WHERE t.active = 1";
    
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

    if ($apellido !== null) {
        $sql .= " AND p.apellidos LIKE  ?";
        $params[] = '%' . $apellido . '%';
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