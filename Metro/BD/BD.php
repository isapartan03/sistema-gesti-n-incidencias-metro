<?php
class BD{

	private $server = "localhost";
	private $user = "root";
	private $password = "";
	private $bd = "new";
	private $conex;

	function __construct($servidor=null, $usuario=null, $clave=null,$bd=null)
	{
		if ($servidor!=null) {$this->setServer($servidor);}
		if ($usuario!=null) {$this->setUser($usuario);}
		if ($clave!=null) {$this->setPassword($clave);}
		if ($bd!=null) {$this->setBD($bd);}
		$this->setConex(NULL);
		$this->establecerConexion();
	}

		//geters y seters

	public function setServer($servidor){$this->servidor=$servidor;}
	public function setUser($usuario){$this->usuario=$usuario;}
	public function setPassword($clave){$this->clave=$clave;}
	public function setBD($bd){$this->bd=$bd;}
	public function setConex($Conex){$this->conex=$Conex;}

	public function getServer(){return $this->servidor;}
	public function getUser(){return $this->usuario;}
	public function getPassword(){return $this->clave;}
	public function getBD(){return $this->bd;}
	public function getConex(){return $this->conex;}

		//establece la conexion  
	public  function establecerConexion()
	{
		$this->setConex(new mysqli($this->server,$this->user,$this->password,$this->bd));
		if (!$this->conex) 
		{
			die("ERROR de conexion:  ");
		}
	}


	public  function cerraConexion()
	{
		$this->conex->close();
	}

	

	public  function selectAll($tabla)
	{
		$sql="SELECT * FROM $tabla;";
		$result=$this->conex->query($sql);
		if (!$result) 
		{
			return false;
		}else
		{
			
			while ($fila=$result->fetch_assoc()) 
			{
				$array[]=$fila;
			}
			return $array;
		}
	}

	public  function selecId($tabla,$id)
	{
		
		$sql="SELECT * FROM $tabla WHERE id='$id';";
		$result=$this->conex->query($sql);
		if (!$result) 
		{ 
			
			return false;
		}else
		{ 

			while ($fila=$result->fetch_assoc()) 
			{
				$array[]=$fila;
			}
			

			return $array;
		}

	}

	public  function selecBy($columna,$value,$tabla)
	{
		$sql="SELECT * FROM $tabla WHERE $columna ='$value';";
		$result=$this->conex->query($sql);
		if (!$result) 
		{
			return null;
		}else
		{
			if ($result->num_rows==1) 
			{
				while ($fila=$result->fetch_assoc()) 
				{
					$array=$fila;

				}
				return $array;
				
			}else
			{
				return null;
			}
			
			
		}
	}

	public function filtrarUsuarios($nombre,$apellidos){
		$sql="SELECT p.nombres, p.apellidos, u.rol, u.ID, u.estatus  FROM persona p INNER JOIN  usuario u ON u.Carnet=p.carnet WHERE 1=1";
		$params = [];
    $types = "";
		 if ($nombre !== null) {
        $sql .= " AND p.nombres LIKE  ?";
        $params[] = '%' . $nombre . '%';
        $types .= "s";
    }

    if ($apellidos !== null) {
        $sql .= " AND p.apellidos LIKE  ?";
        $params[] = '%' . $apellidos . '%';
        $types .= "s";
    }

     $stmt = $this->conex->prepare($sql);
if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
	}


	public  function changeStatus($tabla,$estatus,$id)
	{

		$sql="UPDATE $tabla SET estatus = ? WHERE id = ?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("si", $estatus,$id);
		
		if($stmt->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
		
		
		return true;

	}

	public  function selecDatosPersona($id)
	{
		
		$sql="SELECT p.*, u.ID, u.Rol FROM usuario u INNER JOIN persona p ON u.Carnet=p.carnet WHERE p.Carnet =?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result=$stmt->get_result();
		if ($result->num_rows==1) 
		{
			
			
			return $result->fetch_assoc();
			
		}

		
	}


	public  function verifCredenciales($usuario)
	{
		$sql="SELECT * FROM usuario WHERE usuario=?";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("s", $usuario);
		$stmt->execute();
		$result=$stmt->get_result();

		if ($result->num_rows==1) 
		{
			
			
			
			return $result->fetch_assoc()['clave'];
		}
		
	}

	public  function regisDatosPerso($id,$nombres,$apellidos)
	{
		
		$sql="INSERT INTO persona(nombres,apellidos,carnet) VALUES('$nombres','$apellidos','$id');";
		$result=$this->conex->query($sql);
		if (!$result) 
		{
			
			return false;
		}else
		{
			
			return true;
		}
	}




	public  function registrarUsuario($carnet,$usuario,$clave,$rol)
	{
		
		$sql="INSERT INTO usuario(Carnet,UserName,Password,Rol) VALUES('$carnet','$usuario','$clave','$rol');";
		$result=$this->conex->query($sql);
		if (!$result) 
		{
			return false;
		}else
		{
			
			return true;
			
		}
	}


	public  function actualiDatos($id,$nombres,$apellidos)
	{
		$sql="UPDATE persona SET nombres = ?, apellidos = ? WHERE CARNET = ?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("sss", $nombres,$apellidos,$id);
		if(!$stmt->execute())
		{
			
			return false;
		}
		else
		{
			
			return true;
		}
	}

	public  function obtenerLista()
	{
		
		$sql="SELECT p.nombres, p.apellidos, u.rol, u.ID, u.estatus  FROM persona p INNER JOIN  usuario u ON u.Carnet=p.carnet ORDER BY p.apellidos ASC;";
		$result=$this->conex->query($sql);
		if ($result->num_rows<=0) 
		{
			return null;
		}else
		{
			
			while ($fila=$result->fetch_assoc()) 
			{
				$array[]=$fila;
			}
			
			return $array;
			
		}

	}

	public  function verifiPre($p1,$p2,$p3,$id)
	{
		$sql="SELECT id FROM usuario_respuesta WHERE id = ? AND 1 = ? AND 2 = ? AND 3 = ?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("sssi",$p1,$p2,$p3,$id);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}

	}

	public  function regisPre($p1,$p2,$p3,$p4,$p5,$p6,$id)
	{
		$sql="INSERT INTO usuario_respuesta(id,p0,p1,p2,p3,p4,p5)VALUES(?,?,?,?,?,?,?);";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("issssss",$id,$p1,$p2,$p3,$p4,$p5,$p6);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}

	}


	public  function comprobarPregSeguridad($id)
	{
		$sql="SELECT id FROM usuario_respuesta WHERE ID = '$id';";
		$result=$this->conex->query($sql);
		
		if ($result->num_rows==1) 
		{ 
			
			return true;
		}else
		{ 
		
			return false;
		}

	}

	public function actualizarRol($rol,$id){
		$sql="UPDATE usuario SET rol = ? WHERE Carnet = ?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("si",$rol,$id);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}
		
		
	}
	


	
	public  function updatePass($pass,$id)
	{
		$sql="UPDATE usuario SET Password = ? WHERE id = ?;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("si",$pass,$id);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}
	}


	public  function comprobarInfo($id,$tabla)
	{
		$sql="SELECT EXISTS(SELECT 1 FROM $tabla WHERE ID_Usuario = ?) AS existe;";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$result=$stmt->get_result();
		$row=$result->fetch_assoc();
		if ($row['existe']) {
			return true;
		}else{
			return false;
		}
	}

	public function resetearRespuestas($id)
	{
		$sql="DELETE FROM usuario_respuesta WHERE id = ?;";
		$stmt = $this->conex->prepare($sql);
		$stmt->bind_param("i",$id);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}
	}


	public function resetearPreguntas($id){
		$sql="DELETE FROM usuario_preguntas WHERE id = ?;";
		$stmt = $this->conex->prepare($sql);
		$stmt->bind_param("i",$id);
		if (!$stmt->execute()) 
		{
			return false;
		}else
		{
			return true;
		}
	}
//Registra las preguntas personalizdas 
	public  function regisPreguntas($id,$idPre,$pre)
	{
		$sql="INSERT INTO usuario_preguntas(id,IdPregunta,value)VALUES(?,?,?);";
		$stmt=$this->conex->prepare($sql);
		$stmt->bind_param("iss",$id,$idPre,$pre);
		if (!$stmt->execute()) 
		{
			
			return false;
		}else
		{
			return true;
		}

	}
//elimina un usuari de la tabla 
	public  function eliminarUser($id){
		$sql="DELETE FROM usuario WHERE ID = ?;";
		$stmt = $this->conex->prepare($sql);
		$stmt->bind_param("i",$id);
		if (!$stmt->execute()) {
			return false;
		}else
		{
			return true;
		}
	}

	
//registra los evenetos del sistema en la tabla

	public function log( $tipo,  $mensaje,  $contexto = [],  $idUsuario = null) {
		$contexto=json_encode($contexto);
		$sql = "INSERT INTO sistema_eventos (tipo, mensaje, contexto, idUsuario) VALUES (?,?,?,?);";
		$stmt = $this->conex->prepare($sql);
		$stmt->bind_param("sssi",$tipo,$mensaje,$contexto,$idUsuario);
		if (!$stmt->execute()) {
			return false; 
		}else{
			return true;
		}
	}

	















	}//fin de la clase


?>