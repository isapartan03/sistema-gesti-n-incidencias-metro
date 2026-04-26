<?php
require_once '../Model/modulo_update_falla.php';
require_once '../Model/modulo_create_falla.php';
require_once '../Model/modulo_update_reporte.php';
require_once '../Model/modulo_create_reporte.php';
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';

session_start();
if($_SESSION['rol']==='Trabajador'){
    header('Location: controlador_read_falla.php?action=mostrar&n=3');
    exit;
}

$id = ($_GET['id']) ?? null;

if (!$id) {
    die("ID de falla no proporcionado.");
}

try {

    if (!empty($_GET['n'])) {
        detectar($_GET['n']);
        unset($_GET['n']); // Evita que persista en la URL
    }

    // Instancias de las clases necesarias
    $modelo = new ModeloUpdateFalla();
    $modelo2 = new ModeloUpdateReporte();
    $modeloDatos = new ModeloFalla();
    $modeloDatos2 = new ModeloReporte();

    // Obtener datos
    $falla = $modelo->obtenerFallaPorID($id);
    if (!$falla) {
        throw new Exception("No se pudo obtener la información de la falla.");
    }

    $reporte = $modelo2->obtenerReportePorFallaID($id);
    
    // Obtener listados
    $equipos = $modeloDatos->obtenerEquipos();
    $usuarios = $modeloDatos->obtenerUsuarios();
    $supervisores = $modeloDatos->obtenerSupervisores();
    $justificaciones = $modeloDatos2->obtenerJustificaciones();
    $coordinaciones = $modeloDatos->obtenerCoordinaciones();
    $tecnicos = $modeloDatos2->obtenerTecnicos();

    // Cargar vista
    require_once '../View/vista_update_falla.php';
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Ocurrió un error al procesar la solicitud. Por favor, intente nuevamente.");
}
?>