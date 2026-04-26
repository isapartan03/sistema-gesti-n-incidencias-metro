<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();

if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/CoorController.php?action=mostrar&n=3');
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
    <title>Personal/Editar</title>
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
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
         include '../../Assets/HTML/headerAdmin.php'; //AQUI DEBE IR EL HEADER DE ADMIN
?>

<div class="contenedoor">
    <header>Editar Personal</header>

    <form method="POST" action="../Controller/CoorController.php" class="form">

        <input type="hidden" name="carnet" value="<?= $id ?>">

        <div class="input-box">
            <label for="name">Nuevo nombre del Personal <span class="required"> *</span></label>
            <input type="text" name="name" id="name" value="<?=$coordinador['nombres']?>" required>
        </div>

        <div class="input-box">
            <label for="lastN">Nuevo apellido del Personal <span class="required"> *</span></label>
            <input type="text" name="lastN" id="lastN" value="<?=$coordinador['apellidos']?>" required>
        </div>
<div class="column">
        <div class=" input-select">
            <label for="codG">Nuevo Grado <span class="required"> *</span></label><br>
            <select name="codG" id="codG" required>
                <option value="">Seleccione un Código de Grado</option>
                <option value="5" 
            <?= (isset($coordinador['Nombre_Grado']) && $coordinador['Nombre_Grado'] == 'Coordinador') ? 'selected' : 'selected' ?>>
            Coordinador
        </option>
        <option value="4"
            <?= (isset($coordinador['Nombre_Grado']) && $coordinador['Nombre_Grado'] == 'Supervisor') ? 'selected' : '' ?>>
            Supervisor
        </option>
            </select>
        </div>

        <div class=" input-select">
            <label for="codG">Nueva Gerencia <span class="required"> *</span></label><br>
            <select name="gerencia" id="codG" required>
                <option value="">Seleccione una Gerencia</option>
                <option value="MANTENIMIENTO" 
            <?= (isset($coordinador['gerencia']) && $coordinador['gerencia'] == 'MANTENIMIENTO') ? 'selected' : 'selected' ?>>
            Mantenimiento
        </option>
        <option value="OPERACIONES"
            <?= (isset($coordinador['gerencia']) && $coordinador['gerencia'] == 'OPERACIONES') ? 'selected' : '' ?>>
            Operaciones
        </option>
            </select>
        </div>
    </div>

       

         <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="update" value="update">Guardar</button>
        <a href="CoorController.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>

    </form>
</div>


</body>
</html>
