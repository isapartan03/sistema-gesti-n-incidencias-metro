<?php
	include_once"../BD/BD.php";
	class PesonaModelo 
	{
		private $nombres;
		private $apellidos;
		private $carnet;
		private $conex;
		

		
		
		//Geters Y seters

		public function setNombres($nombres){$this->nombres=$nombres;}
		public function setApellidos($apellidos){$this->apellidos=$apellidos;}
		public function setCarnet($carnet){$this->carnet=$carnet;}
		public function setConex($conex){$this->conex=$conex;}


		public function getConex(){return $this->conex;}
		public function getCarnet(){return $this->carnet;}
		public function getNombres(){return $this->nombres;}
		public function getApellidos(){return $this->apellidos;}
		




		function __construct($nombres=null,$apellidos=null,$carnet=null){
			if($carnet!=null){$this->setCarnet($carnet);}
			if($nombres!=null){$this->setNombres($nombres);}
			if($apellidos!=null){$this->setApellidos($apellidos);}
			$this->setConex(new BD());

		}


		public function toString()
		{
			return $this->getNombres()."/". 
				   $this->getApellidos()."/". 
				   $this->getCarnet()."/";
		}



		protected function obtenerDatos(){
			return $this->getConex()->selecDatosPersona($this->getCarnet());
		}



		protected function registrarNewPerso(){

			return $this->getConex()->regisDatosPerso($this->getCarnet(),$this->getNombres(),$this->getApellidos());

			
	 		
		}

		protected function actualizar(){
			
			if ($this->getConex()->actualiDatos($this->getCarnet(),$this->getNombres(),$this->getApellidos())) 
			{
				
			//	echo "se ha actualizado correctamente en la clase persona<br>";
				return true;
			}else
			{
				
			//	echo "los datos no se actualizaron correctamente en la clase persona<br>";
				return false;
			}



			
		}













	}// fin de la clase













?>