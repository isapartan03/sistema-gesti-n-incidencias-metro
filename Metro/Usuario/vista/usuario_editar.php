<?php require_once"../utilidades/repositorio.php";
    verifcarSession();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../Assets/CSS/formulario.css">
    <link rel="stylesheet" href="../Assets/CSS/tablaMenu.css">
    <title>Usuario/Editar</title>
    <style>
  .required {
    color: red;
    font-weight: bold;
  }
    </style>

</head>
<body>
    <?php include'../Assets/HTML/headerAdmin.php';?>
    <section class="contenedoor">
        <header>Editar</header>
        <form method="POST" action="../public/index.php?c=usuario&a=editar" autocomplete="off" class="form">

        <div class="input-box">
            <label>Nombre<span class="required"> *</span></label>
            <input type="text" name="nombres"  required value="<?php echo $usuario['nombres'];?>">
        </div>
        
        <div class="input-box">
            <label>Apellidos<span class="required"> *</span></label>
            <input type="text" name="apellidos" required value="<?php echo $usuario['apellidos'];?>">
        </div>

        <?php if($usuario['ID'] != $_SESSION['id']): ?>
        <label>Rol<span class="required"> *</span></label>
        <div class="input-select">
                <select name="rol" required>
                    <option value='' disabled>Selecciona una opción</option>
                    <option value="Admin" <?php echo ($usuario['Rol'] == 'Admin') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="Trabajador" <?php echo ($usuario['Rol'] == 'Trabajador') ? 'selected' : ''; ?>>Operador</option>
                </select>
            </div>
        <?php endif; ?>

        <input type="text" name="id" required value="<?php echo $usuario['carnet'];?>" hidden><br></br>

  <div class="botones">
                <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
                
                
                <a href="index.php?c=usuario" class="btn btn-sm btn-warning">Volver</a>
                
    </div>

    </form>
    </section>
            
        
</body>
</html>