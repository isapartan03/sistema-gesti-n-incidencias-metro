<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();

require_once '../../utilidades/repositorio.php';
require_once '../../config/variablesGlobales.php';
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/CoordCont.php?action=mostrar&n=3');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <title>Coordinación/Registro</title>
    <link rel="stylesheet" href="../../Assets/CSS/Formulario.css">
     <link rel="stylesheet" href="../../Assets/CSS/tablaMenu.css">
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/sweetalert2.min.css">
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
    <header>Agregar Nueva Coordinación</header><br>

    <form action="../Controller/CoordCont.php" method="POST" class="form">
        <div class="input-box">
            <label for="coor_name">Nombre de la nueva Coordinación<span class="required"> *</span></label>
            <input type="text" id="coor_name" name="coor_name" required>
        </div>

        <div class="input-box">
            <label for="coor_email">Correo de la Coordinación<span class="required"> *</span></label>
            <input type="email" id="coor_email" name="coor_email" required>
        </div>

        <div class="input-select">
            <label for="coordinador">Coordinador a cargo<span class="required"> *</span></label><br>
            <select name="coordinador" id="coordinador" required>
                <option value="">Seleccione un coordinador</option>
                <?php foreach ($coordinadores as $coor): ?>
                    <option value="<?= htmlspecialchars($coor['carnet']) ?>">
                        <?= htmlspecialchars($coor['nombres']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

       <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="Crear" value="Crear">Guardar</button>
        <a href="CoordCont.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>
    </form>
</div>
<?php 
if (!empty($GLOBALS['alertas'])) {
    echo '<pre>';
    var_dump($GLOBALS['alertas']);
    echo '</pre>';
}
?>

<script>
    //Este ecrip permite mostrar la alerta segun los parametros de la variable global alertas
    swal('<?=$GLOBALS['alertas']['titulo']?>','<?=$GLOBALS['alertas']['mesge']?>','<?=$GLOBALS['alertas']['icon']?>',);
</script>
<?php resetGlobals();
?>
</body>
</html>
