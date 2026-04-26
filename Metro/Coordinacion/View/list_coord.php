<?php 
require_once"../../utilidades/repositorio.php";
    verifcarSession();
$registrosPorPagina = 5; 
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 
$totalRegistros = count($coordinaciones);
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));

$paginaActual = min($paginaActual, $totalPaginas);
$inicio = ($paginaActual - 1) * $registrosPorPagina;
$coordinacionesPorPagina = array_slice($coordinaciones, $inicio, $registrosPorPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel/Coordinaciones</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
    
</head>
<body>

<?php include '../../Assets/HTML/headerAdmin.php'; ?> 

<?php if (isset($_SESSION['confirm_delete'])): ?>
    <div id="modalConfirmDelete" class="modal">
      <div class="modal-content">
        <p>¿Esta seguro de eliminar esta Coordinación? También se desactivarán <?= $_SESSION['confirm_delete']['cantidadEquipos'] ?> equipo(s).</p>
        <button id="btnConfirmDelete" class="btn btn-danger">Confirmar</button>
        <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
      </div>
    </div>
<?php endif; ?>

<div class="center-wrapper">
  <div class="container">
    <h2>Panel de Coordinaciones</h2>
    <form method="POST" action="../Controller/CoordCont.php">
      <div class="form-row">
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" class="form-control">
        </div>
      </div>
      <div class="botones">
        <button class="btn btn-filtrar" type="submit" name="Buscar" value="Buscar">Filtrar</button>
        <a href="../Controller/CoordCont.php?action=formulario" class="action-btn">Registrar Coordinación</a>
      </div>
    </form>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
           
            <th>Nombre</th>
            <th>Correo</th>
            <th>Responsable</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($coordinacionesPorPagina)): ?>
            <?php foreach ($coordinacionesPorPagina as $coord): ?>
              <tr>
                
                <td><?= htmlspecialchars($coord['Nombre']) ?></td>
                <td><?= htmlspecialchars($coord['correo']) ?></td>
                <td><?= htmlspecialchars($coord['nombres']) ?></td>
                <td>
                  <a href="../Controller/CoordCont.php?action=formularioE&id=<?= $coord['ID_Coordinacion'] ?>" class="btn btn-primary">Editar</a>
                  <a href="../Controller/CoordCont.php?action=delete&id=<?= $coord['ID_Coordinacion'] ?>" class="btn btn-danger">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5">No hay coordinaciones registradas.</td></tr>
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
      Mostrando Coordinaciones <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> 
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
    modal.style.display = 'flex';

    document.getElementById('btnConfirmDelete').addEventListener('click', function() {
      window.location.href = 'CoordCont.php?action=confirm_delete&id=<?= $_SESSION['confirm_delete']['id'] ?>';
    });

    document.getElementById('btnCancelDelete').addEventListener('click', function() {
      modal.style.display = 'none';
      window.location.href = 'CoordCont.php?action=mostrar';
    });
  }
  window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            idAEliminar = null;
        }
    });
});
</script>
<?php
if (isset($_SESSION['confirm_delete'])) {
    unset($_SESSION['confirm_delete']);
}
?>
</body>
</html>
