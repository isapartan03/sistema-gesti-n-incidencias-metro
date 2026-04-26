<?php 
require_once"../../utilidades/repositorio.php";
    verifcarSession();
 
$registrosPorPagina = 5; 
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 
// Calcular total de páginas
$totalRegistros = count($fallas);
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));

// Ajustar página actual si es mayor que el total
$paginaActual = min($paginaActual, $totalPaginas);
// Obtener los registros para la página actual
$inicio = ($paginaActual - 1) * $registrosPorPagina;
$fallasPorPagina = array_slice($fallas, $inicio, $registrosPorPagina);
$mostrandoDesde = $totalRegistros > 0 ? $inicio + 1 : 0;
$mostrandoHasta = $totalRegistros > 0 ? min($inicio + $registrosPorPagina, $totalRegistros) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel/Fallas</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
   
<script src="../../Assets/Js/sweetalert2.min.js"></script>

<style>
.select2-container--default .select2-selection--single {
    height: 34px;
    border: 1px solid #bbb !important;
    border-radius: 4px !important;
    padding: 3px 10px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-results__options {
    max-height: 200px;
    overflow-y: auto !important;
}
</style>
</head>
<body>

<?php include '../../Assets/HTML/headerAdmin.php'; ?>  


<div id="modalConfirmDelete" class="modal">
    <div class="modal-content">
        <p>¿Estás seguro que deseas cerrar esta falla? 
        Esta acción no se podrá revertir.</p>
        <button id="btnConfirmDelete" class="btn btn-warning">Confirmar</button>
        <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
    </div>
</div>

<div class="center-wrapper">
    <div class="container">
        <h2>Panel de Fallas</h2>

        <!-- Formulario de filtros -->
        <form method="get" action="controlador_read_falla.php">
<!-- Primer form-row: fecha inicio, fecha fin, status -->
<div class="form-row">
    <div class="form-group">
        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="<?=htmlspecialchars($_GET['fecha_inicio'] ?? '')?>">
    </div>
    <div class="form-group">
        <label>Fecha fin</label>
        <input type="date" name="fecha_fin" value="<?=htmlspecialchars($_GET['fecha_fin'] ?? '')?>">
    </div>

    <div class="form-group" style="width: 100%;">
        <label>Status</label>
        <select name="status" style="width: 100%;">
            <option value="">Todas</option>
            <?php foreach ($status as $s): ?>
                <option value="<?= $s['Falla_Status'] ?>"
                    <?= (($_GET['status'] ?? '') === (string)$s['Falla_Status']) ? 'selected' : '' ?>>
                    <?= $s['Falla_Status'] == 1 ? 'Activo' : 'Finalizado' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


<!-- Segundo form-row: Prioridad, Justificación -->
<div class="form-row">
    <div class="form-group" style="width: 100%;">
        <label>Prioridad</label>
        <select name="prioridad" style="width: 100%;">
            <option value="">Todas</option>
            <?php foreach ($prioridades as $p): ?>
                <option value="<?=htmlspecialchars($p['Codigo'])?>" <?= (($_GET['prioridad'] ?? '') === $p['Codigo']) ? 'selected':''?>>
                    <?=htmlspecialchars($p['Codigo'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="width: 100%;">
        <label>Justificación</label>
        <select name="justificacion" style="width: 100%;">
            <option value="">Todas</option>
            <?php foreach ($justificaciones as $j): ?>
                <option value="<?=htmlspecialchars($j['descripcion'])?>" <?= (($_GET['justificacion'] ?? '') === $j['descripcion']) ? 'selected':''?>>
                    <?=htmlspecialchars($j['descripcion'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Tercer form-row: Equipo, Supervisor -->
<div class="form-row">
    <div class="form-group" style="width: 100%;">
        <label>Equipo</label>
        <select name="equipo" style="width: 100%;">
            <option value="">Todos</option>
            <?php foreach ($equipos as $e): ?>
                <option value="<?=htmlspecialchars($e['Nombre'])?>" <?= (($_GET['equipo'] ?? '') === $e['Nombre']) ? 'selected':''?>>
                    <?=htmlspecialchars($e['Nombre'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="width: 100%;">
        <label>Supervisor</label>
        <select name="supervisor" style="width: 100%;">
            <option value="">Todos</option>
            <?php foreach ($supervisores as $s): ?>
                <option value="<?=htmlspecialchars($s['nombres'])?>" <?= (($_GET['supervisor'] ?? '') === $s['nombres']) ? 'selected':''?>>
                    <?=htmlspecialchars($s['nombres'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
            <div class="botones">
    <div class="grupo-izquierdo">
        <button type="submit" name="accion" value="filtrar" class="btn btn-filtrar">Filtrar</button>
        <button type="submit" name="accion" value="limpiar" class="btn btn-limpiar">Limpiar Filtros</button>
    </div>
    
    <button type="submit" name="accion" value="exportar" class="btn btn-exportar">Exportar PDF</button>
    
    <button type="button" class="btn btn-registrar" onclick="location.href='controlador_create_falla.php'">Registrar Nueva Falla</button>
</div>
        </form>

        <!-- Tabla de resultados -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Prioridad</th>
                        <th>Nro de Falla</th>
                        <th>Supervisor</th>
                        <th>Nombre del Equipo</th>
                        <th>Coordinación</th>
                        <th>Status de Falla</th>
                        <th>Tiempo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fallasPorPagina)): ?>
                        <?php foreach ($fallasPorPagina as $f): ?>
                            <tr>
                                <td><?= htmlspecialchars($f['Prioridad'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($f['ID_Falla'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($f['nombres']) ?></td>
                                <td><?= htmlspecialchars($f['Nombre']) ?></td>
                                <td><?= htmlspecialchars($f['Coordinacion'] ?? '—') ?></td>
                                <td>
                                    <span class="estatus <?= $f['Falla_Status'] ? 'activo' : 'inactivo' ?>">
                                      <?= $f['Falla_Status'] ? 'Activo' : 'Finalizado' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($f['Tiempo_inoperatividad']) ?></td>
                                <td class="acciones">
                                    <?php if (!empty($f['ID_reporte']) && $f['Falla_Status'] == 1): ?>
                                            <a href="controlador_update_falla.php?id=<?= $f['ID_Falla'] ?>" class="btn btn-sm btn-primary">Cambiar Diagnóstico</a>
                                        <?php endif; ?>

                                    <a href="../Controller/controlador_detalle_falla.php?id=<?= urlencode($f['ID_Falla']) ?>"class="btn btn-info btn-sm">Ver detalles</a>

                                    <?php if (!empty($f['ID_reporte'])): ?>
                                        <?php if (!empty($f['ID_reporte']) && $f['Falla_Status'] == 1): ?>
                                            <a href="../Controller/controlador_delete_falla.php?id=<?= $f['ID_Falla'] ?>" class="btn btn-danger btn-sm btn-finalizar">Finalizar</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if (empty($f['ID_reporte'])): ?>
                                        <a href="controlador_create_reporte.php?idFalla=<?= $f['ID_Falla'] ?>" class="btn btn-sm btn-warning">Diagnosticar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14" class="text-center">No hay fallas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="paginacion" id="pag">

            <?php
// Construir la cadena base de la URL con los filtros actuales, excepto "pagina"
    $parametros = $_GET;
    unset($parametros['pagina']);
    $queryBase = http_build_query($parametros);
    ?>

    <div class="paginacion" id="pag">
        <!-- Primera página -->
        <?php if ($paginaActual > 1): ?>
            <a href="?<?= $queryBase ?>&pagina=1" title="Primera página">« Primera</a>
        <?php else: ?>
            <span class="deshabilitado">« Primera</span>
        <?php endif; ?>

    <!-- Página anterior -->
        <?php if ($paginaActual > 1): ?>
            <a href="?<?= $queryBase ?>&pagina=<?= $paginaActual - 1 ?>" title="Página anterior">‹ Anterior</a>
        <?php else: ?>
            <span class="deshabilitado">‹ Anterior</span>
        <?php endif; ?>

    <!-- Rango de páginas -->
        <?php 
            $inicioRango = max(1, $paginaActual - 2);
            $finRango = min($totalPaginas, $paginaActual + 2);

            if ($inicioRango > 1): ?>
                <span>...</span>
            <?php endif; ?>

    <?php for ($i = $inicioRango; $i <= $finRango; $i++): ?>
        <?php if ($i == $paginaActual): ?>
            <span class="pagina-actual"><?= $i ?></span>
        <?php else: ?>
            <a href="?<?= $queryBase ?>&pagina=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($finRango < $totalPaginas): ?>
        <span>...</span>
    <?php endif; ?>

    <!-- Página siguiente -->
    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?<?= $queryBase ?>&pagina=<?= $paginaActual + 1 ?>" title="Página siguiente">Siguiente ›</a>
    <?php else: ?>
        <span class="deshabilitado">Siguiente ›</span>
    <?php endif; ?>

    <!-- Última página -->
    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?<?= $queryBase ?>&pagina=<?= $totalPaginas ?>" title="Última página">Última »</a>
    <?php else: ?>
        <span class="deshabilitado">Última »</span>
    <?php endif; ?>
</div>

      </div>
    <div class="info-paginacion">
  <?php if ($totalRegistros > 0): ?>
    Mostrando Fallas <strong><?= $mostrandoDesde ?></strong>
    a <strong><?= $mostrandoHasta ?></strong>
    de un total de <strong><?= $totalRegistros ?></strong>
    registros
  <?php else: ?>
    No hay registros para mostrar
  <?php endif; ?>
</div>
<script>
    //Este ecrip permite mostrar la alerta segun los parametros de la variable global alertas
    swal('<?=$GLOBALS['alertas']['titulo']?>','<?=$GLOBALS['alertas']['mesge']?>','<?=$GLOBALS['alertas']['icon']?>',);
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="../../Assets/Js/modalFinalizarFalla.js"></script>
<script>
$(document).ready(function() {
    $('select').select2({
        minimumResultsForSearch: Infinity,
        dropdownAutoWidth: true,
        width: '100%'
    });
});


</script >

<?php resetGlobals();?>

</body>
</html>