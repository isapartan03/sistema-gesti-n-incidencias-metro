<?php

	//hass de clave 
	function hass($clave)
	{
		return password_hash($clave, PASSWORD_BCRYPT);
	}

	// recibe un valor y filtar caracteres invalidos
	function filtrar($valor,$modo)
	{

		switch ($modo) {
			case 's':
				return filter_var($valor, FILTER_SANITIZE_STRING);
				break;
			case 'e':
				return filter_var($valor, FILTER_SANITIZE_EMAIL);
				break;
			
			default:
				break;
		}
		
	}


//Devuelve treselementos aleatoreos de un array
	function obtenerPreguntasaEleatoreas($arrayPreguntas,$cant)
	{
		return array_rand($arrayPreguntas,$cant);
	}

//Permite verificar si hay una sesion activa
	function verifcarSession(){
		
		if (empty($_SESSION)) {
			header('Location: ../../../../Programas/Metro/index.php');
			exit;
		}

		
		
	}

// Funcio que almacena las alertas segun el tipo en un arreglo 
	function alerta($caso,$link=null)
	{
		switch ($caso) {
			case 'exito':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Completado!',
				'mesge'=> 'Operación completada con éxito.',
				'icon'=>'success'];
				break;

			case 'fracaso':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Oops!',
				'mesge'=> 'Parece que algo salio mal.',
				'icon'=>'error'];
				break;
			
			case 'repetido':
			$GLOBALS['alertas'] = [
				'titulo'=> '¡Intentelo nuevamente!',
				'mesge'=> 'Datos repetidos.',
				'icon'=> 'error' ];
			break;

		   case 'acceso':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Acción denegada!',
				'mesge'=> 'Actualmente no tiene permisos para  realizar esta accion.',
				'icon'=>'error'];
				break;
			case 'info':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Acción denegada!',
				'mesge'=> 'El usuario tiene informacion relacionada.',
				'icon'=>'error'];
				break;
			case 'vacia':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Acción denegada!',
				'mesge'=> 'No se puede exportar una tabla vacía.',
				'icon'=>'error'];
				break;
				case 'soez':
				$GLOBALS['alertas'] = array();
				$GLOBALS['alertas'] = [
				'titulo'=> '¡Acción denegada!',
				'mesge'=> 'No se permite ese tipo de palabras.',
				'icon'=>'error'];
				break;

			default:
				$GLOBALS['alertas']=array();
				break;
		}
	}
//Permite resetaer la variable gloval alertas
	function resetGlobals(){
		$GLOBALS['alertas']=array();
	}
//Permite detectar el tipo de notificacion y llama a la funcion alerta segun el caso
	function detectar($tipoDeNotificacionPorElGet){
		switch ($tipoDeNotificacionPorElGet) {
			case '0':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
				alerta('exito');
				break;
			case '1':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
				alerta('fracaso');
				break;
			case '2':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
				alerta('repetido');
				break;
			case '3':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
				alerta('acceso');
				break;
			case '4':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
					alerta('vacia');			// code...
					break;
				case '5':
				echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        $(".swal2-container select, .alert select").select2("destroy").remove();
                    }, 1);
                });
            </script>';
					alerta('soez');			// code...
					break;						
			default:



				resetGlobals();
				break;
		}
	}

//Permite encriptar preguntas de seguridad 
	function ecripPregunta($text)		
	{
		$textModifi='';
		for ($i=0; $i <strlen($text) ; $i++) { 
			$caracter=(ord($text[$i])+1);
			$textModifi.=chr($caracter);
		}

		return $textModifi;
	}
//Pemirte desencriptar las preguntas de seguridad
	function desenCriptPreguntas($lista){
		$newValue='';
		for ($i=0; $i < count($lista) ; $i++) {
			$valor=$lista[$i]['value']; 
			for ($j=0; $j <strlen($valor); $j++) { 
				$caracter=(ord($valor[$j])-1);
				$newValue.=chr($caracter);
			}
			$lista[$i]['value']=$newValue;
			$newValue='';
		}
		return $lista;
	}


	
?>