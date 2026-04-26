<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/EquipoC.php?action=mostrar&n=3');
    exit;
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <title>Equipo/Registro</title>
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
    <header>Agregar nuevo Equipo</header>

    <form action="../Controller/EquipoC.php" method="POST" class="form">

        <div class="input-box">
            <label for="name">Serial / Código <span class="required"> *</span></label>
            <input type="text" name="id" id="id" required>
        </div>

        <div class="input-box">
            <label for="name">Nombre <span class="required"> *</span></label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="input-box">
            <label for="numberA">Nombre de Ambiente <span class="required"> *</span></label>
            <input type="text" name="numberA" id="numberA" required>
        </div>
        <div class="column">
        <div class="input-select">
            <label for="id_coord">Coordinación <span class="required"> *</span></label><br>
            <select name="id_coord" id="id_coord" required>
                <option value="">Seleccione una coordinación</option>
                <?php if (!empty($coordinaciones)): ?>
                    <?php foreach ($coordinaciones as $coord): ?>
                        <option value="<?= $coord['ID_Coordinacion'] ?>">
                            <?= $coord['Nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay coordinaciones disponibles</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="input-select">
            <label for="id_estacion">Estación <span class="required"> *</span></label><br>
            <select name="id_estacion" id="id_estacion" required>
                <option value="">Seleccione una estación</option>
                <?php if (!empty($estacion)): ?>
                    <?php foreach ($estacion as $estac): ?>
                        <option value="<?= $estac['ID_Estacion'] ?>">
                            <?= $estac['Nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay estaciones disponibles</option>
                <?php endif; ?>
            </select>
        </div>
</div>
          <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="crear" value="crear">Guardar</button>
        <a href="equipoC.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>

    </form>
</div>


</body>
</html>