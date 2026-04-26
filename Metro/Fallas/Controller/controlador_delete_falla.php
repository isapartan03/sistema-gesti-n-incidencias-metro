<?php

session_start();
if (empty($_SESSION['id'])) {
    die("Usuario no autenticado.");
}

require_once '../Model/modulo_delete_falla.php';
require_once 'mailer.php'; 

//modulo que necesitamos para este caso

try {
    if (isset($_GET['id'])) {
        $idFalla = ($_GET['id']);

        //llamamos a la clase necesaria
        $modelo = new ModeloDeleteFalla();
        
        //ejecutamos el método necesario
        try {
            if ($modelo->finalizarFalla($idFalla)) {//si el método corre correctamente nos saldrá este mensaje
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
                    
                    header("Location: ../Controller/controlador_read_falla.php?n=0");
                            exit();
            } else {// si no es el caso saldrá este mensaje
                echo "Error al desactivar la falla.";
            }
        } catch (Exception $e) {
            throw new Exception("Error en finalizarFalla: " . $e->getMessage());
        }
    } else {//si accedes a la pantalla sin el id
        echo "ID de falla no proporcionado.";
    }
} catch (Exception $e) {
    echo "Error en controlador_delete_falla: " . $e->getMessage();
}
//
?>