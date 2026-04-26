<?php require_once"../../utilidades/repositorio.php";
    verifcarSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <title>Falla/Detalles  <?= htmlspecialchars($falla['ID_Falla'] ?? '') ?></title>
   <!-- <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">-->
<link rel="stylesheet" href="../../Assets/CSS/detalleFallasq.css">
    <style>
        /* Estilos adicionales para el nuevo diseño */
        .contenedor-columnas {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .seccion-falla, .seccion-reporte {
            flex: 1;
            min-width: 0;
        }
        
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-contenido {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .cerrar-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .cerrar-modal:hover {
            color: black;
        }
        
        .lista-tecnicos {
            margin-top: 20px;
        }
        
        .lista-tecnicos ul {
            list-style-type: none;
            padding: 0;
        }
        
        .lista-tecnicos li {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .btn-asignar {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .btn-asignar:hover {
            background-color: #45a049;
        }

        .contenedor-falla {
    transform: scale(0.85); /* Reduce todo al 85% */
    transform-origin: top center; /* Mantiene el punto de origen */
}


        .required {
            color: red;
            font-weight: bold;
            margin-left: 5px;
        }
        
    </style>
</head>
<body>
    <?php include '../../Assets/HTML/headerAdmin.php'; ?>
    
    <div class="contenedor-falla">
        <h1>Detalles de la Falla #<?= htmlspecialchars($falla['ID_Falla'] ?? 'N/A') ?></h1>
        
        <div class="contenedor-columnas">
            <!-- Sección Falla -->
            <div class="seccion-falla">
                <h2>Información de la Falla</h2>

                <div class="campo-detalle">
                    <label for="prioridad">Prioridad<span class="required"> *</span></label>
                    <input id="prioridad" type="text" readonly  value="<?= htmlspecialchars(($falla['PrioridadCodigo'] ?? '-')) ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="id_falla">ID Falla<span class="required"> *</span></label>
                    <input id="id_falla" type="text" readonly value="<?= htmlspecialchars($falla['ID_Falla'] ?? 'N/A') ?>">
                </div>

                <div class="campo-detalle">
                    <label for="equipo">Coordinación<span class="required"> *</span></label>
                    <input id="equipo" type="text" readonly value="<?= htmlspecialchars($falla['Coordinacion'] ?? 'No especificado') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="equipo">Equipo<span class="required"> *</span></label>
                    <input id="equipo" type="text" readonly value="<?= htmlspecialchars($falla['NombreEquipo'] ?? 'No especificado') ?>">
                </div>

                <div class="campo-detalle">
                    <label for="usuario">Usuario quien registro la falla<span class="required"> *</span></label>
                    <input id="supervisor" type="text" readonly value="<?= htmlspecialchars($falla['Username'] ?? 'No asignado') ?>">
                </div>

                <div class="campo-detalle">
                    <label for="usuario">Usuario quien finalizo la falla<span class="required"> *</span></label>
                    <input id="supervisor" type="text" readonly value="<?= htmlspecialchars($falla['UsuarioCierre'] ?? 'No asignado') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="supervisor">Supervisor<span class="required"> *</span></label>
                    <input id="supervisor" type="text" readonly value="<?= htmlspecialchars($falla['nombres'] ?? 'No asignado') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="descripcion">Descripción<span class="required"> *</span></label>
                    <textarea id="descripcion" readonly><?= htmlspecialchars($falla['descripcion'] ?? 'Sin descripción') ?></textarea>
                </div>
                
                <div class="campo-detalle">
                    <label for="estado">Estado<span class="required"> *</span></label>
                    <input id="estado" type="text" readonly 
                           value="<?= ($falla['Falla_Status'] ?? 0) == 1 ? 'Abierta' : 'Cerrada' ?>"
                           class="<?= ($falla['Falla_Status'] ?? 0) == 1 ? 'estado-abierta' : 'estado-cerrada' ?>">
                </div>
            </div>
            
            <!-- Sección Reporte -->
            <?php if (!empty($reporte)): ?>
            <div class="seccion-reporte">
                <h2>Reporte Asociado</h2>
                
                <div class="campo-detalle">
                    <label for="ubicacion">Ubicación<span class="required"> *</span></label>
                    <input id="ubicacion" type="text" readonly value="<?= htmlspecialchars($reporte['Ubicacion'] ?? 'No especificada') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="observacion">Observación<span class="required"> *</span></label>
                    <textarea id="observacion" readonly><?= htmlspecialchars($reporte['Observaciones'] ?? 'Sin Observación') ?></textarea>
                </div>

                <div class="campo-detalle">
                    <label for="diagnostico">Diagnóstico<span class="required"> *</span></label>
                    <textarea id="diagnostico" readonly><?= htmlspecialchars($reporte['Diagnostico'] ?? 'Sin diagnóstico') ?></textarea>
                </div>
                

                <div class="campo-detalle">
                    <label for="justificacion">Justificación<span class="required"> *</span></label>
                    <input id="justificacion" type="text" readonly value="<?= htmlspecialchars($reporte['Justificacion'] ?? 'No especificada') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="fecha_apertura">Fecha Apertura<span class="required"> *</span></label>
                    <input id="fecha_apertura" type="text" readonly value="<?= htmlspecialchars($reporte['F_apertura'] ?? 'No registrada') ?>">
                </div>
                
                <div class="campo-detalle">
                    <label for="fecha_cierre">Fecha Cierre<span class="required"> *</span></label>
                    <input id="fecha_cierre" type="text" readonly value="<?= htmlspecialchars($reporte['F_cierre'] ?? 'Sin cerrar') ?>">
                </div>
            </div>
            <?php else: ?>
            <div class="seccion-reporte">
                <h2>Reporte Asociado</h2>
                <div class="sin-reporte">No hay reporte asociado a esta falla.</div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="acciones">
<a href="controlador_read_falla.php" class="btn-ver-tecnicos">Volver al listado</a>

            <?php if (empty($reporte) && ($falla['Falla_Status'] ?? 0) == 1): ?>
                <a href="controlador_create_reporte.php?idFalla=<?= urlencode($falla['ID_Falla'] ?? '') ?>" class="btn-ver-tecnicos">Crear Reporte</a>
            <?php endif; ?>
            <button id="btnVerTecnicos" class="btn-ver-tecnicos">Historial de Diagnosticos</button>
        </div>
    </div>
    
    <!-- Modal para lista de técnicos -->
    <div id="modalTecnicos" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal">&times;</span>
            <h2>Técnicos Disponibles</h2>
            <div class="lista-tecnicos">
                <ul>
                   <?php foreach ($historialTecnicos as $t): ?>
                            <li>
                                <?= htmlspecialchars($t['nombres'] . ' ' . $t['apellidos']) ?> - 
                                <?= htmlspecialchars($t['Fecha']) ?>
                            </li>
                            <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="../../Assets/Js/modalListaTecnicos.js"></script>
</body>
</html>