<?php
	require_once"../BD/BD.php";
	require_once"../usuario/modelo/UsuarioModelo.php";
	require_once"../config/variablesGlobales.php";
	require_once"../utilidades/enrutador.php";
	
	$conex= new BD();
	
	session_start();

		
	if (isset($_GET['c'])) 
	{
		

		$controlador = cargarControlador($_GET['c']);
		if(isset($_GET['a']))
		{
			
			if(isset($_GET['id']))
			{
				cargarAccion($controlador,$_GET['a'], $_GET['id']);
			}else
			{
				cargarAccion($controlador,$_GET['a'],null);
			}
		}else
		{
			cargarAccion($controlador,ACCION_PRINCIPAL,null);
		}
	}else
	{
		
		$controlador=cargarControlador(CONTROLADOR_PRINCIPAL);
		cargarAccion($controlador,ACCION_PRINCIPAL,null);
	}
?>