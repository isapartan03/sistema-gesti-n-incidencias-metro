<?php
	require_once"../utilidades/repositorio.php";
	require_once"../utilidades/LogModelo.php";
	
	
	
	
	class UsuarioControlador{
		#variable que almacena el objeto tipo usuario
		private $objUser;
		#Almacena la variable coneccion
		private $conex;
		#Almacena un arreglo para el flujo de datos
		private $datos=[];
		#Atributo para el objeto del tipo log
		private $log;


		//seters Y geters
		public function setDatos($datos){$this->datos=$datos;}
		public function setObjUser($objUser){$this->objUser=$objUser;}
		public function setDato($llave,$dato){$this->datos[$llave]=$dato;}
		public function setConex($conex){$this->conex=$conex;}

//*************************************************************************\\
		public function getConex(){return $this->conex;}
		public function getDatos(){return $this->datos;}
		public function getDato($llave){return $this->datos[$llave];}
		public function getObjUser(){return $this->objUser;}

		//constructor
		function __construct($objUser=null)
		{
			$this->setObjUser($objUser);
			$this->setConex(new BD());
			$this->log = new SistemaLog($this->getConex());
		}

		//metodo index para mostrar una lista estandar de usuarios 
		public function index()
		{

			
			$this->setObjUser(new UsuarioModelo());
			$usuarios = $this->getObjUser()->ObtenerListaUser(); 
			require_once"../Usuario/vista/usuario_lista.php";
		}



		// muestra el fromulario de registro
		public function mostraFormu()
		{
			if ($_SESSION['rol'] !='Admin') {
				
				
				alerta('acceso');
				$this->index();
				exit;
			}
			require_once"../Usuario/vista/usuario_registro.php";
		}

		//captura los datos y regiatra un nuevo usuario
		public function registrar()
		{
			$this->setDato('userName',$_POST['userName']);
			$this->setDato('clave',$_POST['clave']);    
			$this->setDato('nombres',$_POST['nombres']);
			$this->setDato('apellidos',$_POST['apellidos']);
			$this->setDato('carnet',$_POST['carnet']);
			$this->setDato('rol',$_POST['rol']);

			$this->registro();
		}

		// permite registrar un nuevo usuario
		private function registro()
		{
			$this->setObjUser(new UsuarioModelo
				(
					$this->getDato('userName'),                     
					$this->getDato('clave'),
					null,
					$this->getDato('nombres'),
					$this->getDato('apellidos'),
					$this->getDato('rol'),
					$this->getDato('carnet')
				)
			);
			
			try {

				if($this->getObjUser()->registrarNewUser()){
					$this->regisLog("Registro",NULL);
					alerta('exito');
					$this->index();
				}else
				{
					alerta("fracaso");
					$this->index();
				}


				
			} catch (Exception $e) {

				
				$this->regisLog('error',$e,"error de registro");
				alerta("fracaso");
				$this->index();
				
				
			}
			
		}


	

		//muetra el fromulario de edicion 
		public function showfrm($id=null)
		{
			$this->setObjUser(new UsuarioModelo());
			
			if ($id==null) {
				
				$this->getObjUser()->setId($_SESSION['id']);
			}else
			{
				if ($_SESSION['rol'] !='Admin') {
					alerta('acceso');
						
					$this->index();
					exit;
				}

					
				$this->getObjUser()->setId($id);
			}
			
			$usuario=$this->getObjUser()->obtenerDatosPerso();
			/*echo "<pre>";
			var_dump($usuario);*/
			require_once"../Usuario/vista/usuario_editar.php";
			
			
		}

		//captura los datos y llama el metodo actualizar
		public  function editar()
		{
			
			$this->setDato('id',$_POST['id']);
			$this->setDato('nombres',$_POST['nombres']);
			$this->setDato('apellidos',$_POST['apellidos']);
			if (!isset($_POST['rol'])) {
				
			$this->setDato('rol',$_SESSION['rol']);
			}else
			{
				$this->setDato('rol',$_POST['rol']);
			}

			$this->actualizar();
		}

		//actualiza los datos utilizando el metodo actualizar del objeto usuario modelo
		private  function actualizar()
		{
			////echo "<pre>";
			//var_dump($this->getDatos());

			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setCarnet($this->getDato('id'));
			$this->getObjUser()->setNombres($this->getDato('nombres'));
			$this->getObjUser()->setApellidos($this->getDato('apellidos'));
			$this->getObjUser()->setRol($this->getDato('rol'));
			
			try {

				if($this->getObjUser()->actualizar()){
				$this->regisLog('Actualizar');
				alerta('exito');
				$this->index();
			}
				
			} catch (Exception $e) {
				alerta('fracaso');
				$this->regisLog("error",$e,"error al actualizar usuario");
				$this->index();
				
			}

			
			
			
			
		}


		//permite iniciar la sesion
		public function login()
		{
			
			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setUserName(filtrar($_POST['usuario'],"s"));
			$this->getObjUser()->setClave(filtrar($_POST['clave'],"s"));
			$this->crearSesion();
			
		}

		//busca un usuario y lo devuelve
		private function buscarUser()
		{
			return $this->getObjUser()->obtenerUser();
		}


		//verifica la clave 
		private function verifPass()
		{
			if(password_verify($this->getObjUser()->getClave(), $this->buscarUser()["Password"]))
			{

				return true;
			}
			else
			{
				$this->veriIntentos();
				return false;
			}
		}

		//permite crear la sesion 
		private function crearSesion()
		{
			if ($this->buscarUser()!=null && $this->verifPass() && $this->verificarStatus()) 
			{
				$this->getObjUser()->setId($this->buscarUser()['ID']);
				$this->getObjUser()->setRol($this->buscarUser()['Rol']);

				
				$_SESSION['id']=$this->getObjUser()->getId();
				$_SESSION['rol']=$this->getObjUser()->getRol();
				$_SESSION['userName']=$this->getObjUser()->getUserName();
				$this->regisLog("login","ninguno");

				$this->comprobarSegu();

				
			}
			else
			{
				
				
				$this->mostrarLogin(0);

			}
		}
//Permite verificar si el usuario aun no esta bloqueado por intentos fallidos
		private function veriIntentos(){

			if(!isset($_SESSION['intentos']) && $this->verificarStatus()){
				$_SESSION['intentos']=1;
				$this->mostrarLogin(0);
			
			}else{

				$_SESSION['intentos']++;
				if ($_SESSION['intentos']>3 && $this->verificarStatus()) {
					
					

					
					
					$this->getObjUser()->setId($this->buscarUser()['ID']);
					$this->cambiarStatus('inactivo');
					$this->mostrarLogin(4);
					
				}else{
					if (!$this->verificarStatus()) {
						$this->cerrarSesion(4);
						
					}
				}

				


				$this->mostrarLogin(0);
			}
		}

//Verifica el estatus de un usuario
		private function verificarStatus(){
			if ($this->buscarUser()['estatus']==='inactivo')
			{
				return false;
			}else
			{
				return true;
			}
		}

		//permite mostrar el login recibe el parametro evento para definir el tipo de evento que se ejecutara en el login
		private  function mostrarLogin($evento)
		{
			if ($evento===null) {
				
				header("Location: \Programas\Metro\index.php");
				exit;
				
			}else{
				
				if ($_SESSION['intentos']<=3) {

					header("Location: \Programas\Metro\index.php?e=$evento&i=".$_SESSION['intentos']);
				
				exit;

						
					}	
				header("Location: \Programas\Metro\index.php?e=$evento");
				
				exit;
			}
			
		}

		//cierra la sesion y redirige al login 
		public function cerrarSesion($accion=null)
		{
			//echo "cerrando sesion<br>";
			session_unset();
			session_destroy();
			$this->getConex()->cerraConexion();
			$this->mostrarLogin($accion);
		}

		//comprueba el rol con el que se intenta iniciar sesion
		private function ComprobarRol()
		{
			if ($_SESSION['rol']=="Admin")
			{
				
				 header("Location: ../Fallas/Controller/controlador_read_falla.php");
				 exit();
				
				

				
			}
			else
			{
				if ($_SESSION['rol']=="Trabajador") 
				{
					 header("Location: ../Fallas/Controller/controlador_read_falla.php");
				 exit();
					
					
				}else
				{
					
				}
			}
		}

		//permite comprobar si el usuario ya registro sus preguntas de seguridad 
		private function comprobarSegu()
		{
			if(!$this->getObjUser()->comprobarPregu()) 
			{
				
				
				$this->showFmrRegisPre();
			}else{
				$this->ComprobarRol();
			}
		}

		//permite mostrar el fromulario del registro de las preguntas de seguridad
		private function showFmrRegisPre()
		{   
			
			require_once"../Usuario/vista/usuario_preguntasRegis.php";
		}

		//obtine las prreguntas de la base de datos
		private function obtenerPreguntas()
		{

			return $this->getConex()->selectAll("usuario_preguntas");
		}

		//permite capturar los datos y luego los filtra con la funcion filitrar(), para luego llamar registrarPre()
		public function regis()
		{
			$this->setDato("p0",filtrar($_POST['p0'],"s"));
			$this->setDato("p1",filtrar($_POST['p1'],"s"));
			$this->setDato("p2",filtrar($_POST['p2'],"s"));
			$this->setDato("p3",filtrar($_POST['p3'],"s"));
			$this->setDato("p4",filtrar($_POST['p4'],"s"));
			$this->setDato("p5",filtrar($_POST['p5'],"s"));
			$this->setDato(0,filtrar($_POST['r0'],"s"));
			$this->setDato(1,filtrar($_POST['r1'],"s"));
			$this->setDato(2,filtrar($_POST['r2'],"s"));
			$this->setDato(3,filtrar($_POST['r3'],"s"));
			$this->setDato(4,filtrar($_POST['r4'],"s"));
			$this->setDato(5,filtrar($_POST['r5'],"s"));
			$this->setDato("id",$_POST['id']);
			$this->registrarPre();
		
		}

		//registraPre permite ecriptar las respuetas enviads por el usuario
		private function registrarPre()
		{
			try {
				if($this->getConex()->regisPre(hass($this->getDato(0)), hass($this->getDato(1)), hass($this->getDato(2)), hass($this->getDato(3)), hass($this->getDato(4)), hass($this->getDato(5)),$this->getDato("id")))
			{
				
				$this->regisPre();
				
				
			}

				
			} catch (Exception $e) {
				alerta('fracaso');
				$this->reset($this->getDato("id"));
				$this->regisLog("error",$e,"error al registrar metodos de recuperacion");
				$this->cerrarSesion("1");
			}
			

		}
//Guarda las respuestas del usuario en la bd
		private function regisPre(){
			
			try {

				for ($i=0; $i <=5  ; $i++) { 
				$this->getConex()->regisPreguntas($this->getDato("id"),$i,ecripPregunta($this->getDato("p".$i)));
			}
				
				alerta('exito');
				$this->ComprobarRol();
				
			} catch (Exception $e) {
				
				$this->regisLog("error",$e,"error al registrar metodos de recuperacion");
				$this->cerrarSesion("1");
			}

		}


	

		//permite mostrar un formulario de erecupercaion de contraseña 
		public function FrmRecupeUser()
		{
			require_once"../Usuario/vista/usuario_frmRecuperAcion.php";
		}

		//permite buscar al usuario por medio de su nombre de usuario 
		public function findUser()
		{
			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setUserName(filtrar($_POST['userName'],"s"));
			$this->setDatos($this->buscarUser());
			$this->verificarUser();
		}

		//pemite verificar si exite usu usuario
		private function verificarUser()
		{
			$this->getObjUser()->setId($this->getDato('ID'));
			if ($this->getDatos()!=null && $this->getObjUser()->comprobarPregu()) 
			{
				
				$this->showFrmRecupePre();
			}else
			{
				
				
				$this->mostrarLogin(2);
			}
		}

		
		
		//permite mostrar el formulario de recuperacion con las tres preguntas que el usuario debe reporder 
		private function showFrmRecupePre()
		{
			$preguntas = desenCriptPreguntas($this->getConex()->selecId("usuario_preguntas",$this->getDato('ID')));
			$x=obtenerPreguntasaEleatoreas($this->getConex()->selecId("usuario_preguntas",$this->getDato('ID')),3);
		
			include_once"../Usuario/vista/usuario_frmPreguntas.php";
		}

		//permite capturar las pregustas enviadas por el fomulario y luego las filtra y llama al metodo recuperar
		public function capturaPre()
		{
			
			$this->setDato("0",filtrar($_POST['1'],"s"));
			$this->setDato("1",filtrar($_POST['2'],"s"));
			$this->setDato("2",filtrar($_POST['3'],"s"));
			$this->setDato("id",filtrar($_POST['id'],"s"));
			$this->setDato("id0",filtrar($_POST['id1'],"s"));
			$this->setDato("id1",filtrar($_POST['id2'],"s"));
			$this->setDato("id2",filtrar($_POST['id3'],"s"));
			
			$this->recuperar();
		}

		//este metodo permite verificar las repuestas previamente almacenadas por el usuario con las repuestas envida previamente
		private function verificarPre()
		{
			$count=0;
			$auxi= new UsuarioControlador();
			$auxi->setDatos($this->getConex()->selecId("usuario_respuesta",$this->getDato('id')));
			// erreglo con las preguntas registradas var_dump($auxi->getDatos());
			// arreglo con las respuestas del usuario var_dump($this->getDatos());




			for ($i=0; $i < 3 ; $i++) { 
					$p="p".$this->getDato("id".$i);
					
					if (password_verify($this->getDato("$i"),$auxi->getDato('0')["$p"])) 
					{
						$count++;
					} 

			}
			
			
			//preguntamos si existen solo tres aciertos y se retorna verdadero 

			if ($count==3) {
				return true;
			}else
			{
				return false;
			}
			
			
		}
		//Permite capturar los campos para filtrar
		public function capturaCampos(){
			$this->filtrarUsuarios($_GET['Nombre'],$_GET['Apellidos']);
		}

		//Filtra y muestra los registros obtenidos
		private function filtrarUsuarios($nombre=null,$apellido=null){
			if ($nombre==null && $apellido==null) {
				//$this->index();
			}

			$usuarios = $this->getConex()->filtrarUsuarios($nombre,$apellido);
				require_once"../Usuario/vista/usuario_lista.php";

			
			
		}

		//este metodo muetra un formulario para introducir la contraseña nueva 
		private function recuperar()
		{
			if($this->verificarPre())
			{
				include_once"../Usuario/vista/usuario_frmclaveNueva.php";
			}else
			{

				
				$this->mostrarLogin(3);

			}
		}
		

		//este metodo permite actualizar la contraseña par alamacenarla
		public function actualizarPass()
		{
			//echo "estas en actualizar pas<br>";
			$this->setDato('id',$_POST['id']);
			$this->setDato('pass',filtrar($_POST['pass'],"s"));
			$this->updatePass();
		}
		

		//permite actualizar el nuevo pass
		private function updatePass()
		{
			
			if($this->getConex()->updatePass(hass($this->getDato('pass')),$this->getDato('id')))
			{
				//echo "clave actualizada exitosamente";
				$this->mostrarLogin(null);

			}else
			{

				//echo "error al actualizar la clave<br>";
				$this->mostrarLogin(1);
			}
		}



		//Permite ccambiar el estatus de un usuario a inactivo. Recibe el id correspondiente al usuario
		public function susp($id)
		{
			if ($_SESSION['rol'] !='Admin') {
				
				
				alerta('acceso');
				$this->index();
				exit;
			}

			if ($_SESSION['id']==$id) {
				alerta('acceso');
				$this->index();
				exit;
			}

			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setId($id);
			$this->cambiarStatus('inactivo');
			$this->index();

			
		}

		public function elimi($id){
			if ($_SESSION['rol'] !='Admin') {
				
				alerta('acceso');
				$this->index();
				exit;
			}
			
			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setId($id);
			$this->eliminarUser();
			$this->index();
		}


		private function eliminarUser(){

			if ($this->getObjUser()->verificarInfo()) {
				alerta('info');
				$this->index();
				
			}else
			{
				
				if($this->getObjUser()->eliminar()){
					alerta('exito');
					$this->regisLog("Eliminar");
					$this->index();
				}
				
			}
		}

		public function habi($id){

			if ($_SESSION['rol'] !='Admin')
			{
				
				alerta('acceso');
				$this->index();
				exit;
			}

			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setId($id);
			$this->cambiarStatus('activo');
			$this->index();
		}

		//Metodo que cambia el estatus de un usuario. 

		private function cambiarStatus($tipo)
		{

			try {
				if($this->getObjUser()->changeStatus($tipo))
				{

					$this->regisLog("changeStatus",$tipo);
					alerta("exito");


				}
				
			} catch (Exception $e) {
				alerta('fracaso');
				$this->regisLog("error",$e,"error al cambiar el estatus");

			}
			
				
			
			
		}

		
		


		//Permite reiniciar las preguntas de seguridad de un usuario espesifico
		public function reset($id)
		{
			if ($_SESSION['rol'] !='Admin') {
				
				
				alerta('acceso');
				$this->index();
				exit;
			}

			$this->setObjUser(new UsuarioModelo());
			$this->getObjUser()->setId($id);
			$this->resetPreguntas();
		}

		//Verifica si se han reseteado las preguntas y arroja una alerta correspondiente 
		//Redirige al metodo lista de usuarios
		private function resetPreguntas(){

			try {
				if ($this->getObjUser()->resetPregu()) 
				{

					alerta('exito');
					$this->regisLog("reset");
					$this->index();

				}
				
			} catch (Exception $e) {
				alerta('fracaso');
				$this->regisLog("error",$e,"Error al restablecer metodos de seguridad");
				$this->index();
				
			}
			
		}


		


		
		

		// Metodo que detecta que tipo de accion se quiere registrar
		private function regisLog($caso,$estatus=null,$mensaje=null){
			switch ($caso) {
				case 'Registro':
				$this->log->evento(
					"Se ha registrado un nuevo usuario",[
						'usuario'=> $this->getObjUser()->getCarnet(),
						'nombres'=>$this->getObjUser()->getNombres(),
						'apellidos'=>$this->getObjUser()->getApellidos(),
						'rol'=>$this->getObjUser()->getRol()
					],$_SESSION['id']);

				break;
				case 'Actualizar':
				$this->log->evento(
					"Se Actualizo los datos de un usuario",[
						'usuario'=> $this->getObjUser()->getCarnet(),
						'nombres'=>$this->getObjUser()->getNombres(),
						'apellidos'=>$this->getObjUser()->getApellidos(),
						'rol'=>$this->getObjUser()->getRol()
					],$_SESSION['id']);
				break;

				case 'Eliminar':
				$this->log->evento(
					"Se elimino un usuario",[
						'usuario'=> $this->getObjUser()->getId()
					],$_SESSION['id']);
				break;
				case 'changeStatus':
				$this->log->evento(
					"Se cambio el estatus de un usuario",[
						'usuario'=> $this->getObjUser()->getId(),
						'estatus'=>$estatus
					],$_SESSION['id']);

				break;
				case 'login':
				$this->log->evento(
					"Un usuario se ha logueado",[
						'usuario'=> $_SESSION['userName'],
					],$_SESSION['id']);
				
				break;
				case 'error':
				$this->log->error(
					$mensaje,[
						"error"=>$estatus->getMessage()],
						$_SESSION['id']
					);
				break;
				case 'reset':
				$this->log->evento(
					"Se han restablecido los metodos de recuperacion",[
						'usuario'=> $this->getObjUser()->getId(),
					],$_SESSION['id']);
				break;
				
				default:
				break;
			}

		}


		
	}//fin de la clase








?>