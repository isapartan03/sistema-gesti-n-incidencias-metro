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
		<header>Rellene Los campos obligatorios</header>
		
		<form method="POST" action="index.php?c=usuario&a=capturaPre" class="form">
			<div class="input-box">
				<label><?php echo $preguntas[$x[0]]['value']; ?><span class="required"> *</span></label>
				<input type="password" name="1" required>
				<input type="text" name="id1"  value="<?php echo $preguntas[$x[0]]['idPregunta'] ?>" hidden >
			</div>
			
			<div class="input-box">
				<label><?php echo $preguntas[$x[1]]['value']; ?><span class="required"> *</span></label>
				<input type="password" name="2" required>
				<input type="text" name="id2"  value="<?php echo $preguntas[$x[1]]['idPregunta'] ?>" hidden >
			</div>

			<div class="input-box">
				<label><?php echo $preguntas[$x[2]]['value'];?><span class="required"> *</span></label>
				<input type="password" name="3" required></label><br></br>
				<input type="text" name="id3"  value="<?php echo $preguntas[$x[2]]['idPregunta'] ?>" hidden >
			</div>
			<input type="text" name="id" value="<?php echo $this->getDato('ID');?>" hidden>

			<div class="botones">
                <button type="submit" id="btnGuardar" class="btn btn-success">Recuperar</button>
                
                
                <a href="../index.php" class="btn btn-sm btn-warning">Volver</a>
                
    </div>
		</form>


	</section>
	



</body>
</html>


