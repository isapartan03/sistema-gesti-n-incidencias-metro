<?php 
require_once"../../utilidades/repositorio.php";
    verifcarSession();
$registrosPorPagina = 5; 

$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 

$totalRegistros = count($estacion);
$totalPaginas   = max(1, ceil($totalRegistros / $registrosPorPagina)); 

$paginaActual   = min($paginaActual, $totalPaginas);
$inicio         = ($paginaActual - 1) * $registrosPorPagina;

$estacionPorPagina = array_slice($estacion, $inicio, $registrosPorPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel/Estaciones</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
</head>
<body>
<?php include '../../Assets/HTML/headerAdmin.php'; 
?>  

<?php if (isset($_SESSION['confirm_delete'])): ?>
    <div id="modalConfirmDelete" class="modal">
        <div class="modal-content">
            <p>¿Estás seguro de eliminar esta estación? También se desactivarán <?= $_SESSION['confirm_delete']['cantidadEquipos'] ?> equipo(s).</p>
            <button id="btnConfirmDelete" class="btn btn-danger">Confirmar</button>
            <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
        </div>
    </div>
<?php endif; ?>

<div class="center-wrapper">
    <div class="container">
        <h2>Panel de Estaciones</h2>

        

        <form method="POST" action="../Controller/estacionC.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="id">Nro Estacion:</label>
                    <input type="text" name="id" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" class="form-control">
                </div>
            </div>
            <div class="botones">
                <button class="btn btn-filtrar" type="submit" name="Buscar" value="Buscar">Filtrar</button>
                <a href="../View/crear_estacion.php" class="action-btn">Registrar Estación</a>
            </div>
        </form>

        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nro Estación</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estacionPorPagina)): ?>
                    <?php foreach ($estacionPorPagina as $est): ?>
                        <tr>
                            <td><?= htmlspecialchars($est['ID_Estacion']) ?></td>
                            <td><?= htmlspecialchars($est['Nombre']) ?></td>
                            <td>
                                <a class="btn btn-primary" href="../Controller/estacionC.php?action=edition&id=<?= urlencode($est['ID_Estacion']) ?>">Editar</a>
                                <a class="btn btn-danger" href="../Controller/estacionC.php?action=delete&id=<?= urlencode($est['ID_Estacion']) ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No hay estaciones registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>

        <div class="paginacion" id="pag">
        <?php if ($paginaActual > 1): ?>
            <a href="?action=mostrar&pagina=1">« Primera</a>
        <?php else: ?>
            <span class="deshabilitado">« Primera</span>
        <?php endif; ?>

        <?php if ($paginaActual > 1): ?>
            <a href="?action=mostrar&pagina=<?= $paginaActual - 1 ?>">‹ Anterior</a>
        <?php else: ?>
            <span class="deshabilitado">‹ Anterior</span>
        <?php endif; ?>

        <?php 
            $inicioRango = max(1, $paginaActual - 2);
            $finRango = min($totalPaginas, $paginaActual + 2);
            if ($inicioRango > 1) echo '<span>...</span>';
            for ($i = $inicioRango; $i <= $finRango; $i++): 
        ?>
            <?php if ($i == $paginaActual): ?>
                <span class="pagina-actual"><?= $i ?></span>
            <?php else: ?>
                <a href="?action=mostrar&pagina=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; if ($finRango < $totalPaginas) echo '<span>...</span>'; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?action=mostrar&pagina=<?= $paginaActual + 1 ?>">Siguiente ›</a>
        <?php else: ?>
            <span class="deshabilitado">Siguiente ›</span>
        <?php endif; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?action=mostrar&pagina=<?= $totalPaginas ?>">Última »</a>
        <?php else: ?>
            <span class="deshabilitado">Última »</span>
        <?php endif; ?>
        </div>

        <div class="info-paginacion">
            Mostrando Estaciones <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> 
            de un total de <strong><?= $totalRegistros ?></strong> registros
        </div>
            <script>
      swal('<?= $GLOBALS['alertas']['titulo'] ?>','<?= $GLOBALS['alertas']['mesge'] ?>','<?= $GLOBALS['alertas']['icon'] ?>');
    </script>
    <?php resetGlobals() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalConfirmDelete');
    
    if (modal) {
        // Mostrar modal si existe
        modal.style.display = 'flex';
        
        // Confirmar eliminación
        document.getElementById('btnConfirmDelete').addEventListener('click', function() {
            const id = '<?= $_SESSION['confirm_delete']['id'] ?? '' ?>';
            if (id) {
                window.location.href = '../Controller/estacionC.php?action=confirm_delete&id=' + id;
            }
        });
        
        // Cancelar eliminación
        document.getElementById('btnCancelDelete').addEventListener('click', function() {
            // Ocultar modal
            modal.style.display = 'none';
            // Redirigir sin parámetros de eliminación
            window.location.href = '../Controller/estacionC.php?action=mostrar';
        });
        
        // Cerrar modal haciendo clic fuera
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                window.location.href = '../Controller/estacionC.php?action=mostrar';
            }
        });
    }
});
</script>

<?php
if (isset($_SESSION['confirm_delete'])) {
    unset($_SESSION['confirm_delete']);
}
?>
</body>
</html>
