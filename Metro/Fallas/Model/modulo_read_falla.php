<?php
require_once '../../BD/BD.php';
session_start();
class ModeloFalla{
    private $conn;

    public function __construct (){
        try {
            $conexion = new BD();
            $this->conn = $conexion->getConex();
        } catch (Exception $e) {
            throw new Exception("Error en la conexión: " . $e->getMessage());
        }
    }

    //se muestran las fallas que tiene reportes hechos
    public function ObtenerFallas($filtros = []) {
        $datos = [];    
        try {
            // sentencia sql para poder visualizar todos las fallas y su reporte asociado a ella
            $sql = "SELECT f.ID_Falla, per.nombres, u.Username, uc.Username AS UsuarioCierre, f.descripcion, f.Falla_Status, e.Nombre, r.ID_reporte, r.Ubicacion, r.Observaciones, r.Diagnostico, c.Nombre AS Coordinacion, j.descripcion AS Justificacion, DATE_FORMAT(r.F_apertura, '%Y-%m-%d %H:%i') AS F_apertura, DATE_FORMAT(r.F_cierre,  '%Y-%m-%d %H:%i') AS F_cierre, f.ID_Prioridad AS Prioridad
            FROM falla f
            JOIN personal p ON p.carnet = f.ID_Personal
            JOIN persona per ON per.carnet = p.carnet
            JOIN usuario u ON u.ID = f.ID_Usuario
            JOIN equipos e ON e.ID_Equipos = f.ID_Equipos
            LEFT JOIN usuario uc ON uc.ID = f.ID_Usuario_cierre
            LEFT JOIN reporte r ON r.ID_Falla = f.ID_Falla
            LEFT JOIN coordinacion c ON c.ID_Coordinacion = r.ID_Coordinacion
            LEFT JOIN justificacion j ON j.ID = r.ID_Justificacion
            WHERE 1";

            //lógica del filtro
            $param = [];

            if (!empty($filtros['fecha_inicio'])) {//si el select no está 'vacio', hará lo siguiente
                $sql .= " AND F_apertura >= ?";//concatenará está sentencia sql
                $param[] = $filtros['fecha_inicio'];//lo guardará en el array (que se vuelve un array asociativo)
            }
            if (!empty($filtros['fecha_fin'])) {//si el select no está 'vacio', hará lo siguiente
                $sql .= " AND F_apertura <= ?";//concatenará está sentencia sql
                $param[] = $filtros['fecha_fin'];//lo guardará en el array (que se vuelve un array asociativo)
            }
            if (!empty($filtros['justificacion'])) {//si el select no está 'vacio', hará lo siguiente
                $sql .= " AND j.descripcion LIKE ?";// concatenará está sentencia sql
                //LIKE es un operador que se usa en consultas SQL para buscar coincidencias en texto (strings), de forma parcial o con comodines
                //ejemplo: SELECT * FROM usuario WHERE Username LIKE 'juan'
                //solo buscará los usuarios llamados juan
                
                $param[] = '%' . $filtros['justificacion'] . '%';//lo guardará en el array (que se vuelve un array asociativo)

                //El símbolo % es un comodín en SQL que representa cualquier número de caracteres (cero o más). Es parecido a * en otros lenguajes de búsqueda

                //ejemplos: LIKE 'juan%'	Cualquier cosa que empiece con "juan", como "juanito", "juan123", "juana".
                // LIKE '%juan'	    Cualquier cosa que termine en "juan", como "srjuan", "123juan".
                // LIKE '%juan%'	Cualquier cosa que contenga "juan", esté donde esté, como "mi_juan_amigo".
                // LIKE 'j%n'	    Cualquier texto que empiece con j y termine con n, como "juan", "jardin".
            }

            //mismo caso en los siguientes if
            if (!empty($filtros['equipo'])) {
                $sql .= " AND e.Nombre LIKE ?";
                $param[] = '%' . $filtros['equipo'] . '%';
            }
            
            if (!empty($filtros['prioridad'])) {
                $sql .= " AND f.ID_Prioridad = ?";
                $param[] = $filtros['prioridad'];
            }
            
            if (!empty($filtros['supervisor'])) {
                $sql .= " AND per.nombres LIKE ?";
                $param[] = '%' . $filtros['supervisor'] . '%';
            }

            if (isset($filtros['status']) && $filtros['status'] !== '') {
                $sql .= " AND f.Falla_Status = ?";
                $param[] = $filtros['status'];  // Asegúrate de no reescribir $param
            }

            $sql .= " ORDER BY 
                f.Falla_Status DESC,
                CASE f.ID_Prioridad
                WHEN 'A' THEN 1
                WHEN 'B' THEN 2
                WHEN 'C' THEN 3
                ELSE 4
                END ASC,
                f.ID_Falla DESC"; //se ordena por la prioridad de la falla de la falla de manera decresiente 

            $resultado = $this->conn->prepare($sql); //se prepará la sentencia sql
            
            if ($resultado && !empty($param)) { //si la variable $resultado existe y $param no está vacia, hará lo siguiente

                $tipos = str_repeat('s', count($param)); //Esta línea genera una cadena de caracteres que representa los tipos de datos de los parámetros que vas a enlazar en la consulta (bind_param)
                //tipos: será la variable que guardará los valores que cuente el método que iguala
                //str_repeat: repite la cadena un número específico de veces, primero con el count contará cuantos valores tiene esa variable (array) $param, dependiendo de cuantas haya será su delimitador (si hay 3 valores contará hasta 3 y su valor será de 3), y se repetiran las s dependiendo del delimitador

                $resultado->bind_param($tipos, ...$param); //se enlazan los valores de la consulta
            }

            if ($resultado->execute()) {//ejecutamos la sentencia sql
                $result = $resultado->get_result();//obtenemos los resultados obtenidos
                while ($fila = $result->fetch_assoc()) { //obtenemos una fila asociativa
                    
                    //calcular el tiempo de inoperatividad
                    if (!empty($fila['F_apertura']) && !empty($fila['F_cierre'])) {
                    $inicio = new DateTime($fila['F_apertura']);//tomamos el valor de la fecha de apertura
                    $fin = new DateTime($fila['F_cierre']);//tomamos el valor de la fecha de cierre
                    $diff = $inicio->diff($fin);

                    // Puedes formatearlo como días y horas o total de horas
                    $fila['Tiempo_inoperatividad'] = $diff->format('%a días, %h horas, %i minutos');
                    } else {
                        $fila['Tiempo_inoperatividad'] = 'En curso';
                    }           
                    $datos[] = $fila;//guardamos los datos en la variable $datos
                }
            }
            return $datos;
        } catch (Exception $e) {
            throw new Exception("Error en ObtenerFallas: " . $e->getMessage());
        }
    }

    //los selects
    public function obtenerUsuarios() {
        try {
            $sql = "SELECT ID, Username FROM usuario";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerUsuarios: " . $e->getMessage());
        }
    }

    public function obtenerPrioridades() {
        try {
            $sql = "SELECT Codigo FROM prioridad";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerUsuarios: " . $e->getMessage());
        }
    }

    public function obtenerSupervisores() {
        try {
            $sql = "SELECT p.carnet, per.nombres FROM personal p 
            JOIN persona per ON p.carnet = per.carnet
            WHERE p.active = 1 AND gerencia = 'OPERACIONES'";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerSupervisores: " . $e->getMessage());
        }
    }

    public function obtenerEquipos() {
        try {
            $sql = "SELECT ID_Equipos, Nombre FROM equipos
            where active = 1 AND status = 1";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerEquipos: " . $e->getMessage());
        }
    }

    public function obtenerJustificaciones() {
        try {
            $sql = "SELECT ID, descripcion FROM justificacion";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerJustificaciones: " . $e->getMessage());
        }
    }

    public function obtenerStatus() {
        try {
            $sql = "SELECT DISTINCT Falla_Status FROM falla";
            return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error en obtenerStatus: " . $e->getMessage());
        }
    }
}
?>