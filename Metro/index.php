<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="Assets/CSS/Styles.css">
	<link rel="icon" type="text/css" href="Assets/imagenes/imgMetro16.png">
	<link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
	<script src="https://kit.fontawesome.com/a81368914c.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	

</head>
<body>
	<header>
	<div class="cintillo">
		<img src="Assets/imagenes/MPPT.png" alt="Ministerio del Poder Popular para el Transporte" id="uno">
		
		<img src="Assets/imagenes/GMT.png" alt="Gran Misión Transporte" id="tres">
		<img src="Assets/imagenes/LogoB1.png" alt="200 años Bicentenario 2022-2030" id="cuatro">
	</div>
	</header>
	<div id="notificacion"></div>
	<div class="card"></div>
		<div class="login-content">
			<form method="POST" action="public/index.php?c=usuario&a=login">
				<img src="Assets/imagenes/imgMetro16.png" alt="Avatar">
				<p class="GFO"><span>G</span>estion de <span>F</span>allas <span>O</span>perativas</p>
					<p class="MLT"><span>M</span>etro <span>L</span>os <span>T</span>eques</p>

				<div class="input-div one">
				   <div class="i">
						<i class="fas fa-user"></i>
				   </div>
				   <div class="div">
						<h5>Usuario</h5>
						<input type="text" name="usuario" class="input" required>
				   </div>
				</div>
				<div class="input-div pass">
				   <div class="i"> 
						<i class="fas fa-lock"></i>
				   </div>
				   <div class="div">
						<h5>Contraseña</h5>
						<input type="password" name="clave" class="input" required>
				   </div>
				</div>
				<a href="public/index.php?c=usuario&a=FrmRecupeUser">¿Olvidó su Contraseña?</a>
				

				<input type="submit" class="btn" value="Entrar">
			</form>
		</div>
	</div>
	
	<script type="text/javascript" src="Assets/Js/main.js"></script>
	<script type="text/javascript" src="Assets/Js/notificacion.js"></script>
	
</body>
</html>
</html>
 