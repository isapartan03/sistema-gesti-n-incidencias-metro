<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();
$registrosPorPagina = 5; 
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 

$totalRegistros = count($coordinadores);
$totalPaginas   = max(1, ceil($totalRegistros / $registrosPorPagina)); // corrección

$paginaActual   = min($paginaActual, $totalPaginas);
$inicio         = ($paginaActual - 1) * $registrosPorPagina;

$coordinadoresPorPagina = array_slice($coordinadores, $inicio, $registrosPorPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel/Personal</title>
  <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
     <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">

    <script src="../../Assets/Js/sweetalert2.min.js"></script>
</head>
<body>

<?php  include '../../Assets/HTML/headerAdmin.php'; //AQUI DEBE IR EL HEADER DE ADMIN ?> 

<!-- Modal de Confirmación -->
<div id="modalConfirmDelete" class="modal">
    <div class="modal-content">
        <p>¿Está seguro de eliminar este Coordinador? Esta acción no se podrá revertir.</p>
        <button id="btnConfirmDelete" class="btn btn-danger">Confirmar</button>
        <button id="btnCancelDelete" class="btn btn-primary">Cancelar</button>
    </div>
</div>

<div class="center-wrapper">
  <div class="container">

    <h2>Panel del Personal</h2>

   
    <!-- FORMULARIO DE FILTRADO -->
    <form method="POST" action="../Controller/CoorController.php">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Nombre:</label>
          <input type="text" name="name" class="form-control">
        </div>
        <div class="form-group">
          <label for="carnet">Carnet:</label>
          <input type="text" name="carnet" class="form-control">
        </div>
        <div class="form-group">
          <label for="gerencia">Gerencia:</label>
          <input type="text" name="gerencia" class="form-control">
        </div>
      </div>

      <!-- BOTONES -->
      <div class="botones">
        <button class="btn btn-filtrar" type="submit" name="Buscar" value="Buscar">Filtrar</button>
        <a href="../Controller/CoorController.php?action=formulario" class="action-btn">Registrar Personal</a>
      </div>
    </form>

    <!-- TABLA DE RESULTADOS -->
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Carnet</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Grado</th>
            <th>Gerencia</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($coordinadoresPorPagina)): ?>
            <?php foreach ($coordinadoresPorPagina as $coord): ?>
              <tr>
                <td><?= htmlspecialchars($coord['carnet']) ?></td>
                <td><?= htmlspecialchars($coord['nombres']) ?></td>
                <td><?= htmlspecialchars($coord['apellidos']) ?></td>
                <td><?= htmlspecialchars($coord['Nombre_Grado']) ?></td>
                <td><?= htmlspecialchars($coord['gerencia']) ?></td>
                <td>
                  <a href="../Controller/CoorController.php?action=formularioEdi&carnet=<?= urlencode($coord['carnet']) ?>" class="btn btn-primary">Editar</a>
                  <button type="button" class="btn btn-danger btn-show-modal" data-id="<?= $coord['carnet'] ?>">Eliminar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6">No hay coordinadores registrados.</td></tr>
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
            Mostrando coordinadores <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> 
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
<script type="text/javascript" src="../../Assets/Js/modalCordinadores.js"></script>
</body>
</html>
