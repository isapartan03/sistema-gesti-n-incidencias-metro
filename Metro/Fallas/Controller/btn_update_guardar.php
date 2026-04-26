<?php
require_once '../Model/modulo_update_falla.php';
require_once '../Model/modulo_update_reporte.php';
require_once '../Model/modulo_tecnico_falla.php';
require_once '../Model/modulo_filtro.php';

try {
    //si es un server diferente a POST saldrá este mensaje
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        exit("Acceso no permitido.");
    }

    // -- Datos Falla --
    $idFalla = trim($_POST['id']);
    $equipo = intval($_POST['equipo']);
    $usuario = intval($_POST['usuario']);
    $supervisor = intval($_POST['supervisor']);

    // -- Datos Reporte --
    $idReporte = !empty($_POST['id_reporte']) ? ($_POST['id_reporte']) : null;
    $idCoord = intval($_POST['id_coordinacion']);
    $idJustif = trim($_POST['id_justificacion']);
    $ubicacion = trim($_POST['ubicacion']);
    $observacion = trim($_POST['observacion']);
    $diagnostico = trim($_POST['diagnostico']);

    $carnetTecnico = isset($_POST['carnet_tecnico']) ? trim($_POST['carnet_tecnico']) : null;

    //instancia de los métodos
    $modelo = new ModeloUpdateFalla();
    $modelo2 = new ModeloUpdateReporte();
    $modelo3 = new ModeloTecnicoFalla();

    //---------filtro-------------------//
    $filtro = new Filtro();
    $resultFiltro = $filtro->verificarContenido($observacion);
    if ($resultFiltro ['inapropiado']){
        header("Location: controlador_update_falla.php?n=5&id=" . $_POST['id']);
        exit();
    }

    $resultFiltro = $filtro->verificarContenido($diagnostico);
    if ($resultFiltro ['inapropiado']){
        header("Location: controlador_update_falla.php?n=5&id=" . $_POST['id']);
        exit();
    }

    $observacionLimpio = $filtro->filtrarTexto($observacion);
    $diagnosticoLimpio = $filtro->filtrarTexto($diagnostico);
    //---------fin del filtro---------------//

    // Actualizar reporte si existía
    $okReporte = true;
    if ($idReporte) {
        try {
            $okReporte = $modelo2->actualizarReporte(
                $idReporte,
                $idCoord,
                $idJustif,
                $ubicacion,
                $observacionLimpio,
                $diagnosticoLimpio
            );
        } catch (Exception $e) {
            throw new Exception("Error al actualizar reporte: " . $e->getMessage());
        }
    }

    if ($carnetTecnico && $diagnosticoLimpio && $observacionLimpio != null){
        try{
            $modelo3->insertarDiagnosticoTecnico($carnetTecnico, $idFalla ,$diagnosticoLimpio, $observacionLimpio);
        }catch (Exception $e) {
            throw new Exception("Error al enviar información sobre el carnet del tecnico, id de falla o diagnostico: " . $e->getMessage());
    }
}
    //si se actualiza correctamente saldrá este mensaje
    if ($okReporte) {
        header('Location: controlador_read_falla.php?n=0');
       exit();
    } else {// si no es así saldrá este mensaje
        echo "Error al actualizar: "
             . (!$okReporte ? "reporte fallido." : "");
    }
} catch (Exception $e) {
    echo "Error en controlador_update_falla: " . $e->getMessage();
}
?>