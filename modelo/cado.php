<?php session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');window.open('cerrarSesion.php','_self');</script>";
	exit();
}
//INICIO: VERIFICA INACTIVIDAD
date_default_timezone_set('America/Lima');
$_SESSION['R_FechaProceso'] = date("d/m/Y");
if(!isset($_SESSION['R_UltimoAcceso'])){
	$_SESSION['R_UltimoAcceso'] = date("Y-n-j H:i:s");
}
/*
$segundos_transcurridos=strtotime(date("Y-n-j H:i:s"))-strtotime($_SESSION['R_UltimoAcceso']);
if($segundos_transcurridos>=$_SESSION['R_Inactividad'] and $_SESSION['R_Inactividad']>0){
    if($_SESSION["R_Formulario"]!="Mozo"){
	    echo "<script>alert('Se cerro la sesion por inactividad');window.open('cerrarSesion.php','_self');</script>";
	    exit();
    }else {
        echo "<script>alert('Se cerro la sesion por inactividad');window.open('cerrarSesion.php?Origen=Mozo','_self');</script>";
	    //exit();    
    }
}else{
	$_SESSION['R_UltimoAcceso'] = date("Y-n-j H:i:s");
}
*/
//FIN: VERIFICA INACTIVIDAD
$ggTipoBD=3;
function umill($valor)
{
	global $ggTipoBD;
	if($ggTipoBD==1){
		$valor = utf8_encode($valor);
		/*$valor = str_replace("\\\\" ,"\\", $valor);
		$valor = str_replace("\\\"" ,"\"", $valor);
		$valor = str_replace("\'" ,"''", $valor);	*/
		return $valor;
	}elseif($ggTipoBD==2){
		return utf8_encode($valor);
	}else{
		return $valor;
	}
}
function umillmain($valor)
{
	global $ggTipoBD;
	if($ggTipoBD==1){
		$valor = utf8_encode($valor);
		/*$valor = str_replace("\\\\" ,"\\", $valor);
		$valor = str_replace("\\\"" ,"\"", $valor);
		$valor = str_replace("\'" ,"''", $valor);	*/
		return $valor;
	}elseif($ggTipoBD==2){
		return utf8_encode($valor);
	}else{
		return utf8_decode($valor);
	}
}
function cambiaHTML($valor)
{
	global $ggTipoBD;
	if($ggTipoBD==1){
		$valor = utf8_encode($valor);
		/*$valor = str_replace("\\\\" ,"\\", $valor);
		$valor = str_replace("\\\"" ,"\"", $valor);
		$valor = str_replace("\'" ,"''", $valor);	*/
		return $valor;
	}elseif($ggTipoBD==2){
		return utf8_encode($valor);
	}else{
		$valor = str_replace("&aacute;" ,"�", $valor);
		$valor = str_replace("&eacute;" ,"�", $valor);
		$valor = str_replace("&iacute;" ,"�", $valor);
		$valor = str_replace("&oacute;" ,"�", $valor);
		$valor = str_replace("&uacute;" ,"�", $valor);
		$valor = str_replace("&uacute;" ,"�", $valor);
		$valor = str_replace("&ntilde;" ,"�", $valor);
		$valor = str_replace("&nbsp;" ," ", $valor);
		return $valor;
	}
}
require_once('pdo/PDO.class.php');
//Clase para acceso a datos
class clsAccesoDatos{      
	//Codigo de Tabla
	public $gIdTabla;
	//Codigo de Sucursal
	public $gIdSucursal;
	// Total de Paginas
	public $gNroPaginas;
	// Total de Registros
	public $gNroRegistros;
	// Pagina que se muestra
	public $gPagActual;    
	// Conexion BD
	private $gCnx;
	// Para ejecutar procedures
	public $gStmt;
	
	public $gError;
	public $gMsg;

	// Nro de Registro a Mostrar
	private $gNumReg = 20;  

	//Servidor de Base de Datos
	private $gServidor = "localhost";
	//Nombre de Base de Datos
	private $gBaseDatos = "bdpanaderia";
	//Nombre de Usuario
	//Tipo de Base Datos
	private $gTipoBD = 3; //1=SQLSERVER, 2=MYSQL, 3=POSTGRESQL
	public $gTipoConex = 1; //1=PDO, 2 = PDOSICA
	
	
	// Constructor de la clase
	function __construct($user, $pass){
		global $ggTipoBD;
		$this->gTipoBD=$ggTipoBD;
		if($this->gTipoBD==1){
			$this->gServidor = "GEYNEN-PC";
		}
		if($this->gTipoBD==2){
			$this->gServidor = "localhost";
			$user='root';
			$pass='root';
		}
		if($this->gTipoBD==3){
			$this->gServidor = "localhost";
			$user='postgres';
			$pass='Garzasoft-Test';
		}
		// Crea una Conexion SQLSERVER 2000
		//try {
			if($this->gTipoBD==1){
				$this->gCnx = new PDO("mssql:host=".$this->gServidor.";dbname=".$this->gBaseDatos,$user,$pass);
			}
			if($this->gTipoBD==2){
				$this->gCnx = new PDO("mysql:host=".$this->gServidor.";port=3306;dbname=".$this->gBaseDatos,$user,$pass);
			}
			if($this->gTipoBD==3){
				if($this->gTipoConex==1){
					$this->gCnx = new PDO("pgsql:host=".$this->gServidor.";port=5432;dbname=".$this->gBaseDatos,$user,$pass);
				}else{
					$this->gCnx = new PDOSICA("pgsql:host=".$this->gServidor.";port=5432;dbname=".$this->gBaseDatos,$user,$pass);
					//echo "Inicia::....".$this->gCnx->errorInfo();
				}
			}
			$this->gCnx->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		//} catch (PDOException $e) {
			//echo "Error:\n" . $e->getMessage();
		//}

		//mysql_query("SET NAMES 'utf8'");
	}
	
	// Destructor de la clase
	function __destruct(){
		//Cierra la Conexion BD
		try{
			unset($this->gCnx);
		} catch (PDOException $e) {
			return "Error:\n" . $e->getMessage();
		}
	}
	
	function getTipoBD(){
		return $this->gTipoBD;
	}
	
	function obtenerDataSP($sql)
 	{
		if($this->gTipoBD>1){
			$sql = substr($sql,8,strlen($sql)-8);
			$valor = strpos($sql,' ');
			return  $this->obtenerDataSQL("SELECT * FROM ".substr($sql,0,$valor)."(".substr($sql,$valor, strlen($sql)-$valor).")");
		}else{
			$this->gStmt = $this->gCnx->prepare($sql);
			$this->gStmt->execute();
			if($this->gStmt->errorCode()=="00000"){
				$this->gError = $this->gStmt->errorInfo();
				return $this->gStmt;
			}else{
				$this->gError = $this->gStmt->errorInfo();
				return $this->gError[2];
			}		
		}
 	}

	function obtenerDataSQL($sql)
 	{
		if($this->gTipoBD==2){
			$sql = $this->millSQL($sql);
		}
		if($this->gTipoBD==3){
			$sql = str_replace('like','ilike',$sql);
			$sql = str_replace('LIKE','ILIKE',$sql);
		}
		//echo $sql;
		$rst = $this->gCnx->query($sql);	
		if($this->gCnx->errorCode()=="00000"){
			return $rst;
		}else{
			$this->gError = $this->gCnx->errorInfo();
			return $this->gError[2];
		}
 	}

	function ejecutarSP($sql)
 	{
		if($this->gTipoBD==2){
			$sql = substr($sql,8,strlen($sql)-8);
			$valor = strpos($sql,' ');
			return  $this->ejecutarSQL("CALL ".substr($sql,0,$valor)."(".substr($sql,$valor, strlen($sql)-$valor).")");
		}elseif($this->gTipoBD==3){
			$sql = substr($sql,8,strlen($sql)-8);
			$valor = strpos($sql,' ');
			return  $this->ejecutarSQL("SELECT ".substr($sql,0,$valor)."(".substr($sql,$valor, strlen($sql)-$valor).")");
		}else{
			$this->gStmt = $this->gCnx->prepare($sql);
			$this->gStmt->execute();
			if($this->gStmt->errorCode()=="00000"){
				$this->gMsg = "Operaci�n realizada con �xito";
				return 0;
			}else{
				$this->gError = $sql.$this->gStmt->errorInfo();
				return 1;
			}
		}
 	}

	function ejecutarSQL($sql)
 	{
		$this->gCnx->query($sql);	
		if($this->gCnx->errorCode()=="00000"){
			$this->gMsg = "Operaci�n realizada con �xito";
			return 0;
		}else{
			$this->gError = $this->gCnx->errorInfo();
			return 1;
		}
 	}
		
	function obtenerTabla()
 	{
		if($this->gTipoBD==1){
   		$sql = "execute up_BuscarSecuenciaCodigo 1, 1, '1',1,".$this->gIdTabla.", ".$this->gIdSucursal.", '%%'";
		return $this->obtenerDataSP($sql);
		}else{
   		$sql = "SELECT IdTabla, IdSucursal, Descripcion, descripcionmant FROM RelacionTablaSucursal WHERE 1=1 ";
		$sql = $sql." AND Estado = 'N' ";
		$sql = $sql." AND IdTabla = ".$this->gIdTabla;
		$sql = $sql." AND IdSucursal = ".$this->gIdSucursal;
		return $this->obtenerDataSQL($sql);
		}
 	}

	function obtenerSecuencia()
 	{
   		$sql = "execute up_SecuenciaTabla ".$this->gIdTabla.", ".$this->gIdSucursal;
		return $this->obtenerDataSP($sql);
 	}
	
	function obtenerCampos()
 	{
		if($this->gTipoBD==1){
			$sql = "execute up_BuscarCampo 0, 1, '1',1,".$this->gIdTabla;
			return $this->obtenerDataSP($sql);
		}else{		
			$sql = "SELECT Campo.IdTabla, Campo.IdCampo, Campo.Descripcion, Campo.Comentario, Campo.Longitud
		FROM Campo WHERE 1=1 ";
			$sql = $sql." AND Campo.Estado = 'N' ";
			$sql = $sql." AND Campo.IdTabla = ".$this->gIdTabla;
			return $this->obtenerDataSQL($sql);
		}

 	}

	function obtenerCamposMostrar($tipo)
 	{
		if($this->gTipoBD==1){
   		$sql = "execute up_BuscarRelacionCampoMostrar ".$this->gIdTabla.", ".$this->gIdSucursal.", '".$tipo."'";
		return $this->obtenerDataSP($sql);
		}else{
   		$sql = "SELECT RelacionCampo.IdTabla, RelacionCampo.IdCampo, Campo.Descripcion, RelacionCampo.Descripcion as Comentario, RelacionCampo.Diccionario, longitud, validacion as Validar, msgvalidacion as MsgValidar, longitudreporte, alineacionreporte
FROM RelacionCampo
	INNER JOIN Campo ON RelacionCampo.IdTabla = Campo.IdTabla AND RelacionCampo.IdCampo = Campo.IdCampo
WHERE RelacionCampo.IdTabla = ".$this->gIdTabla." 
	AND RelacionCampo.Tipo = '".$tipo."'
	AND RelacionCampo.Estado = 'N'
	AND (RelacionCampo.IdSucursal = ".$this->gIdSucursal." OR RelacionCampo.IdSucursal = RelacionCampo.IdSucursal - ".$this->gIdSucursal.")
ORDER BY RelacionCampo.IdTabla, RelacionCampo.Tipo, RelacionCampo.Orden";
		return $this->obtenerDataSQL($sql);
		}
 	}
	
	function agregarRegistro($campo, $registro, $valor)
 	{
		//$valor=str_replace("\'" ,"''", $valor);
		//if($campo>1){$str="( ".$valor." ) u";}
   		$sql = "execute ".$str."up_AgregarTablaRegistro ".$this->gIdTabla.", ".$campo.", ".$registro.", '".($this->mill($valor))."', ".$this->gIdSucursal;
		return $this->ejecutarSP($sql).$sql;
 	}

	function modificarRegistro($campo, $registro, $valor)
 	{
		//$valor=str_replace("\'" ,"''", $valor);
   		$sql = "execute up_ModificarTablaRegistro ".$this->gIdTabla.", ".$campo.", ".$registro.", '".$this->mill($valor)."', ".$this->gIdSucursal;
		return $this->ejecutarSP($sql).$sql;
 	}
	
	function eliminarRegistro($id)
 	{
   		$sql = "execute up_EliminarTablaRegistro ".$this->gIdSucursal.", ".$this->gIdTabla.", ".$id;
		return $this->ejecutarSP($sql);
 	}
	
	function obtenerPermisos($wap='N')
 	{
		if($this->gTipoBD==1){
   		$sql = "execute up_BuscarPermisoUsuario ".$this->gIdSucursal.", ".$_SESSION["R_IdUsuario"];
		return $this->obtenerDataSP($sql);
		}else{
   		$sql = "SELECT permisousuario.idopcionmenu, opcionmenu.idtabla, opcionmenu.descripcion, opcionmenu.accion, opcionmenu.idmenuprincipal, menuprincipal.Descripcion as menuprincipal, menuprincipal.orden as ordenmenu, opcionmenu.idmodulo, modulo.Descripcion as modulo, modulo.orden as moduloorden, modulo.expandido, menuprincipal.expandido as menuexpandido
FROM PermisoUsuario 
	INNER JOIN OpcionMenu ON PermisoUsuario.IdOpcionMenu = OpcionMenu.IdOpcionMenu and OpcionMenu.estado='N'
	INNER JOIN MenuPrincipal ON OpcionMenu.IdMenuPrincipal = MenuPrincipal.IdMenuPrincipal
	INNER JOIN Modulo ON OpcionMenu.IdModulo = Modulo.IdModulo
WHERE OpcionMenu.wap like '%".$wap."' AND PermisoUsuario.IdSucursal = ".$this->gIdSucursal." 
	AND PermisoUsuario.IdPerfil IN (SELECT IdPerfil FROM Usuario WHERE IdUsuario  = ".$_SESSION["R_IdUsuario"]."  AND IdSucursal =  ".$this->gIdSucursal.")
ORDER BY modulo.orden, menuprincipal.Orden, opcionmenu.descripcion";
		return $this->obtenerDataSQL($sql);
		}
 	}

	function obtenerOperaciones()
 	{
		if($this->gTipoBD==1){
   		$sql = "execute up_RelacionOperacionPerfil ".$this->gIdTabla.",".$this->gIdSucursal.", ".$_SESSION["R_IdPerfil"];
		return $this->obtenerDataSP($sql);
		}else{
   		$sql = "SELECT RelacionOperacionPerfil.IdOperacion, RelacionOperacion.Descripcion, RelacionOperacion.Comentario, RelacionOperacion.Tipo, RelacionOperacion.Accion, RelacionOperacion.imagen, RelacionOperacion.versi
FROM RelacionOperacion
	INNER JOIN RelacionOperacionPerfil ON RelacionOperacionPerfil.IdTabla = RelacionOperacion.IdTabla AND RelacionOperacionPerfil.IdOperacion = RelacionOperacion.IdOperacion
	INNER JOIN RelacionOperacionCliente ON RelacionOperacionCliente.IdTabla = RelacionOperacionPerfil.IdTabla AND RelacionOperacionCliente.IdOperacion = RelacionOperacionPerfil.IdOperacion AND RelacionOperacionCliente.IdSucursal = RelacionOperacionPerfil.IdSucursal WHERE RelacionOperacionPerfil.IdTabla = ".$this->gIdTabla." 
	AND RelacionOperacionPerfil.IdSucursal = ".$this->gIdSucursal."
	AND RelacionOperacionPerfil.IdPerfil = ".$_SESSION["R_IdPerfil"]."
	AND RelacionOperacion.Estado = 'N'
ORDER BY RelacionOperacionCliente.orden";
        //echo $sql;
		return $this->obtenerDataSQL($sql);
		}
 	}
	
	function iniciarTransaccion()
 	{
		if($this->gTipoBD==2){
   		$this->ejecutarSQL("START TRANSACTION;");
		}else{
		$this->ejecutarSQL("BEGIN TRANSACTION");
		}
 	}
	
	function abortarTransaccion()
 	{
		if($this->gTipoBD==2){
   		$this->ejecutarSQL("ROLLBACK;");
		}else{
		$this->ejecutarSQL("ROLLBACK TRANSACTION");
		}
   		
 	}
	
	function finalizarTransaccion()
 	{
		if($this->gTipoBD==2){
   		$this->ejecutarSQL("COMMIT;");
		}else{
		$this->ejecutarSQL("COMMIT TRANSACTION");
		}
   		
 	}
	
	function ControlaTransaccion()
 	{
		$rst=$this->obtenerDataSQL("SELECT @@ERROR AS ERROR");
		$dato=$rst->fetchObject();
		if($dato->ERROR=='0'){
			$this->finalizarTransaccion();
			return "Guardado correctamente";
		}else{
			$this->abortarTransaccion();
			return "Fallo Transacci�n";
		}
 	}

	function miles($valor)
 	{
		$valor = str_replace("," ,"", $valor);
		$valor = str_replace(" " ,"", $valor);
		return $valor;
 	}
	
	function mill($valor)
 	{
		if($this->gTipoBD==1){
			$valor = utf8_decode($valor);
			$valor = str_replace("\\\\" ,"\\", $valor);
			$valor = str_replace("\\\"" ,"\"", $valor);
			$valor = str_replace("\'" ,"''", $valor);
			return $valor;
		}elseif($this->gTipoBD==2){
			return $valor;
		}else{
			$valor = str_replace("\'" ,"'", $valor);
			$valor = str_replace("'" ,"\'", $valor);
			return $valor;
		}
 	}
	
	function millSQL($sql)
 	{
		$data = explode("'",$sql);
		$con=count($data);
		for($i=0;$i<=$con;$i++){
			if($i%2==0){
				$data[$i] = strtolower($data[$i]);
			}else{
				$data[$i] = $data[$i];
			}
		}
		$sql = implode("'",$data);
		$sql = substr($sql,0,strlen($sql)-1);
		if($this->gTipoBD==2){
			$cadena1="";
			$cadena2="";
			$pos_ini = 0;
			$pos_fin = strpos($sql,"obtenertabla");
			while($pos_fin>0){
				$cadena1 = substr($sql,0,$pos_fin-1);
				$cadena2 = substr($sql,$pos_fin);
				$pos = strpos($cadena2,")");
				$convertir= substr($cadena2,0,$pos+1);
				$cadena2 = substr($cadena2,$pos+1);
				$sql = $cadena1.$this->obtenerTablaSql($convertir).$cadena2;
				$pos_fin = strpos($sql,"obtenertabla");
			}
			
		}
		return $sql;
	}
	
	function obtenerTablaSql($cadena)
 	{
		$pos = strpos($cadena,"(");
		$data = explode(",",substr($cadena,$pos+1,strlen($cadena)-$pos-2));
		$v1=$data[0];
		$v2=$data[1];
		$v3=$data[2];
		return " (SELECT IdRegistro, Registro 
	FROM Registro 
	WHERE IdTabla = $v1 AND IdCampo = $v2
	AND (IdSucursal = CASE WHEN EXISTS(SELECT * FROM Tabla WHERE IdTabla = $v1 AND Multiple = 'S') THEN $v3 ELSE 0 END OR IdSucursal = IdSucursal - CASE WHEN EXISTS(SELECT * FROM Tabla WHERE IdTabla = $v1 AND Multiple = 'S') THEN $v3 ELSE 0 END)) ";
	}
	function consultarCombo($idtabla, $idcampo){
   		$sql = "select IdRegistro, Registro from obtenerTabla(".$idtabla.",".$idcampo.",".$this->gIdSucursal.")";
		return $this->obtenerDataSQL($sql);
 	}
	
	function consultarComboGrado($idtabla, $idcampo){
   		$sql = "select IdRegistro, Registro from obtenerTabla(".$idtabla.",".$idcampo.",".$this->gIdSucursal.") as Tabla inner join AsignacionGrado on Tabla.IdRegistro = AsignacionGrado.IdGrado";
		return $this->obtenerDataSQL($sql);
 	}
}
?>