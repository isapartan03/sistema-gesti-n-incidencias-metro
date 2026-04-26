<?php
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
//se carga el modulo con sus métodos y al final la pantalla que deseamos
require_once '../Model/modulo_read_falla.php';


try {
    $modelo = new ModeloFalla();

    if (isset($_GET['accion'])) {
        switch ($_GET['accion']) {
            case 'limpiar':
                header("Location: controlador_read_falla.php?action=mostrar");
                exit;
                
            case 'exportar':
                // Obtener fallas con los filtros actuales
                $filtros = $_GET;
                unset($filtros['accion']); // Quitamos la acción para no interferir
                unset($filtros['n']);      // Quitamos notificaciones previas
                
                $fallasParaExportar = $modelo->ObtenerFallas($filtros);
                
                if (empty($fallasParaExportar)) {
                    // Redirigir con error, manteniendo los filtros
                    $queryString = http_build_query($filtros);
                    header("Location: controlador_read_falla.php?n=4&" . $queryString);
                    exit;
                } else {
                    // Redirigir a la página de exportación con los filtros
                    header("Location: btn_exportar_fallas.php?" . http_build_query($filtros));
                    exit;
                }
                break;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if (isset($_GET['accion']) && $_GET['accion'] === 'limpiar') {
        header("Location: controlador_read_falla.php?action=mostrar");
         exit;
        }

        try {
            $fallas = $modelo->ObtenerFallas($_GET);
            if (isset($_GET['n'])) {
                detectar($_GET['n']);
                unset($_GET['n']);
            }
        } catch (Exception $e) {
            throw new Exception("Error al obtener fallas (POST): " . $e->getMessage());
        }
    } else {
        try {
            $fallas = $modelo->ObtenerFallas();
            if (isset($_GET['n'])) {
                detectar($_GET['n']);
                unset($_GET['n']);
            }
        } catch (Exception $e) {
            throw new Exception("Error al obtener fallas (GET): " . $e->getMessage());
        }
    }

    // Para los selects
    try {
        $usuarios = $modelo->obtenerUsuarios();
        $supervisores = $modelo->obtenerSupervisores();
        $equipos = $modelo->obtenerEquipos();
        $justificaciones = $modelo->obtenerJustificaciones();
        $status = $modelo->obtenerStatus();
        $prioridades = $modelo->obtenerPrioridades();
    } catch (Exception $e) {
        throw new Exception("Error al cargar datos para selects: " . $e->getMessage());
    }

    require_once '../View/vista_read_falla.php';
} catch (Exception $e) {
    echo "Error en controlador_read_falla: " . $e->getMessage();
}


?>