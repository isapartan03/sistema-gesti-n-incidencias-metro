<?php
require_once '../../BD/BD.php';
require_once '../Model/ModelE.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once"../../utilidades/LogModelo.php";
require_once"../../utilidades/LogModelo.php";

session_start();

$conn = new BD(); 
$bd = $conn->getConex(); 
$log = new SistemaLog($conn);
$equipoM = new ModelE($bd);

//------------------------------------------------------------------- Crear Equipo
if (isset($_POST['crear'])) {

    $name = strtoupper($_POST['name']);
    $id = $_POST['id']; //ID
    $numberA = $_POST['numberA'];
    $idCoord = $_POST['id_coord'];
    $idEsta = $_POST['id_estacion'];

    $resultado = $equipoM->crearE($id, $name, $numberA, $idCoord, $idEsta);
    
    switch ($resultado) {
        case 'exito':
           ///**************************************************////

        $log->evento(
                    "Se ha registrado un nuevo equipo",[
                        'nombre'=>$name,
                        'idEstacion'=>$idEsta,
                        'idCoordinacion'=>$idCoord,
                        'ambiente'=>$numberA
                    ],$_SESSION['id']);

            header('Location: equipoC.php?action=mostrar&n=0');
            break;   
        ///**************************************************////
        case 'error':
            header('Location: equipoC.php?action=mostrar&n=1');
            break;
        case 'repetido':
        ///**************************************************////

         $log->error(
                    "equipo duplicado",[
                        'equipo'=>$name
                    ],$_SESSION['id']);
        ///**************************************************////
            header('Location: equipoC.php?action=mostrar&n=2');
            break;
    }

    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Busqueda de coordinaciones y estacion (para el formulario de agregar)
if (isset($_GET['action']) && $_GET['action'] === 'formulario') {
    $coordinaciones = $equipoM->obtenerCoordinaciones(); 
    $estacion = $equipoM->obtenerEstacion(); 
    include '../View/Add_equipo.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- MOSTRAR TODOS LOS EQUIPOS
if (isset($_GET['action']) && $_GET['action'] === 'mostrar') {

    if (isset($_GET['n'])) {
        detectar($_GET['n']); // solo si hay 'n'
    }

    $resultado = $equipoM->getAll();
    $coordinaciones = $equipoM->obtenerCoordinaciones();
    $estacion = $equipoM->obtenerEstacion();
    include '../View/All_Equipo.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- MOSTRAR TODOS LOS EQUIPOS Trabajador
if (isset($_GET['action']) && $_GET['action'] === 'mostrarT') {

    $resultado = $equipoM->getAll();
    $coordinaciones = $equipoM->obtenerCoordinaciones();
    $estacion = $equipoM->obtenerEstacion();
    include '../View/All_EquipoT.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Busqueda de coordinaciones y estacion (para el formulario editar)
if (isset($_GET['action']) && $_GET['action'] === 'edition') {
    $coordinaciones = $equipoM->obtenerCoordinaciones(); 
    $estacion = $equipoM->obtenerEstacion();
    $id = $_GET['id'];  
    $equipo = $equipoM->obtenerEquipo($id);
    
    //var_dump($equipo);
   include '../View/edit_equipo.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id']; 
    $name = strtoupper($_POST['name']);
    $name_a = $_POST['numberA'];
    $idCoord = $_POST['id_coord'];
    $idEsta = $_POST['id_estacion'];

    $resultado = $equipoM->edit($id, $name, $name_a, $idCoord, $idEsta );
    ///**************************************************////
   
    $log->evento(
                    "Se ha editado un equipo",[
                        'nombre'=>$name,
                        'ambiente'=>$name_a,
                        'idEstacion'=>$idEsta
                    ],$_SESSION['id']);
    ///**************************************************////
    
    header('Location: equipoC.php?action=mostrar&n=' . ($resultado ? '0' : '1'));
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- ELIMINAR UN EQUIPO
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if($_SESSION['rol']==='Trabajador'){
    
    header('Location: EquipoC.php?action=mostrar&n=3');
    exit;
}
    $id = $_GET['id'];

    $result = $equipoM->delete($id); 
    $log->evento(
                    "Se ha eliminado un equipo",[
                        'idEquipo'=>$id
                    ],$_SESSION['id']);
   

    header('Location: equipoC.php?action=mostrar&n='.($result=='exito' ? '0' : '1'));
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- ENTRAR EN EL FORMULARIO DE BUSQUEDA
if (isset($_GET['action']) && $_GET['action'] === 'busqueda') {
    $coordinaciones = $equipoM->obtenerCoordinaciones(); 
    $estacion = $equipoM->obtenerEstacion(); 
    include '../View/All_Equipo.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- BUSCAR EQUIPO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
$name = !empty($_POST['nombre']) ? $_POST['nombre'] : null;
$numberA = !empty($_POST['ambiente']) ? $_POST['ambiente'] : null;
$idCoord = !empty($_POST['id_coord']) ? $_POST['id_coord'] : null;
$idEsta = !empty($_POST['id_estacion']) ? $_POST['id_estacion'] : null;
$estatus = isset($_POST['status']) ? $_POST['status']: null;



    $resultado = $equipoM->buscarEquipo($name, $numberA, $idCoord, $idEsta, $estatus);

    $coordinaciones = $equipoM->obtenerCoordinaciones(); 
    $estacion = $equipoM->obtenerEstacion(); 
    include '../View/All_Equipo.php';
   exit;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
if ($_GET['action'] === 'desincorporar' && isset($_GET['id'])) {
    $equipoM->cambiarEstado($_GET['id'], 0); // 0 = inactivo
    header('Location: EquipoC.php?action=mostrar&n=0');
    exit;
}

if ($_GET['action'] === 'incorporar' && isset($_GET['id'])) {
    $equipoM->cambiarEstado($_GET['id'], 1); // 1 = activo
    header('Location: EquipoC.php?action=mostrar&n=0');
    exit;
}



?>