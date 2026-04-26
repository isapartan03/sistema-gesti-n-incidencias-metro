<?php
session_start();
require_once"../../utilidades/repositorio.php";
    verifcarSession();
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/estacionC.php?action=mostrar&n=3');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Estación/Registro</title>
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
    <header>Agregar nueva Estación</header>

    <form action="../Controller/estacionC.php" method="POST" class="form">

        <div class="input-box">
            <label for="estac_name">Nombre de nueva Estación <span class="required"> *</span></label>
            <input type="text" id="estac_name" name="estac_name" required>
        </div>

        <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="Crear" value="Crear">Guardar</button>
        <a href="../Controller/estacionC.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>
    </form>
</div>



</body>
</html>