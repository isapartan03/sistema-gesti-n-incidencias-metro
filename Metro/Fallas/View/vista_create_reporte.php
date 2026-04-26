<?php require_once"../../utilidades/repositorio.php";
    verifcarSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fallas/Reporte</title>
  <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
  <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
  <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
  <script src="../../Assets/Js/sweetalert2.min.js">  </script>
  <link rel="stylesheet" type="text/css" href="../../Assets/CSS/modal.css">
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0; 
      top: 0;
      width: 100%; 
      height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 1.5rem;
      border-radius: 8px;
      text-align: center;
      width: 350px;
    }

    .confirm-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
    }
   
    label > .required {
      color: red;
      font-weight: bold;
      margin-left: 4px;
    }

    /* Asegurar que el modal esté por encima de todo */
    .modal {
      z-index: 10000;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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

<div class="center-wrapper">
  <div class="container">
    <h2>Crear Reporte para Falla #<?= htmlspecialchars($idFalla) ?></h2>

    <form id="reporteForm" action="../Controller/btn_create_guardar_reporte.php" method="post">

      <!-- Inputs ocultos -->
      <input type="hidden" name="falla" value="<?= htmlspecialchars($idFalla) ?>">
      <input type="hidden" name="coordinacion" value="<?= htmlspecialchars($idcoordinacion) ?>">
      <input type="hidden" name="ubicacion" value="<?= htmlspecialchars($ubicacion) ?>">
      <input type="hidden" name="fecha" value="<?= htmlspecialchars($fechaActual) ?>">

      <div class="form-row">

        <!-- Coordinación -->
        <div class="form-group">
          <label>Coordinación<span class="required"> *</span></label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($Coordinacion) ?>" disabled>
        </div>

        <!-- Justificación -->
        <div class="form-group">
          <label for="justificacion">Justificación<span class="required"> *</span></label>
          <select name="justificacion" id="justificacion" class="form-control" required>
            <option value="">-- Seleccione justificacion --</option>
            <?php foreach ($justificaciones as $j): ?>
              <option value="<?= htmlspecialchars($j['ID']) ?>">
                <?= htmlspecialchars($j['descripcion']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Ubicación -->
        <div class="form-group">
          <label>Ambiente/Estación<span class="required"> *</span></label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($ubicacion) ?>" disabled>
        </div>

        <!-- Técnico -->
        <div class="form-group">
          <label for="tecnico_diagnostico">Técnico que diagnostica<span class="required"> *</span></label>
          <select name="carnet_tecnico" class="form-control" required>
            <option value="">-- Seleccione técnico --</option>
            <?php foreach ($tecnicos as $tecnico): ?>
            <option value="<?= htmlspecialchars($tecnico['carnet']) ?>">
              <?= htmlspecialchars($tecnico['nombres'] . ' ' . $tecnico['apellidos']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Observaciones -->
        <div class="form-group" style="flex: 1 1 100%;">
          <label for="observacion">Observaciones<span class="required"> *</span></label>
          <textarea name="observacion" id="observacion" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Diagnóstico -->
        <div class="form-group" style="flex: 1 1 100%;">
          <label for="diagnostico">Diagnóstico<span class="required"> *</span></label>
          <textarea name="diagnostico" id="diagnostico" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Fecha apertura -->
        <div class="form-group">
          <label>Fecha de apertura<span class="required"> *</span></label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($fechaActual) ?>" disabled>
        </div>

      </div> <!-- .form-row -->

      <!-- Botón de envío -->
      <div class="form-row">
        <button type="button" id="submitBtn" class="btn btn-success">Guardar Reporte</button>
        <a href="controlador_read_falla.php?action=mostrar" class="btn btn-sm btn-warning">
          <span class="btn-text">Volver</span>
        </a>
      </div>

    </form>
  </div>
</div>

<!-- Modal de confirmación -->
<div id="customConfirm" class="modal">
  <div class="modal-content">
    <p>¿Está seguro que desea guardar este reporte?</p>
    <div class="confirm-buttons">
      <button type="button" id="confirmCancel" class="btn btn-primary">Cancelar</button>
      <button type="button" id="confirmSubmit" class="btn btn-warning">Guardar</button>
    </div>
  </div>
</div>

<script type="text/javascript" src="../../Assets/Js/modalConfirmacionReporte.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('select').select2({
        minimumResultsForSearch: Infinity,
        dropdownAutoWidth: true,
        width: '100%'
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