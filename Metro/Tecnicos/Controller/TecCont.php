<?php
require_once '../../BD/BD.php';
require_once '../Model/tecModel.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once '../../config/variablesGlobales.php';
require_once "../../utilidades/LogModelo.php";

session_start();

$conn = new BD(); 
$bd = $conn->getConex(); 
$log = new SistemaLog($conn);
$coordModel = new tecModel($bd);

//------------------------------------------------------------------- Crear tecnico
if (isset($_POST['crear'])) {

    $name = strtoupper($_POST['name']);
    $lastN = strtoupper($_POST['lastN']);
    $carnet = strtoupper($_POST['carnet']);
    $codG = strtoupper($_POST['codG']);

    $idCoord = $_POST['id_coord'];
    $resultado = $coordModel->crearTec($name, $lastN, $carnet, $codG, $idCoord);
    
    switch ($resultado) {
        case 'exito':
         ///**************************************************////

        $log->evento(
                    "Se ha registrado un nuevo tecnico",[
                        'nombres'=>$name,
                        'apellidos'=>$lastN,
                        'idTecnico'=>$carnet,
                        'idCoordinacion'=>$idCoord
                    ],$_SESSION['id']);
        ///**************************************************////
            header('Location: TecCont.php?action=mostrar&n=0');
            break;
        case 'error':
            header('Location: TecCont.php?action=mostrar&n=1');
            break;
        case 'repetido':
        $log->evento(
                    "tecnico duplicada",[
                        'idTecnico'=>$carnet,
                        'idCoordinacion'=>$idCoord
                    ],$_SESSION['id']);
            header('Location: TecCont.php?action=mostrar&n=2');
            break;
    }

    exit;
}
//---------------------------------------------------------------------------Mostrar Todos los Tecnicos
if (isset($_GET['action']) && $_GET['action'] == 'mostrar') {

    if (isset($_GET['n'])) {
        detectar($_GET['n']); // solo si hay 'n'
    }

    $tecnicos = $coordModel->getAll(); 
    include '../View/mostrarT.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- agregar tecnicos
if (isset($_GET['action']) && $_GET['action'] === 'formulario') {
    $coordinaciones = $coordModel->obtenerCoordinaciones(); 
    $codGrado = $coordModel->obtenerCodgrado();
    include '../View/agregarT.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- editar tecnicos formulario
if (isset($_GET['action']) && $_GET['action'] === 'forEditar') {
    $carnet = $_GET['carnet'];

    // Obtener listas para los selects
    $coordinaciones = $coordModel->obtenerCoordinaciones(); 
    $codGrado = $coordModel->obtenerCodgrado();
    $tecnicos= $coordModel->obtenerTecnico($carnet);

    
    include '../View/edit_tec.php';
    exit;
}

//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Editar Tecnico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $carnet = $_POST['carnet']; 
    $newname = strtoupper($_POST['name']);
    $lastN = strtoupper($_POST['lastN']);
    $codG = $_POST['codG'];
    $idCoord = $_POST['id_coord'];
    
    $resultado = $coordModel->editar($carnet, $newname, $lastN, $codG, $idCoord);
     ///**************************************************////
   
    $log->evento(
                    "Se ha editado un tecnico",[
                        'idTecnico'=>$carnet,
                        'nombres'=>$newname,
                        'apellidos'=>$lastN,
                        'coordinacion'=>$idCoord
                    ],$_SESSION['id']);
    ///**************************************************////

    header('Location: TecCont.php?action=mostrar&n=' . ($resultado ? '0' : '1'));
    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Eliminar Tecnico
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['carnet'])) {
    if($_SESSION['rol']==='Trabajador'){
    
    header('Location: TecCont.php?action=mostrar&n=3');
    exit;
}
    $carnet = $_GET['carnet'];

    $resultado = $coordModel->delete($carnet);

    if ($resultado) {
        ///**************************************************////

         $log->evento(
                    "Se ha eliminado un tecnico",[
                        'idTecnico'=>$carnet
                    ],$_SESSION['id']);
     ///**************************************************////
        header("Location: TecCont.php?action=mostrar&n=0"); // 0: Éxito
    } else {
        ///**************************************************////
         $log->error(
                    "error al eliminar  este tecnico",[
                        'idTecnico'=>$carnet
                    ],$_SESSION['id']);
     ///**************************************************////
        header("Location: TecCont.php?action=mostrar&n=1"); // 1: Error
    }

    exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------Buscar tecnito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Buscar'])) {
    $carnet = !empty($_POST['carnet']) ? $_POST['carnet'] : null;
    $name = !empty($_POST['name']) ? strtoupper($_POST['name']) : null;
    $apellido = !empty($_POST['apellido']) ? strtoupper($_POST['apellido'] ) : null;

    $tecnicos = $coordModel->buscarTec($carnet, $name, $apellido);
    include '../View/mostrarT.php';
    exit;
}
//---------------------------------------------------------------------------
?>