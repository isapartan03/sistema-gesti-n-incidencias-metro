<?php
require_once '../Model/modulo_detalle_falla.php';
session_start();

// 1. Validación y saneamiento del input
$idFalla = trim($_GET['id'] ?? '');

// 2. Validación básica
if (empty($idFalla)) {
    header("Location: controlador_read_falla.php?error=id_requerido");
    exit;
}

try {
    // 3. Instanciar modelo
    $modelo = new DetalleFalla();
    
    // 4. Obtener datos
    $falla = $modelo->ObtenerFallaPorID($idFalla);
    
    // 5. Verificar si se encontró la falla
    if ($falla === null) {
        header("Location: controlador_read_falla.php?error=falla_no_encontrada&id=" . urlencode($idFalla));
        exit;
    }
    
    // 6. Preparar datos para la vista
    $reporte = null;
    if (!empty($falla['ID_reporte'])) {
        $reporte = [
            'Ubicacion' => $falla['Ubicacion'] ?? null,
            'Diagnostico' => $falla['Diagnostico'] ?? null,
            'Observaciones' => $falla['Observaciones'] ?? null,
            'Justificacion' => $falla['Justificacion'] ?? null, // Corregido mayúscula
            'F_apertura' => $falla['F_apertura'] ?? null,   // Corregido mayúscula
            'F_cierre' => $falla['F_cierre'] ?? null           // Corregido mayúscula
        ];
        
        // Limpiar datos principales (opcional)
        unset(
            $falla['Ubicacion'],
            $falla['Diagnostico'],
            $falla['Observaciones'],
            $falla['Justificacion'],
            $falla['F_apertura'],
            $falla['F_cierre']
        );
    }

     $tecnico = $modelo->ObtenerTecnicoFalla($idFalla);
     $historialTecnicos = $modelo->obtenerHistorialTecnicos($idFalla);
    
    // 7. Incluir vista
    require_once '../View/vista_detalle_falla.php';
    
} catch (Exception $e) {
    // 8. Manejo de errores
    error_log("Error en controlador_detalle_falla: " . $e->getMessage());
    header("Location: controlador_read_falla.php?error=servidor");
    exit;
}
?>