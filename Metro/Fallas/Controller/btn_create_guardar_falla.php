<?php
require_once '../../BD/BD.php';
require_once '../Model/modulo_filtro.php';
require_once '../../utilidades/repositorio.php';
require_once 'mailer.php'; 

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $Equipo = $_POST['equipo'];
        $usuario = $_POST['usuario'];
        $supervisor = $_POST['supervisor'];
        $descripcion = $_POST['descripcion'];
        $prioridad = $_POST['prioridad'];

        $conexion = new BD();
        $conn = $conexion->getConex();

        $anio = date('Y'); 
        $mes = date('m');

            // Buscar el último ID_Falla del mes actual
        $sqlUltimo = "SELECT ID_Falla FROM falla WHERE ID_Falla LIKE ? ORDER BY ID_Falla DESC LIMIT 1";
        $like = "$anio"."$mes"."%";
        $stmt = $conn->prepare($sqlUltimo);
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $stmt->bind_result($ultimoID);
        $stmt->fetch();
        $stmt->close();
            //var_dump($ultimoID);

            // Obtener número correlativo
            if ($ultimoID) {
                // Extraer los últimos 4 dígitos
                $numero = intval(substr($ultimoID, -4)) + 1;
            } else {
                $numero = 1;
            }

            // Formar nuevo ID_Falla
            $idFalla = sprintf('%s%s%04d', $anio, $mes, $numero);
            //echo "$idFalla";

            //---------filtro-------------------//
            $filtro = new Filtro();
            $resultFiltro = $filtro->verificarContenido($descripcion);
            if ($resultFiltro ['inapropiado']){
                header("Location: controlador_create_falla.php?n=5");
                exit();
            }

            $descripcionLimpia = $filtro->filtrarTexto($descripcion);
            //---------fin del filtro---------------//

        // 2. Insertar nueva falla
        $sql = "INSERT INTO falla (ID_Falla, ID_Personal, ID_Usuario, Descripcion, ID_Equipos, ID_Prioridad)
                VALUES (?, ?, ?, ?, ?, ?)";
        $resultado = $conn->prepare($sql);
        if (!$resultado) throw new Exception("Error en preparación: " . $conn->error);

        $resultado->bind_param("siisis", $idFalla, $supervisor, $usuario, $descripcionLimpia, $Equipo, $prioridad);
        if (!$resultado->execute()) throw new Exception("Error al ejecutar: " . $resultado->error);
        $resultado->close();

        // 3. Obtener correo de coordinación según el equipo
        $sqlCorreo = "SELECT c.correo, e.Nombre, e.N_Ambiente, est.Nombre, c.Nombre 
        FROM equipos e
        JOIN coordinacion c ON c.ID_Coordinacion = e.ID_Coordinacion
        JOIN estacion est ON e.ID_Estacion = est.ID_Estacion
        WHERE e.ID_Equipos = ?
        LIMIT 1";
        $stmtCorreo = $conn->prepare($sqlCorreo);
        
        if (!$stmtCorreo) throw new Exception("Error preparando consulta de correo: " . $conn->error);
        $emailDestino = null;

        $stmtCorreo->bind_param("i", $Equipo);
        $stmtCorreo->execute();
        $stmtCorreo->store_result();
        if($stmtCorreo->num_rows > 0){
        $stmtCorreo->bind_result($emailDestino, $Equipo, $N_Ambiente, $Estacion, $coordinacion);
        $stmtCorreo->fetch();
        }
        //error_log("DEBUG: emailDestino = " . var_export($emailDestino, true));
        $stmtCorreo->close();

        //echo "$equipo $N_Ambiente $Estacion";
        // 4. Enviar correo si se obtuvo el correo
        if ($emailDestino) {
            try {
                enviarCorreo($emailDestino, $Equipo, $N_Ambiente, $Estacion, $idFalla, $coordinacion);
            } catch (Exception $e) {
                error_log("Error al enviar correo: " . $e->getMessage());
                // No detiene el proceso
            }
        } else {
            error_log("No se encontró correo para la coordinación del equipo ID $Equipo");
        }

        $conn->close();

        header("Location: controlador_read_falla.php?n=0");
        exit();
    } else {
        echo "Acceso no válido.";
    }
} catch (Exception $e) {
    echo "Error en el proceso: " . $e->getMessage();
}
?>