<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
	<link rel="stylesheet" type="text/css" href="../Assets/CSS/formulario.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/tablaMenu.css">
	<title>Usuario/Preguntas</title>
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
		
		<form action="index.php?c=usuario&a=regis" method="POST" class="form" id="preguntasForm">
    <!-- Parte 1 (visible inicialmente) -->
    <div id="parte1">
    	<div id="alerta"></div>
        <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 1<span class="required"> *</span></label>
                <input type="text" name="p0" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 1<span class="required"> *</span></label>
                <input type="text" name="r0" required>
            </div>
        </div>
        <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 2<span class="required"> *</span></label>
                <input type="text" name="p1" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 2<span class="required"> *</span></label>
                <input type="text" name="r1" required>
            </div>
        </div>
        <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 3<span class="required"> *</span></label>
                <input type="text" name="p2" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 3<span class="required"> *</span></label>
                <input type="text" name="r2" required>
            </div>
        </div>
        
        <div class="botones">
            <button type="button" class="btn btn-success" onclick="mostrarParte(2)">Siguiente</button>
            <button type="reset" class="btn btn-info">Limpiar</button>
        </div>
    </div>
    
    <!-- Parte 2 (oculta inicialmente) -->
    <div id="parte2" style="display:none;">
        <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 4<span class="required"> *</span></label>
                <input type="text" name="p3" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 4<span class="required"> *</span></label>
                <input type="text" name="r3" required>
            </div>
        </div>
        <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 5<span class="required"> *</span></label>
                <input type="text" name="p4" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 5<span class="required"> *</span></label>
                <input type="text" name="r4" required>
            </div>
        </div>
         <div class="column">
            <div class="input-box">
                <label>Pregunta Personalizada 6<span class="required"> *</span></label>
                <input type="text" name="p5" required>
            </div>
            <div class="input-box">
                <label>Respuesta personalizada 6<span class="required"> *</span></label>
                <input type="text" name="r5" required>
            </div>
        </div>
        <input type="text" name="id" hidden value="<?=$this->getObjUser()->getId()?>">
        
        <div class="botones">
             <button type="button"  class="btn btn-primary" onclick="mostrarParte(1)" >Anterior</button>
            
            <button type="submit" class="btn btn-success">Guardar</button>
             <a href="../index.php" class="btn btn-sm btn-warning">Volver</a>
        </div>
    </div>
</form>
 

 
 
<script type="text/javascript" src="../Assets/Js/formularioDoble.js"></script>






	
	
	
			
		
</body>
</html>