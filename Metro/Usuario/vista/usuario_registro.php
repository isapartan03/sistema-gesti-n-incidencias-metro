<?php require_once"../utilidades/repositorio.php";
verifcarSession();?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
	<link rel="stylesheet" href="../Assets/CSS/tablaMenu.css">
	<link rel="stylesheet" type="text/css" href="../Assets/CSS/formulario.css">

	<title>Usuario/Registro</title>
<style>
.contenedoor {
  max-width: 500px;
  margin: 4px auto;
  padding: 10px;
  font-size: 14px; 
}

.contenedoor label {
  font-size: 0.9rem; 
}

.contenedoor input[type="text"],
.contenedoor input[type="number"],
.contenedoor input[type="password"],
.contenedoor textarea {
  font-size: 0.85rem;
  padding: 5px 7px;
}

.contenedoor .buttons input {
  font-size: 0.9rem;
  padding: 5px 5px;
  margin-right: 5px;
}


.contenedoor .input-box {
  margin-bottom: 10px;
}


</style>
</head>
<body>
	<?php include'../Assets/HTML/headerAdmin.php';?><br>
	<section class="contenedoor">
		<header>Rellene los campos</header>

		<form action="index.php?c=usuario&a=registrar" method="POST" class="form" id="formRecuperacion">
			<div class="column">			
			<div class="input-box">
				<label>Nombre<span class="required"> *</span></label>
				<input type="text" name="nombres" required>
			</div>
			<div class="input-box">
				<label>Apellidos</label><label class="etiqueta"> </label>
				<input type="text" name="apellidos" required>
			</div>
			
			<div class="input-box">
				<label>Carnet<span class="required"> *</span></label>
				<input type="number" name="carnet" required>
			</div>
			</div>

			<div class="input-box">
					<label>Nombre de Usuario<span class="required"> *</span></label>
					<input type="text" name="userName" required>
				</div>
				<div class="radioprueba">
			<h3>Cargo</h3>
		<div class="CargoOpción">
			<div class="cargo">
				<input type="radio"  name="rol" value="Admin"> 
				<label for="uno">Administrador<span class="required"> *</span></label>
			</div>
			<div class="cargo">
				<input type="radio" name="rol" value="Trabajador"> 
				<label for="dos">Operador<span class="required"> *</span></label>
			</div>
		</div>
		</div>


			<div class="column">
				<div class="input-box">
					<label>Contraseña<span class="required"> *</span></label>
					 <input type="password"name="clave" id="pass" required oninput="validarContrasena()">
					
				</div>
				
				 <div class="input-box">
                <label>Confirme contraseña<span class="required"> *</span></label>
                <input type="password" id="confirmarPass" required oninput="validarConfirmacion()">
              
            </div>


				
			</div>
			<div id="errorContrasena" class="error-contrasena"></div>
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
			
			
			 <div class="botones">
				<button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
				
				<button type="reset" id="btnGuardar" class="btn btn-info btn-sm btn-show-reset">Limpiar</button>
			                <a href="index.php?c=usuario" class="btn btn-sm btn-warning">Volver</a>

				
			</div>
		</form>
	</section>
	
	 <script type="text/javascript" src="../Assets/Js/validarContrasena.js"></script>
	
			
		
</body>
</html>