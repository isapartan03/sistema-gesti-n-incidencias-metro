<?php
require_once '../../BD/BD.php';
require_once '../Model/modulo_delete_falla.php';
require_once 'mailer.php'; 
require_once '../Model/modulo_filtro.php';
date_default_timezone_set('America/Caracas');
try {
    //se verifica si
    if ($_SERVER["REQUEST_METHOD"] !== "POST") exit("Acceso no permitido.");

    //obtenemos los valores que se mandan por el post
    $idFalla = trim($_POST['falla']);
    $idCoordinacion = intval($_POST['coordinacion']);
    $idJustificacion = trim($_POST['justificacion']); 
    $ubicacion = trim($_POST['ubicacion']);
    $observacion = trim($_POST['observacion']);
    $diagnostico = trim($_POST['diagnostico']);
    $fechaApertura = $_POST['fecha'];
    $fechaCierre = null;
    $tecnico = $_POST['carnet_tecnico'];

    if (empty($idJustificacion)) {
        exit("Justificación no válida.");
    }

    $conn = (new BD())->getConex();

    $conn->begin_transaction();

    try {
        
        $esFinalizada = ($idJustificacion === 'N/A'); //si la justificación ninguna es seleccionada
        $fechaCierre = $esFinalizada ? date('Y-m-d H:i') : null; //fecha de cierre

         //---------filtro-------------------//
        $filtro = new Filtro();
        $resultFiltro = $filtro->verificarContenido($observacion);
        if ($resultFiltro ['inapropiado']){
            header("Location: controlador_create_reporte.php?n=5&idFalla=" . $_POST['falla']);
            exit();
        }

        $resultFiltro = $filtro->verificarContenido($diagnostico);
        if ($resultFiltro ['inapropiado']){
            header("Location: controlador_create_reporte.php?n=5&idFalla=" . $_POST['falla']);
            exit();
        }

        $observacionLimpio = $filtro->filtrarTexto($observacion);
        $diagnosticoLimpio = $filtro->filtrarTexto($diagnostico);
        //---------fin del filtro---------------//


        // 1) Insertar en reporte
        $sql1 = "INSERT INTO reporte 
                (ID_Falla, ID_Coordinacion, ID_Justificacion, Ubicacion, Observaciones, Diagnostico, F_apertura, F_cierre)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $resultado1 = $conn->prepare($sql1);
        if (!$resultado1) throw new Exception($conn->error);

        $resultado1->bind_param(
            "sissssss",
            $idFalla,
            $idCoordinacion,
            $idJustificacion,
            $ubicacion,
            $observacionLimpio,
            $diagnosticoLimpio,
            $fechaApertura,
            $fechaCierre
        );
        if (!$resultado1->execute()) {
            throw new Exception($resultado1->error);
        }

        // 2) Obtener el ID del reporte (si lo necesitas)
        $idReporte = $resultado1->insert_id;
        $resultado1->close();

        // 3) Insertar en tecnicos_fallas
        $fechaAsignacion = date('Y-m-d H:i');
        $sql2 = "INSERT INTO tecnicos_fallas (carnet_tecnico, id_falla, Diagnostico, Observaciones ,fecha_asignacion)
                 VALUES (?, ?, ?, ?, ?)";
        $resultado2 = $conn->prepare($sql2);
        if (!$resultado2) throw new Exception($conn->error);

        $resultado2->bind_param(
            "sssss",
            $tecnico,
            $idFalla, 
            $diagnosticoLimpio,
            $observacionLimpio,
            $fechaAsignacion
        );
        if (!$resultado2->execute()) {
            throw new Exception($resultado2->error);
        }
        $resultado2->close();

         $conn->commit();

        //justificación ninguna seleccionada
        if ($esFinalizada) {
            session_start();
            $idUsuario = $_SESSION['id'] ?? null;
            $sql3 = "UPDATE falla SET Falla_Status = 0, ID_Usuario_cierre = ? WHERE ID_Falla = ?";
            $resultado3 = $conn->prepare($sql3);
            if (!$resultado3) throw new Exception($conn->error);
            
            $resultado3->bind_param("is", $idUsuario, $idFalla);
            if (!$resultado3->execute()) throw new Exception($resultado3->error);
            $modelo = new ModeloDeleteFalla;
            $datoscorreo = $modelo->obtenerDatosParaCorreo($idFalla);

                if ($datoscorreo && !empty($datoscorreo['CorreoCoordinacion'])){
                    cerrarCorreo(
                        $datoscorreo['CorreoCoordinacion'],
                        $datoscorreo['NombreEquipo'],
                        $datoscorreo['N_Ambiente'],
                        $datoscorreo['NombreEstacion'],
                        $datoscorreo['TecnicoCierre'],
                        $idFalla,
                         $datoscorreo['Nombre']
                    ); 
                } else{
                        error_log("No se pudo enviar el correo de cierre. Datos incompletos para la falla: $idFalla");
                    }
            $resultado3->close();
        }
        //var_dump($datoscorreo['TecnicoCierre']);

        // 4) Confirmar todo
        $conn->commit();

            header("Location: controlador_read_falla.php?n=0");
                  exit();
    } catch (Exception $e) {
        $conn->rollback();
        // Para depuración:
        error_log("Error al guardar reporte/técnico: " . $e->getMessage());
        exit("Ocurrió un error: " . htmlspecialchars($e->getMessage()));
    }

    $conn->close();
} catch (Exception $e) {
    echo "Error en controlador_create_reporte: " . $e->getMessage();
}
?>