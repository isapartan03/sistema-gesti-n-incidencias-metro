<?php
  require_once"../../utilidades/repositorio.php";
    verifcarSession();
function buscarNombre($id, $lista, $campoID, $campoNombre) {
    foreach ($lista as $item) {
        if ($item[$campoID] == $id) {
            return $item[$campoNombre];
        }
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
  <title>Cambiar Diagnostico/Observación</title>
  <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
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
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-results__options {
    max-height: 200px;
    overflow-y: auto !important;
}

    label > .required {
      color: red;
      font-weight: bold;
      margin-left: 5px;
    }
</style>

<style>
  /* SOLO ESTOS ESTILOS PARA EL TEXTAREA - MANTIENE TODO LO DEMÁS IGUAL */
  textarea[name="observacion"], 
  textarea[name="diagnostico"] {
    width: 100% !important;
    min-height: 80px !important;  /* Altura mínima reducida */
    max-height: 200px !important;  /* Altura máxima controlada */
    resize: vertical !important;   /* Permite ajuste vertical */
    padding: 8px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
    box-sizing: border-box !important;
    overflow-y: auto !important;  /* Scroll interno si el contenido excede */
  }
</style>
</head>
<body>

<?php include '../../Assets/HTML/headerAdmin.php'; ?>  

<div class="center-wrapper">
  <div class="container" id="editar-falla-container">
    <h2 class="mb-4">Cambiar Diagnostico/Observación</h2>

    <form id="formulario-principal" action="../Controller/btn_update_guardar.php" method="post">
      <input type="hidden" name="id" value="<?= $falla['ID_Falla'] ?>">

      <div class="form-row" >
        <div class="form-group">
    <label>Equipo<span class="required"> *</span></label>
    <input type="text" class="disabled-input" 
           value="<?= htmlspecialchars($falla['NombreEquipo'] ?? '') ?>" disabled>
    <input type="hidden" name="equipo" value="<?= $falla['ID_Equipos'] ?>">
</div>

        <div class="form-group">
          <label>Usuario<span class="required"> *</span></label>
          <input type="text" class="disabled-input" 
                 value="<?= buscarNombre($falla['ID_Usuario'], $usuarios, 'ID', 'Username') ?>" disabled>
          <input type="hidden" name="usuario" value="<?= $falla['ID_Usuario'] ?>">
        </div>

        <div class="form-group">
          <label>Supervisor<span class="required"> *</span></label>
          <input type="text" class="disabled-input" 
                 value="<?= buscarNombre($falla['ID_Personal'], $supervisores, 'carnet', 'nombres') ?>" disabled>
          <input type="hidden" name="supervisor" value="<?= $falla['ID_Personal'] ?>">
        </div>

        
      <?php if (!empty($reporte)): ?>
        <hr>
        <h4>Datos del Reporte Asociado</h4>
        <input type="hidden" name="id_reporte" value="<?= htmlspecialchars($reporte['ID_reporte']) ?>">

        <div class="form-row">
          <div class="form-group">
            <label>Coordinación<span class="required"> *</span></label>
            <input type="text" class="disabled-input" 
                   value="<?= buscarNombre($reporte['ID_Coordinacion'], $coordinaciones, 'ID_Coordinacion', 'Nombre') ?>" disabled>
            <input type="hidden" name="id_coordinacion" value="<?= $reporte['ID_Coordinacion'] ?>">
          </div>

          <div class="form-group">
            <label>Justificación<span class="required"> *</span></label>
            <input type="text" class="disabled-input" 
                   value="<?= buscarNombre($reporte['ID_Justificacion'], $justificaciones, 'ID', 'descripcion') ?>" disabled>
            <input type="hidden" name="id_justificacion" value="<?= $reporte['ID_Justificacion'] ?>">
          </div>

          <div class="form-group">
            <label>Ubicación<span class="required"> *</span></label>
            <input type="text" class="disabled-input" 
                   value="<?= htmlspecialchars($reporte['Ubicacion']) ?>" disabled>
            <input type="hidden" name="ubicacion" value="<?= htmlspecialchars($reporte['Ubicacion']) ?>">
          </div>

          <!-- Observaciones -->
          <div class="form-group" style="flex: 1 1 100%; margin-bottom: 10px;">
            <label>Observaciones<span class="required"> *</span></label>
            <textarea name="observacion" class="editable-textarea" rows="3"><?= htmlspecialchars($reporte['Observaciones']) ?></textarea>
          </div>

            <!-- Diagnóstico -->
            <div class="form-group" style="flex: 1 1 100%; margin-bottom: 10px;">
              <label>Diagnóstico<span class="required"> *</span></label>
              <textarea name="diagnostico" class="editable-textarea" rows="4"><?= htmlspecialchars($reporte['Diagnostico']) ?></textarea>
            </div>

          <div class="form-group" style="flex: 1 1 100%;">
            <label>Técnico que diagnostica (nuevo)<span class="required"> *</span></label>
            <select name="carnet_tecnico" required>
              <option value="">Seleccione un técnico</option>
              <?php foreach ($tecnicos as $tec): ?>
                <option value="<?= htmlspecialchars($tec['carnet']) ?>">
                  <?= htmlspecialchars($tec['nombres'] . ' ' . $tec['apellidos']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

      <?php endif; ?>

      <div class="form-row botones">
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="controlador_read_falla.php" class="btn btn-warning">Volver</a>
      </div>

    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#formulario-principal select').select2({
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
<script>
// Elimina específicamente el select del modal de alerta
function limpiarAlertas() {
    // Espera a que exista el modal
    const checkModal = setInterval(() => {
        const modal = document.querySelector('.swal2-container, .alert-modal');
        if (modal) {
            // Destruye cualquier Select2 en el modal
            $(modal).find('select').select2('destroy').remove();
            clearInterval(checkModal);
        }
    }, 100);
}

// Ejecuta cuando haya cambios en la URL (como las redirecciones con ?n=0)
window.addEventListener('popstate', limpiarAlertas);
window.addEventListener('load', limpiarAlertas);

// También verifica cada segundo por si acaso
setInterval(limpiarAlertas, 1000);
</script>
</body>
</html>