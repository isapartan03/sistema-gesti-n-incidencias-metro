<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();

if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/TecCont.php?action=mostrar&n=3');
    exit;
}

if (isset($_GET['carnet'])) {
    $id = $_GET['carnet'];
} else {
    echo "No se ha especificado un carnet.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Técnico</title>
    <link rel="stylesheet" href="../../Assets/CSS/Formulario.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
    <style>
  .required {
    color: red;
    font-weight: bold;
  }
</style>
</head>
<body>
<?php 
    include '../../Assets/HTML/headerAdmin.php';
?>
<div class="contenedoor">
    <header>Editar Técnico</header>

    <form method="POST" action="../Controller/TecCont.php" class="form">

        <!-- Campo oculto con el carnet -->
        <input type="hidden" name="carnet" value="<?= $id ?>">

        <div class="input-box">
            <label for="name">Nuevo nombre del Técnico <span class="required"> *</span></label>
            <input type="text" name="name" id="name" value="<?=$tecnicos['nombres']?>" required>
        </div>

        <div class="input-box">
            <label for="lastN">Nuevo apellido del Técnico <span class="required"> *</span></label>
            <input type="text" name="lastN" id="lastN" value="<?=$tecnicos['apellidos']?>" required>
        </div>
<div class="column">
    

        <div class="input-select">
            <label for="codG">Código de Grado <span class="required"> *</span></label><br>
            <select name="codG" id="codG" required>
                <option value="">Seleccione una Opcion</option>
                <?php if (!empty($codGrado)): ?>
                    <?php foreach ($codGrado as $cod): ?>
                        <option value="<?= $cod['ID_Grado'] ?>"
                            <?= ($tecnicos['Nombre_Grado'] == $cod['Nombre_Grado']) ? 'selected' : '' ?>>
                            <?= $cod['Nombre_Grado'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay códigos de grado disponibles</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="input-select">
            <label for="id_coord">Coordinación <span class="required"> *</span></label><br>
            <select name="id_coord" id="id_coord" required>
                <option value="">Seleccione una Opcion</option>
                <?php if (!empty($coordinaciones)): ?>
                    <?php foreach ($coordinaciones as $coord): ?>
                        <option value="<?= $coord['ID_Coordinacion'] ?>"
                            <?= ($tecnicos['id_coord'] == $coord['ID_Coordinacion']) ? 'selected' : '' ?>>
                            <?= ($coord['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay coordinaciones disponibles</option>
                <?php endif; ?>
            </select>
        </div>
</div>

         
        <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="update" value="update">Guardar</button>
        <a href="TecCont.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>
    </form>
</div>

</body>
</html>