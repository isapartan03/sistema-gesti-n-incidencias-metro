<?php require_once"../../utilidades/repositorio.php";
    verifcarSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fallas/Registro</title>
  <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
  <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
  <link rel="stylesheet" type="text/css" href="../../Assets/CSS/pantallaCarga.css">
  <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
<script src="../../Assets/Js/sweetalert2.min.js">  </script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single {
    height: 34px;
    border: 1px solid #bbb !important;
    border-radius: 4px !important;
    padding: 3px 10px;
}

  .required {
    color: red;
    font-weight: bold;
  }

/* Contenedor del dropdown - Corrección */
.select2-container {
    width: 100% !important;
    min-width: 200px !important;
}

/* Dropdown de opciones */
.select2-results__option {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>
</head>


<body>

<?php include '../../Assets/HTML/headerAdmin.php'; ?>

<!-- Pantalla de carga mejorada -->
<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-content">
    <div class="loading-spinner"></div>
    <div class="loading-text">Procesando solicitud, por favor espere...</div>
  </div>
</div>  

<div class="center-wrapper">
  <div class="container">
    <h2>Registro de Fallas</h2>

    <form id="fallaForm" action="../Controller/btn_create_guardar_falla.php" method="post">
      <div class="form-row">
        <!-- Prioridad -->
        <div class="form-group">
          <label for="prioridad">Prioridad<span class="required"> *</span></label>
          <select name="prioridad" id="prioridad" class="form-control" required>
            <option value="">-- Seleccione prioridad --</option>
            <?php foreach ($prioridades as $p): ?>
              <option value="<?= htmlspecialchars($p['Codigo']) ?>">
                <?= htmlspecialchars($p['Codigo']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Equipos -->
        <div class="form-group">
          <label for="equipo">Equipo<span class="required"> *</span></label>
          <select name="equipo" required>
            <option value="">-- Seleccione equipo --</option>
            <?php foreach ($equipos as $e): ?>
              <option value="<?= $e['ID_Equipos'] ?>"><?= $e['Nombre'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Usuario -->
        <div class="form-group">
          <label for="usuario">Usuario<span class="required"> *</span></label>
          <input type="text"  id="usuario" readonly value="<?= $usuario ?>">
          <input type="text" name="usuario" id="usuario" hidden value="<?= $idUser ?>" >
        </div>

        <!-- Supervisor -->
        <div class="form-group">
          <label for="supervisor">Supervisor<span class="required"> *</span></label>
          <select name="supervisor" id="supervisor" required>
            <option value="">-- Seleccione supervisor --</option>
            <?php foreach ($supervisores as $s): ?>
              <option value="<?= htmlspecialchars($s['carnet']) ?>">
                <?= htmlspecialchars($s['nombres']) ?> (<?= htmlspecialchars($s['carnet']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Descripción -->
        <div class="form-group" style="flex: 1 1 100%;">
          <label for="descripcion" required>Descripción<span class="required"> *</span></label>
          <textarea name="descripcion" id="descripcion" rows="4" style="width: 100%; padding: 6px 3px; font-size: 13px; border-radius: var(--btn-radius); border: var(--input-border);" required></textarea>
        </div>
      </div>

      <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
        <a href="controlador_read_falla.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript" src="../../Assets/Js/pantallaCarga.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('select').select2({
        minimumResultsForSearch: Infinity,
        width: 'resolve', // Cambia de '100%' a 'resolve'
        dropdownAutoWidth: false // Desactiva el autoajuste
    });
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Mostrar alertas cuando la página está completamente cargada
    <?php if (!empty($GLOBALS['alertas'])): ?>
        Swal.fire({
            title: '<?= $GLOBALS['alertas']['titulo'] ?>',
            text: '<?= $GLOBALS['alertas']['mesge'] ?>',
            icon: '<?= $GLOBALS['alertas']['icon'] ?>',
            confirmButtonText: 'Entendido',
            didOpen: () => {
                // Eliminar selects no deseados que puedan estar en la alerta
                $('.swal2-container').find('select').remove();
            }
        });
    <?php endif; ?>
});
</script>
</body>
</html>