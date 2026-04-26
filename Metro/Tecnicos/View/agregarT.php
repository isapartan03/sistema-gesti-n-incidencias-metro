<?php
require_once"../../utilidades/repositorio.php";
    verifcarSession();
if($_SESSION['rol']==='Trabajador'){
    
    header('Location: ../Controller/TecCont.php?action=mostrar&n=3');
    exit;
}

?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Técnico/Registro</title> <!--Agregar acentos-->        
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
    include '../../Assets/HTML/headerAdmin.php';; //AQUI DEBE IR EL HEADER DE ADMIN
?>

<div class="contenedoor">
    <header>Agregar nuevo Técnico</header>

    <form action="../Controller/TecCont.php" method="POST" class="form">

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
        <option value="">Seleccione un Código de Grado</option>
        <?php if (!empty($codGrado)): ?>
            <?php foreach ($codGrado as $cod): ?>
                <?php if (in_array($cod['ID_Grado'], [1, 2, 3, 6])): ?>
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
            <label for="id_coord">Coordinación <span class="required"> *</span></label><br>
            <select name="id_coord" id="id_coord" required>
                <option value="">Seleccione una coordinación</option>
                <?php if (!empty($coordinaciones)): ?>
                    <?php foreach ($coordinaciones as $coord): ?>
                        <option value="<?= $coord['ID_Coordinacion'] ?>">
                            <?= htmlspecialchars($coord['Nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No hay coordinaciones disponibles</option>
                <?php endif; ?>
            </select>
        </div>
        </div>

        
        <div class="botones">
        <button type="submit" id="btnGuardar" class="btn btn-success" name="crear" value="crear">Guardar</button>
        <a href="TecCont.php?action=mostrar" class="btn btn-sm btn-warning">
          Volver
        </a>
      </div>

    </form>
</div>

</body>
</html>