<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
	<link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
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
		<form action="index.php?c=usuario&a=findUser" method="POST" class="form">
			<div class="input-box">
				<label>Usuario<span class="required"> *</span></label>
				<input type="text" name="userName" required placeholder="Ingrese su usuario">
			</div>
			<div class="botones">
                <button type="submit" id="btnGuardar" class="btn btn-success">Buscar</button>
                
                
                <a href="../index.php" class="btn btn-sm btn-warning">Volver</a>
                
    </div>
		</form>
	</section>	
</body>
</html>