<?php
require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
session_start();
/*Lo que se hace acá es que llamamos al modulo correspondiente instanciamos y cargamos sus módulos y al final cargamos la pantalla que necesitamos*/

try {
    require_once '../Model/modulo_create_falla.php';

    // Instancia del modelo
    $modelo = new ModeloFalla();

    // Obtener los datos
    try {

        if (!empty($_GET['n'])) {
            detectar($_GET['n']);
            unset($_GET['n']); // Evita que persista en la URL
        }

        $equipos = $modelo->obtenerEquipos();
        $prioridades = $modelo->obtenerPrioridades();
        $idUser = $_SESSION['id'];
        $usuario = $_SESSION['userName'];
        $supervisores = $modelo->obtenerSupervisores();
    } catch (Exception $e) {
        throw new Exception("Error al obtener datos: " . $e->getMessage());
    }

    // Cargar la vista y pasarle los datos
    require_once '../View/vista_create_falla.php';
} catch (Exception $e) {
    echo "Error en controlador_create_falla: " . $e->getMessage();
}
?>