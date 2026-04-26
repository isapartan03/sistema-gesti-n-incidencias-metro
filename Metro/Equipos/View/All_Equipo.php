<?php 
require_once"../../utilidades/repositorio.php";
    verifcarSession();
$registrosPorPagina = 5;

// Aseguramos que la página actual sea al menos 1
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual);

// Total de registros disponibles
$totalRegistros = count($resultado);

// Calculamos el total de páginas, asegurándonos que sea al menos 1
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));

// Forzamos que la página actual no se pase del total
$paginaActual = min($paginaActual, $totalPaginas);

// Índice de inicio para array_slice()
$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Resultado segmentado
$equiposPorPagina = array_slice($resultado, $inicio, $registrosPorPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel/Equipos</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
   
</head>
<body>
<?php include '../../Assets/HTML/headerAdmin.php'; ?>

<!-- Modal de Confirmación -->
<div id="modalConfirmDelete" class="modal">
    <div class="modal-content">
        <p>¿Estás seguro de eliminar este equipo? Esta acción no se podrá revertir.</p>
        <button id="btnConfirmDelete" class="btn btn-danger">Confirmar</button>
        <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
    </div>
</div>

<!-- Modal Confirmación de Cambio de Estado -->
<div id="modalCambioEstado" class="modal">
    <div class="modal-content">
        <p id="mensajeCambioEstado">¿Estás seguro de cambiar el estado del equipo?</p>
        <button id="btnConfirmarCambioEstado" class="btn btn-danger">Confirmar</button>
        <button id="btnCancelarCambioEstado" class="btn btn-primary">Cancelar</button>
    </div>
</div>


<div class="center-wrapper">
    <div class="container">
        <h2>Panel de Equipos</h2>

       

        <form method="POST" action="../Controller/EquipoC.php">
            <div class="form-row">
                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" class="form-control">
                </div>

                <!-- Ambiente -->
                <div class="form-group">
                    <label for="ambiente">N° Ambiente:</label>
                    <input type="text" name="ambiente" class="form-control">
                </div>

                <!-- Coordinación -->
                <div class="form-group">
                    <label for="id_coord">Buscar por Coordinación:</label>
                    <select name="id_coord" class="form-control">
                        <option value="" selected>Seleccione una coordinación</option>
                        <?php if (!empty($coordinaciones)): ?>
                            <?php foreach ($coordinaciones as $coord): ?>
                                <option value="<?= $coord['ID_Coordinacion'] ?>"><?= htmlspecialchars($coord['Nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay coordinaciones disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Estación -->
                <div class="form-group">
                    <label for="id_estacion">Buscar por Estación:</label>
                    <select name="id_estacion" class="form-control">
                        <option value="" selected>Seleccione una estación</option>
                        <?php if (!empty($estacion)): ?>
                            <?php foreach ($estacion as $estac): ?>
                                <option value="<?= $estac['ID_Estacion'] ?>"><?= htmlspecialchars($estac['Nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay estaciones disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" class="form-control">
                        <option value="">Seleccione una opción</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="botones">
                <button class="btn btn-filtrar" type="submit" name="search" value="search">Filtrar</button>
                <a href="../Controller/EquipoC.php?action=formulario" class="action-btn">Registrar Equipo</a>
            </div>
        </form>

        <!-- TABLA -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Serial / Código</th>
                        <th>Nombre</th>
                        <th>N° Ambiente</th>
                        <th>Estación</th>
                        <th>Coordinación</th>
                        <th>Status</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equiposPorPagina)): ?>
                        <?php foreach ($equiposPorPagina as $equipo): ?>
                            <tr>
                                <td><?= htmlspecialchars($equipo['ID_Equipos']) ?></td>
                                <td><?= htmlspecialchars($equipo['Nombre']) ?></td>
                                <td><?= htmlspecialchars($equipo['N_Ambiente']) ?></td>
                                <td><?= htmlspecialchars($equipo['EstacionNombre']) ?></td>
                                <td><?= htmlspecialchars($equipo['CoordinacionNombre']) ?></td>
                                <td><?= $equipo['Status'] == 1 ? 'Activo' : 'Inactivo' ?></td>
<td>
    <a href="../Controller/EquipoC.php?action=edition&id=<?= urlencode($equipo['ID_Equipos']) ?>" class="btn btn-primary">Editar</a>
    <button type="button" class="btn btn-danger btn-show-modal" data-id="<?= $equipo['ID_Equipos'] ?>">Eliminar</button>

    <?php if ($equipo['Status'] == 1): ?>
        <button type="button" class="btn btn-warning btn-cambiar-estado" data-id="<?= $equipo['ID_Equipos'] ?>" data-action="desincorporar">Desincorporar</button>
    <?php else: ?>
        <button type="button" class="btn btn-success btn-cambiar-estado" data-id="<?= $equipo['ID_Equipos'] ?>" data-action="incorporar">Incorporar</button>
    <?php endif; ?>
</td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No hay equipos registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="paginacion" id="pag">
            <?php if ($paginaActual > 1): ?>
                <a href="?action=mostrar&pagina=1">« Primera</a>
                <a href="?action=mostrar&pagina=<?= $paginaActual - 1 ?>">‹ Anterior</a>
            <?php else: ?>
                <span class="deshabilitado">« Primera</span>
                <span class="deshabilitado">‹ Anterior</span>
            <?php endif; ?>

            <?php 
            $inicioRango = max(1, $paginaActual - 2);
            $finRango = min($totalPaginas, $paginaActual + 2);
            if ($inicioRango > 1) echo "<span>...</span>";
            for ($i = $inicioRango; $i <= $finRango; $i++): ?>
                <?php if ($i == $paginaActual): ?>
                    <span class="pagina-actual"><?= $i ?></span>
                <?php else: ?>
                    <a href="?action=mostrar&pagina=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor;
            if ($finRango < $totalPaginas) echo "<span>...</span>";
            ?>

            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="?action=mostrar&pagina=<?= $paginaActual + 1 ?>">Siguiente ›</a>
                <a href="?action=mostrar&pagina=<?= $totalPaginas ?>">Última »</a>
            <?php else: ?>
                <span class="deshabilitado">Siguiente ›</span>
                <span class="deshabilitado">Última »</span>
            <?php endif; ?>
        </div>
        <div class="info-paginacion">
            Mostrando equipos <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> de un total de <strong><?= $totalRegistros ?></strong> registros
        </div>
    </div>
</div>



<!-- ALERTA GLOBAL -->
<?php if (!empty($GLOBALS['alertas'])): ?>
<script>
swal('<?= $GLOBALS['alertas']['titulo'] ?>', '<?= $GLOBALS['alertas']['mesge'] ?>', '<?= $GLOBALS['alertas']['icon'] ?>');
</script>

<?php resetGlobals(); endif; ?>

<!-- MODAL ELIMINAR FUNCIONAL -->
<script type="text/javascript" src="../../Assets/Js/modalEquipos.js"></script>

</body>
</html>
