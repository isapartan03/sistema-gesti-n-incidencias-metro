<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
    <link rel="stylesheet" type="text/css" href="../Assets/CSS/formulario.css">
     <link rel="stylesheet" href="../Assets/CSS/tablaMenu.css">
    <title>Usuario/Recuperacion</title>
    <style>
  .required {
    color: red;
    font-weight: bold;
  }
    </style>
</head>
<body>
    <section class="contenedoor">
        <header>Clave Nueva</header>
        <form method="POST" action="index.php?c=usuario&a=actualizarPass" autocomplete="off" class="form" id="formRecuperacion">
            <div class="input-box">
                <label>Nueva contraseña<span class="required"> *</span></label>
                <input type="password" name="pass" id="pass" required oninput="validarContrasena()">
                
               
            </div>
            <div id="errorContrasena" class="error-contrasena"></div>

            <div class="input-box">
                <label>Confirme contraseña<span class="required"> *</span></label>
                <input type="password" id="confirmarPass" required oninput="validarConfirmacion()">
               
            </div>
 <div id="errorConfirmacion" class="error-contrasena"></div>
            
            <div class="requisitos-contrasena">

                    <p>La contraseña debe cumplir con:</p>
                    <ul>
                        <li id="longitud" class="requisito">Al menos 8 caracteres</li>
                        <li id="mayuscula" class="requisito">Al menos una letra mayúscula</li>
                        <li id="minuscula" class="requisito">Al menos una letra minúscula</li>
                        <li id="numero" class="requisito">Al menos un número</li>
                       <li id="especial" class="requisito">Al menos un carácter especial<br>  !»#$%&'()*+,-./:;<=>?@[\]^_`{|}~</li>
                    </ul>
                </div>
                <input type="text" name="id" value="<?php echo $this->getDato('id');?>" hidden>

            <div class="botones">
                <button type="submit" class="btn btn-success" id="btnSubmit">Guardar</button>

                
               <button type="reset" class="btn btn-info">Limpiar</button>
                <a href="../index.php" class="btn btn-sm btn-warning">Volver</a>

            </div>
            
        </form>
    </section>

    <script type="text/javascript" src="../Assets/Js/validarContrasena.js"></script>
</body>
</html>