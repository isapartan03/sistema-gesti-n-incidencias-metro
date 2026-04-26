<?php
require_once '../../BD/BD.php';
require_once '../Model/station_Model.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once "../../utilidades/LogModelo.php";

session_start();

$conn = new BD(); 
$bd = $conn->getConex(); 
$log = new SistemaLog($conn);
$modelE = new station_Model($bd);
//--------------------------------------------------------------------------- CREAR ESTACION
if (isset($_POST['Crear'])) {
    $nombre = strtoupper($_POST['estac_name']);
    $result = $modelE->crearEstacion($nombre);

    switch ($result) {
        case 'exito':
         ///**************************************************////

        $log->evento(
                    "Se ha registrado una nueva estacion",[
                        'nombre'=>$nombre
                    ],$_SESSION['id']);
        ///**************************************************////
            header('Location: estacionC.php?action=mostrar&n=0');
            break;
        case 'error':
            header('Location: estacionC.php?action=mostrar&n=1');
            break;
        case 'repetido':
         $log->evento(
                    "estacion duplicada",[
                        'nombre'=>$nombre
                    ],$_SESSION['id']);
            header('Location: estacionC.php?action=mostrar&n=2');
            break;
    }
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- MOSTRAR TODAS LAS ESTACIONES 
if (isset($_GET['action']) && $_GET['action'] === 'mostrar') {

    if (isset($_GET['n'])) {
        detectar($_GET['n']); // solo si hay 'n'
    }

    $estacion = $modelE->getAll();
    include '../View/list_estacion.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- EDITAR ESTACIONES
if (isset($_GET['action']) && $_GET['action'] === 'edition') {
   
    $id = $_GET['id'];  
    $estacion=$modelE->obtenerEstacion($id);
  
   
   include '../View/edit_station.php';
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id']; 
    $newname = strtoupper($_POST['nombre']);

    $resultado = $modelE->editar($id, $newname);
     ///**************************************************////
   
    $log->evento(
                    "Se ha editado una estacion",[
                        'idEstacion'=>$id,
                        'nombre'=>$newname
                    ],$_SESSION['id']);
    ///**************************************************////

    header('Location: estacionC.php?action=mostrar&n=' . ($resultado ? '0' : '1'));
    exit;
}
//---------------------------------------------------------------------------
//----------------------------- ELIMINAR (MOSTRAR MODAL)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($_SESSION['rol'] === 'Trabajador') {
        header('Location: estacionC.php?action=mostrar&n=3');
        exit;
    }

    $id = $_GET['id'];
    $cantidadEquipos = $modelE->contarEquiposPorEstacion($id);

    //  Guardamos en sesión los datos necesarios para el modal
    $_SESSION['confirm_delete'] = [
        'id' => $id,
        'cantidadEquipos' => $cantidadEquipos
    ];

    // Redirigimos a la vista donde está el modal
    header('Location: estacionC.php?action=mostrar');
    exit;
}

//----------------------------- CONFIRMAR ELIMINACIÓN
if (isset($_GET['action']) && $_GET['action'] === 'confirm_delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

 

//    header('Location: estacionC.php?action=mostrar&n=' . ($resultado === true ? '0' : '1'));

    if( $modelE->delete($id)){
     ///**************************************************////

         $log->evento(
                    "Se ha eliminado un equipo",[
                        'idEquipo'=>$id
                    ],$_SESSION['id']);
     ///**************************************************////

        // Eliminación exitosa
        header('Location: estacionC.php?action=mostrar&n=0'); // exito
    } else {
     ///**************************************************////
         $log->error(
                    "error al eliminar una estacion",[
                        'idEquipo'=>$id
                    ],$_SESSION['id']);
     ///**************************************************////


        // Error al eliminar
        header('Location: estacionC.php?action=mostrar&n=1'); // fracaso
    }

    exit;
}

//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- BUSCAR ESTACION POR ID O NOMBRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Buscar'])) {
    $id = !empty($_POST['id']) ? $_POST['id'] : null;
    $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : null;

    $estacion = $modelE->buscarStation($id, $nombre);
    include '../View/list_estacion.php';
    exit;
}
//---------------------------------------------------------------------------
?>