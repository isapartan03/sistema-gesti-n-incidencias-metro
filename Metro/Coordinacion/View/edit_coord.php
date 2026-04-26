<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();

if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/CoordCont.php?action=mostrar&n=3');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    } else {
        echo "No se ha especificado un ID.";
    exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Coordinación</title>
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
        include '../../Assets/HTML/headerAdmin.php';
?>
<div class="contenedoor">
    <header>Editar Coordinación</header>
    <form method="POST" action="../Controller/CoordCont.php" class="form">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="input-box">
            <label for="nombre">Nuevo nombre para la Coordinación <span class="required"> *</span></label>
            <input type="text" name="nombre" id="nombre" value="<?=$cordinacion["Nombre"]?>" required>
        </div>

        <div class="input-box">
            <label for="correo">Correo de la Coordinación <span class="required"> *</span></label>
            <input type="email" name="correo" id="correo" value="<?=$cordinacion["correo"]?>" required>
        </div> 

        <div class="input-select">
    <label for="coordinador">Coordinador a cargo <span class="required"> *</span></label><br>
    <select name="coordinador" id="coordinador" required>
        <option value="">Seleccione un coordinador</option>
        <?php foreach ($coordinadores as $coor): ?>
            <option value="<?= htmlspecialchars($coor['carnet']) ?>" 
                <?= ($cordinacion['carnet_personal'] == $coor['carnet']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($coor['nombres']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

         <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="update" value="update">Guardar</button>
        <a href="CoordCont.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>
    </form>
</div>

</body>
</html>