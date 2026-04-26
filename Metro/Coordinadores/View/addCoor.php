<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/CoorController.php?action=mostrar&n=3');
    exit;
}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" href="../../Assets/CSS/Formulario.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
    <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <script src="../../Assets/Js/sweetalert2.min.js"></script>
    <title>Personal/Registro</title>
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
    <header>Personal/Registro</header>

    <form action="../Controller/CoorController.php" method="POST" class="form">

        <div class="input-box">
            <label for="name">Nombre <span class="required"> *</span></label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="input-box">
            <label for="lastN">Apellido <span class="required"> *</span></label>
            <input type="text" name="lastN" id="lastN" required>
        </div>

        <div class="input-box">
            <label for="carnet">Carnet <span class="required"> *</span></label>
            <input type="number" name="carnet" id="carnet" required>
        </div>
         <div class="column">

<div class="input-select">
    <label for="codG">Código de Grado <span class="required"> *</span></label><br>
    <select name="codG" id="codG" required>
        <option value="">Seleccione el grado</option>
        <?php if (!empty($codGrado)): ?>
            <?php foreach ($codGrado as $cod): ?>
                <?php if (in_array($cod['ID_Grado'], [4, 5])): ?>
                    <option value="<?= $cod['ID_Grado'] ?>">
                        <?= $cod['Nombre_Grado'] ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="">No hay códigos de grado disponibles</option>
        <?php endif; ?>
    </select>
</div>


        <div class="input-select">
            <label for="gerencia">Gerencia <span class="required"> *</span></label><br>
            <select name="gerencia" id="gerencia" required>
                <option value="">Seleccione una gerencia</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Operaciones">Operaciones</option>
            </select>
        </div>
    </div>


        <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="crear" value="crear">Guardar</button>
        <a href="CoorController.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>

    </form>
</div>


</body>
</html>