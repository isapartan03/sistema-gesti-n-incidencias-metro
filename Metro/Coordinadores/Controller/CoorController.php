<?php

require_once '../../BD/BD.php';
require_once '../Model/coorModel.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once"../../utilidades/LogModelo.php";
session_start();

$conn = new BD(); 
$bd = $conn->getConex(); 
$log = new SistemaLog($conn);

$coorModel = new coorModel($bd);

//--------------------------------------------------------------------------- Busqueda de codGrado (para el formulario de agregar)
if (isset($_GET['action']) && $_GET['action'] === 'formulario') {
    $codGrado = $coorModel->obtenerCodgrado(); 
    include '../View/addCoor.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Busqueda de codGrado (para el formulario editar)
if (isset($_GET['action']) && $_GET['action'] === 'formularioEdi') {
    $codGrado = $coorModel->obtenerCodgrado(); 

    $carnet = $_GET['carnet']; 
    $coordinador=$coorModel->obtenerCoordinador($carnet);
    include '../View/edit_coordinadores.php';
    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------CREAR COORDINADOR
if (isset($_POST['crear'])) {

    $name = strtoupper($_POST['name']);
    $lastN = strtoupper($_POST['lastN']);
    $carnet = strtoupper($_POST['carnet']);
    $codG = strtoupper($_POST['codG']);
    $gerencia = strtoupper($_POST['gerencia']);

    $resultado = $coorModel->crearCoord($name, $lastN, $carnet, $codG, $gerencia);

    switch ($resultado) {
        case 'exito':
        ///**************************************************////

        $log->evento(
                    "Se ha registrado un nuevo coordinador",[
                        'nombreCoordinador'=>$name,
                        'apellidoCoordinador'=>$lastN,
                        'idCoord'=>$carnet,
                        'gerencia'=>$gerencia
                    ],$_SESSION['id']);
        ///**************************************************////

            header('Location: CoorController.php?action=mostrar&n=0');
            break;
        case 'error':
            header('Location: CoorController.php?action=mostrar&n=1');
            break;
        case 'repetido':
        ///**************************************************////

         $log->error(
                    "coordinador duplicado",[
                        'coordinacion'=>$nombre
                    ],$_SESSION['id']);
        ///**************************************************////

            header('Location: CoorController.php?action=mostrar&n=2');
            break;
    }
    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Mostrar Todos LOS COORDINADORES
if (isset($_GET['action']) && $_GET['action'] == 'mostrar') {

    if (isset($_GET['n'])) {
    detectar($_GET['n']); // solo si hay 'n'
    }
    
    $coordinadores = $coorModel->getAll(); 
    include '../View/mostrarC.php';
    exit;
}
//---------------------------------------------------------------------------

//--------------------------------------------------------------------------- Editar coordinadores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $carnet = $_POST['carnet']; 
    $newname = strtoupper($_POST['name']);
    $lastN = strtoupper($_POST['lastN']);
    $codG = $_POST['codG'];
    $gerencia = strtoupper($_POST['gerencia']);

    $resultado = $coorModel->editar($carnet, $newname, $lastN, $codG, $gerencia);
     ///**************************************************////
   
    $log->evento(
                    "Se ha editado un coordinador",[
                        'coordinacion'=>$newname,
                        'correo'=>$lastN,
                        'idCoord'=>$carnet,
                        'gerencia'=>$gerencia,
                        'estatus'=>$resultado
                    ],$_SESSION['id']);
    ///**************************************************////


     header('Location: CoorController.php?action=mostrar&n=' . ($resultado ? '0' : '1'));
    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Eliminar COORDINADORES
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['carnet'])) {
    if($_SESSION['rol']==='Trabajador'){
    
    header('Location: CoorController.php?action=mostrar&n=3');
    exit;
}
    $carnet = $_GET['carnet'];

    $resultado = $coorModel->delete($carnet);

    if ($resultado) {
        ///**************************************************////
         $log->evento(
                    "Se ha eliminado un coordinador",[
                        'idCoord'=>$carnet,
                        'estatus'=>$resultado
                    ],$_SESSION['id']);
        ///**************************************************////

        header("Location: CoorController.php?action=mostrar&n=0"); // 0: Éxito
    } else {
        ///**************************************************////

        $log->error(
                    "error al eliminar una coordinador",[
                        'idCoord'=>$carnet,
                        'estatus'=>$resultado
                    ],$_SESSION['id']);
        ///**************************************************////

        header("Location: CoorController.php?action=mostrar&n=1"); // 1: Error
    }

    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Buscar COORDINADORES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Buscar'])) {
    $carnet = !empty($_POST['carnet']) ? $_POST['carnet'] : null;
    $name = !empty($_POST['name']) ? strtoupper($_POST['name']) : null;
    $gerencia = !empty($_POST['gerencia']) ? strtoupper($_POST['gerencia']) : null;

    $coordinadores = $coorModel->buscarCoor($carnet, $name, $gerencia);

    include '../View/mostrarC.php';
    exit;
}
//---------------------------------------------------------------------------
?>