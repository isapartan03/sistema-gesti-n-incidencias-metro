<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();
$registrosPorPagina = 5; 

// Asegura que la página actual siempre sea al menos 1
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 

// Calcular total de registros
$totalRegistros = count($tecnicos);

// Calcular total de páginas, asegurando mínimo 1 para evitar división por cero o páginas inválidas
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));

// Ajustar página actual si se pasa del total
$paginaActual = min($paginaActual, $totalPaginas);

// Calcular el índice de inicio de los registros
$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Obtener los técnicos para mostrar en la página actual
$tecnicosPorPagina = array_slice($tecnicos, $inicio, $registrosPorPagina);

?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel/Técnicos</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
</head>
<body>
<?php  include '../../Assets/HTML/headerAdmin.php';;  //AQUI DEBE IR EL HEADER DE ADMIN
?> 

<!-- Modal de Confirmación -->
<div id="modalConfirmDelete" class="modal">
    <div class="modal-content">
        <p>¿Está seguro de eliminar este Técnico? Esta acción no se podrá revertir.</p>
        <button id="btnConfirmDelete" class="btn btn-danger">Confirmar</button>
        <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
    </div>
</div>

<div class="center-wrapper">
    <div class="container">
        <h2>Panel de Técnicos</h2>

       

        <!-- FORMULARIO DE FILTRADO -->
        <form method="POST" action="../Controller/TecCont.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="carnet">Carnet:</label>
                    <input type="number" name="carnet" class="form-control">
                </div>
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellido:</label>
                    <input type="text" name="apellido" class="form-control">
                </div>
            </div>

            <div class="botones">
                <button class="btn btn-filtrar" type="submit" name="Buscar" value="Buscar">Filtrar</button>
                <a href="../Controller/TecCont.php?action=formulario" class="action-btn">Registrar Técnico</a>
            </div>
        </form>
<div class="table-wrapper">
        <table >
            <thead>
                <tr>
                    <th>Carnet</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Grado</th>
                    <th>Coordinación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tecnicosPorPagina)): ?>
                    <?php foreach ($tecnicosPorPagina as $tec): ?>
                        <tr>
                            <td><?= htmlspecialchars($tec['carnet']) ?></td>
                            <td><?= htmlspecialchars($tec['nombres']) ?></td>
                            <td><?= htmlspecialchars($tec['apellidos']) ?></td>
                            <td><?= htmlspecialchars($tec['Nombre_Grado']) ?></td>
                            <td><?= htmlspecialchars($tec['Nombre']) ?></td>
                            <td>
                                <a class="btn btn-primary" href="../Controller/TecCont.php?action=forEditar&carnet=<?= urlencode($tec['carnet']) ?>">Editar</a>
                                <button type="button" class="btn btn-danger btn-show-modal" data-id="<?= $tec['carnet'] ?>">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay técnicos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="paginacion" id="pag">
            <!-- Primera página -->
            <?php if ($paginaActual > 1): ?>
                <a href="?action=mostrar&pagina=1" title="Primera página">« Primera</a>
            <?php else: ?>
                <span class="deshabilitado">« Primera</span>
            <?php endif; ?>

            <!-- Página anterior -->
            <?php if ($paginaActual > 1): ?>
                <a href="?action=mostrar&pagina=<?= $paginaActual - 1 ?>" title="Página anterior">‹ Anterior</a>
            <?php else: ?>
                <span class="deshabilitado">‹ Anterior</span>
            <?php endif; ?>

            <!-- Rango de páginas -->
            <?php 
            $inicioRango = max(1, $paginaActual - 2);
            $finRango = min($totalPaginas, $paginaActual + 2);
            
            if ($inicioRango > 1): ?>
                <span>...</span>
            <?php endif;
            
            for ($i = $inicioRango; $i <= $finRango; $i++): ?>
                <?php if ($i == $paginaActual): ?>
                    <span class="pagina-actual"><?= $i ?></span>
                <?php else: ?>
                    <a href="?action=mostrar&pagina=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor;
            
            if ($finRango < $totalPaginas): ?>
                <span>...</span>
            <?php endif; ?>

            <!-- Página siguiente -->
            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="?action=mostrar&pagina=<?= $paginaActual + 1 ?>" title="Página siguiente">Siguiente ›</a>
            <?php else: ?>
                <span class="deshabilitado">Siguiente ›</span>
            <?php endif; ?>

            <!-- Última página -->
            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="?action=mostrar&pagina=<?= $totalPaginas ?>" title="Última página">Última »</a>
            <?php else: ?>
                <span class="deshabilitado">Última »</span>
            <?php endif; ?>
        </div>
        <div class="info-paginacion">
            Mostrando Coordinaciones <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> 
            de un total de <strong><?= $totalRegistros ?></strong> registros
        </div>
    </div>

</div>

<script>
    //Este ecrip permite mostrar la alerta segun los parametros de la variable global alertas
    swal('<?=$GLOBALS['alertas']['titulo']?>','<?=$GLOBALS['alertas']['mesge']?>','<?=$GLOBALS['alertas']['icon']?>',);
</script>
<?php resetGlobals()?>

<!-- MODAL DELETE FUNCIONAL -->
<script type="text/javascript" src="../../Assets/Js/modalTecnicos.js"></script>
</body>
</html>
