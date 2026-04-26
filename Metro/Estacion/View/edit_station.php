<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();

 
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/estacionC.php?action=mostrar&n=3');
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
		 include '../../Assets/HTML/headerAdmin.php';//AQUI DEBE IR EL HEADER DE ADMIN
?>  

<div class="contenedoor">
    <header>Editar Estación</header>

    <form method="POST" action="../Controller/estacionC.php" class="form">

        <!-- Campo oculto con ID -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="input-box">
            <label for="nombre">Nuevo nombre para la estación <span class="required"> *</span></label>
            <input type="text" name="nombre" id="nombre" value="<?=$estacion[0]['Nombre']?>" required>
        </div>

         <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" value="update" name="update">Guardar</button>
        <a href="estacionC.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>

    </form>
</div>



</body>
</html>