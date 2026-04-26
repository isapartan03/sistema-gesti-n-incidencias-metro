<?php
	function cargarControlador($controlador)
	{
		
		
		$nombreControlador=ucwords($controlador)."Controlador";
		
		

		$archivoControlador = "../".ucwords($controlador)."/controlador/".ucwords($controlador).".php";
		

		if (!is_file($archivoControlador)) 
		{
			
			$archivoControlador = "../".MODULO_PRINCIPAL."/controlador/".CONTROLADOR_PRINCIPAL.".php";
			$nombreControlador=CONTROLADOR_PRINCIPAL."Controlador";
		}


		require_once $archivoControlador;
		$control = new $nombreControlador();
		

		return $control;

	}


	function cargarAccion($controlador,$accion,$id=null)
	{
		
		
		
		if ($accion && method_exists($controlador,$accion))
		{
			if($id==null)
			{
				$controlador->$accion();
			}else
			{
				$controlador->$accion($id);
			}
			
		}else
		{
			$controlador->$accion();
		}
	}




?>