<?php
	include_once'PersonaModelo.php';
	include_once"../BD/BD.php";
	class UsuarioModelo extends PesonaModelo{

		private $userName;
		private $clave;
		private $id;
		private $tabla;
		private $rol;
		private $conex;


		//Geters Y seters



		public function setUserName($userName){$this->userName=$userName;}
		public function setClave($clave){$this->clave=$clave;}
		public function setId($id){$this->id=$id;}
		public function setTable($tabla){$this->tabla=$tabla;}
		public function setRol($rol){$this->rol=$rol;}
		public function setConex($conex){$this->conex=$conex;}


		public function getConex(){return $this->conex;}
		public function getUserName(){return $this->userName;}
		public function getClave(){return $this->clave;}
		public function getId(){return $this->id;}
		public function getTable(){return $this->tabla;}
		public function getRol(){return $this->rol;}

		//constructor
		function __construct($userName=null,$clave=null, $id=null,$nombres=null,$apellidos=null,$rol=null,$carnet=null)
		{
			parent::__construct($nombres,$apellidos,$carnet);
			if($userName!=null){$this->setUserName($userName);}
			if($clave!=null){$this->setClave($clave);}
			if($id!=null){$this->setId($id);}
			if($rol!=null){$this->setRol($rol);}
			$this->setTable("usuario");
			$this->setConex(new BD());


		}

		public function toString()
		{
			return parent::toString().$this->getUserName()."/". 
				   $this->getClave()."/". 
				   $this->getId()."/".
				   $this->getRol()."<br>";
		}


		//permite obtene la lista de usurios
		public function ObtenerListaUser()
		{

			return $this->getConex()->obtenerLista();
		}

		//registra un susrio 
		private function regisUsuario()
		{
			return $this->getConex()->registrarUsuario(parent::getCarnet(),$this->getUserName(),hass($this->getClave()),$this->getRol());
				
		}




		

		


		//permite registrar Un nuevo usuario
		public function registrarNewUser()
		{
			
			
				if(parent::registrarNewPerso())
				{
				
				 	return $this->regisUsuario();
			
				}else{
					
					return false;
				}
				
			
				
				
				
		

			
			
		}

		//Devuelve los datos personales del usuario
		private function buscarDatos()
		{
			parent::setCarnet($this->getConex()->selecId($this->getTable(),$this->getId())[0]['Carnet']);
			
		}

//devuelves los datos presonales de un usuario
		public function obtenerDatosPerso(){
			$this->buscarDatos();
			
			return parent::obtenerDatos();
		}
//Permite obtener la infomarcion e un usuario
		public function obtenerUser()
		{
			return $this->getConex()->selecBy("Username",$this->getUserName(),$this->getTable());
		}
//Permite actualizar los datos
//actualiza los datos personales y luego el rol 
		public function actualizar()
		{
			
			if (parent::actualizar()) 
			{
				return $this->actualizarRol();

				
			}else
			{
				return false;
			}
		}
//Permite actualizar el rol de usuario 
		private function actualizarRol(){
			
			
			 return $this->getConex()->actualizarRol($this->getRol(),$this->getCarnet());
			 
		}
//Pemite cambiar el estatus de un suaruario
		public function changeStatus($tipo)
		{
			return $this->getConex()->changeStatus($this->getTable(),$tipo,$this->getId());
			
		}
//Comprueba si un usuaruio tiene metodos de recuperacion 
		public function comprobarPregu()
		{
			
			return $this->getConex()->comprobarPregSeguridad($this->getId());
			
			
		}
//Permite reiniciar las preguntas y respuestas de seguridad
		public function resetPregu(){
				if ($this->getConex()->resetearPreguntas($this->getId())) {
					
				
				return $this->getConex()->resetearRespuestas($this->getId());
			}

		}
//Verifica si el usuario tiene informacuion relcaionada con us identificador 
		public function verificarInfo(){
			return $this->getConex()->comprobarInfo($this->getId(),"falla");

		}
//Permite eliminar un usuario del sistema 
//Tambien elimina sus metedos de seguridad
		public function eliminar(){
			if($this->getConex()->eliminarUser($this->getId())){
				$this->getConex()->resetearPreguntas($this->getId());
				$this->getConex()->resetearRespuestas($this->getId());
				return true;
			
			}else
			{
				return false;
			}
		}



	














	}//Fin de la clase






?>