<?php
require_once '../../BD/BD.php'; ///bd johny
require_once '../Model/coorModel.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once"../../utilidades/LogModelo.php";
session_start();
$conn = new BD(); 
$bd = $conn->getConex();
$log = new SistemaLog($conn);
$coordModel = new coordModel($bd);
//--------------------------------------------------------------------------- CREAR COORDINACIÓN
if (isset($_POST['Crear'])) {
    $nombre = strtoupper(trim($_POST['coor_name']));
    $correo = strtolower(trim($_POST['coor_email']));
    $carnet = $_POST['coordinador'];

    $resultado = $coordModel->crearCoord($nombre, $correo, $carnet);

    switch ($resultado) {
        case 'exito':
        $log->evento(
                    "Se ha registrado una nueva coordinacion",[
                        'coordinacion'=>$nombre,
                        'idCoord'=>$carnet
                    ],$_SESSION['id']);
            header('Location: CoordCont.php?action=mostrar&n=0');
            break;
        case 'error':
            header('Location: CoordCont.php?action=mostrar&n=1');
            break;
        case 'repetido':
        $log->error(
                    "coordinacion duplicada",[
                        'coordinacion'=>$nombre
                    ],$_SESSION['id']);
            header('Location: CoordCont.php?action=mostrar&n=2');
            break;
    }
    exit;
}

//--------------------------------------------------------------------------- Busqueda de coordinadores (para el formulario de agregar)
if (isset($_GET['action']) && $_GET['action'] === 'formulario') {
    $coordinadores = $coordModel->obtenerCoordinadores();
    include '../View/coord.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Busqueda de coordinadores (para el formulario de editar)
if (isset($_GET['action']) && $_GET['action'] === 'formularioE') {
    $coordinadores = $coordModel->obtenerCoordinadores();
    $cordinacion = $coordModel->obtenerCoord($_GET['id']);
    include '../View/edit_coord.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- MOSTRAR TODAS LAS COORDINACIONES
// y aqui puedes preguntar si la variable n esta o no, o si esta vacia o lo que sea 
if (isset($_GET['action']) && $_GET['action'] === 'mostrar') {

    if (isset($_GET['n'])) {

        detectar($_GET['n']);
    }

    $coordinaciones = $coordModel->getAll(); // este debe incluir correo y carnet
    include '../View/list_coord.php';
    exit;
}
//---------------------------------------------------------------------------

//--------------------------------------------------------------------------- MOSTRAR TODAS LAS COORDINACIONES TRABAJADOR
// y aqui puedes preguntar si la variable n esta o no, o si esta vacia o lo que sea 
if (isset($_GET['action']) && $_GET['action'] === 'mostrarT') {

    $coordinaciones = $coordModel->getAll(); // este debe incluir correo y carnet
    $coordinadores = $coordModel->obtenerCoordinadores(); // para mapear los nombres
    include '../View/list_coordT.php';
    exit;

}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------BUSCAR COORDINACIÓN POR ID O NOMBRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Buscar'])) {
    $id = !empty($_POST['id']) ? $_POST['id'] : null;
    $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : null;

    $coordinaciones = $coordModel->buscarCoord($id, $nombre);

    include '../View/list_coord.php';
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- EDITAR COORDINACIÓN

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nuevoNombre = strtoupper(trim($_POST['nombre']));
    $nuevoCorreo = strtolower(trim($_POST['correo']));
    $nuevoCarnet = $_POST['coordinador'];

    $resultado = $coordModel->editar($id, $nuevoNombre, $nuevoCorreo, $nuevoCarnet);
     $log->evento(
                    "Se ha editado una coordinacion",[
                        'coordinacion'=>$nuevoNombre,
                        'correo'=>$nuevoCarnet,
                        'idCoord'=>$nuevoCarnet,
                        'estatus'=>$resultado
                    ],$_SESSION['id']);
    header('Location: CoordCont.php?action=mostrar&n=' . ($resultado === 'exito' ? '0' : '1'));
    exit;
}
//--------------------------------------------------------------------------- ELIMINAR (DESACTIVAR) COORDINACIÓN
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($_SESSION['rol'] === 'Trabajador') {
        header('Location: CoordCont.php?action=mostrar&n=3');
        exit;
    }
    $id = $_GET['id'];

    // Obtener la cantidad de equipos activos asociados a la coordinación
    $cantidadEquipos = $coordModel->contarEquiposPorCoordinacion($id);

    // Guardar info para mostrar modal en la vista
    $_SESSION['confirm_delete'] = [
        'id' => $id,
        'cantidadEquipos' => $cantidadEquipos
    ];

    header("Location: CoordCont.php?action=mostrar");
    exit;
}

//--------------------------------------------------------------------------- CONFIRMAR ELIMINACIÓN
if (isset($_GET['action']) && $_GET['action'] === 'confirm_delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $resultado = $coordModel->delete($id);
    //Reistrar el evento en log no se realmenete donde se elimina pero debes colocarla en donde se elimina  despues de confirmar 
     $log->evento(
                    "Se ha eliminado una coordinacion",[
                        'idCoordinacion'=>$id
                    ],$_SESSION['id']);
     ///*************************hasta aqui*****************////

    header('Location: CoordCont.php?action=mostrar&n=' . ($resultado === true ? '0' : '1'));
    exit;
}
//eliminar
 //--------------------------------------------------------------------------- ELIMINAR (DESACTIVAR) ESTACIONES
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
     if($_SESSION['rol']==='Trabajador'){
    
    header('Location: estacionC.php?action=mostrar&n=3');
    exit;
}
    $id = $_GET['id'];

        // Obtener cuántos equipos hay asociados
    $cantidadEquipos = $modelE->contarEquiposPorEstacion($id);
/*
    // Mostrar mensaje de confirmación en JS
    echo "<script>
        if (confirm('¿Estás seguro de eliminar esta estación? También se eliminaran $cantidadEquipos equipo(s).')) {
            window.location.href = 'estacionC.php?action=confirm_delete&id=$id';
        } else {
            window.location.href = 'estacionC.php?action=mostrar';
        }
    </script>";
    */
    exit;
}
//---------------------------------------------------------------------------
//--------------------------------------------------------------------------- Confirmar eliminación después del aviso
if (isset($_GET['action']) && $_GET['action'] === 'confirm_delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if( $modelE->delete($id)){
        // Eliminación exitosa
        header('Location: estacionC.php?action=mostrar&n=0'); // exito
    } else {
        // Error al eliminar
        header('Location: estacionC.php?action=mostrar&n=1'); // fracaso
    }

    //     ? 'Estación y equipos asociados eliminados!' : 'Error al eliminar.';
  //  echo "<script>alert('$msg'); window.location.href='estacionC.php?action=mostrar';</script>";
    exit;
}
?>
