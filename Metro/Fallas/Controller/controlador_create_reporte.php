<?php
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
require_once '../Model/modulo_create_reporte.php';
session_start();

date_default_timezone_set('America/Caracas');

try {
    $idFalla = isset($_GET['idFalla']) ? trim($_GET['idFalla']) : null;

    if (!$idFalla) {
        exit("ID de falla no proporcionado.");
    }

    $modelo = new ModeloReporte();

    try {

        if (!empty($_GET['n'])) {
            detectar($_GET['n']);
            unset($_GET['n']); // Evita que persista en la URL
        }

        // 1. Obtener el ID del equipo asociado a la falla
        $falla = $modelo->obtenerFallaPorID($idFalla);
        if (!$falla || !isset($falla['ID_Equipos'])) {
            exit("No se encontró la falla o el equipo asociado.");
        }

        $idEquipo = intval($falla['ID_Equipos']);

        // 2. Obtener datos del equipo (incluyendo coordinación)
        $datosEq = $modelo->obtenerDatosEquipo($idEquipo);
        
        if (!$datosEq) {
            exit("No se encontraron datos del equipo.");
        }

        // 3. Extraer datos de coordinación del equipo
        if (!isset($datosEq['ID_Coordinacion']) || !isset($datosEq['Nombre_Coordinacion'])) {
            exit("No se encontró la coordinación asociada al equipo.");
        }

        $idcoordinacion = $datosEq['ID_Coordinacion'];
        $Coordinacion = $datosEq['Nombre_Coordinacion'];

        // 4. Construir la ubicación correctamente
        $nAmbiente = $datosEq['N_Ambiente'] ?? 'N/A';
        $nombreEstacion = $datosEq['Nombre_Estacion'] ?? 'N/A';
        $ubicacion = $nAmbiente . '/' . $nombreEstacion;

        // 5. Obtener datos para formulario
        $justificaciones = $modelo->obtenerJustificaciones();
        $tecnicos = $modelo->obtenerTecnicos();
        $fechaActual = date('Y-m-d H:i');

        // 6. Cargar la vista
        require_once '../View/vista_create_reporte.php';
    } catch (Exception $e) {
        throw new Exception("Error en el proceso de creación de reporte: " . $e->getMessage());
    }
} catch (Exception $e) {
    echo "Error en controlador_create_reporte: " . $e->getMessage();
}
?>