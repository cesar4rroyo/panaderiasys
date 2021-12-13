<?php
session_start();
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=SISREST-1.0-BACKUP-RESTAURANTE-".$_SESSION['R_NombreEmpresa']."-".$_SESSION['R_NombreSucursal'].".xls");
header("Pragma: no-cache");
header("Expires: 0");

require("../modelo/clsTabla.php");
$id_clase = 35;
$id_tabla = 35;
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
//echo $id_clase;
try{
$objMantenimiento = new clsTabla($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rst = $objMantenimiento->consultarTablas(0,"%%",'S','B');
if(is_string($rst)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
}else{
	$dato = $rst;
}
?>
<table border="1">
<?php
echo "<tr><th>SISREST Dump</th></tr>";
echo "<tr><th>Version: ".$_SESSION['R_Version']."</th></tr>";
date_default_timezone_set('America/Lima');
echo "<tr><th>Fecha y hora: ".date("d/m/Y H:i:s")."</th></tr>";
echo "<tr><th>Empresa: ".$_SESSION['R_IdEmpresa']." ".$_SESSION['R_NombreEmpresa']."</th></tr>";
echo "<tr><th>Sucursal: ".$_SESSION['R_IdSucursal']." ".$_SESSION['R_NombreSucursal']."</th></tr>";
echo "<tr><td></td></tr>";
while($value=$dato->fetch()){
$c=0;
?>
<?php
	echo "<tr><th>".'TABLA: '.$value['descripcion']."</th></tr>";
	$rst = $objMantenimiento->obtenerDatosTabla($value['descripcion'],$id_cliente,$id_empresa);
	while($registros=$rst->fetch()){
		//print_r($registros);
		if($c==0){
			echo "<tr>";
			foreach($registros as $campo => $valor){
				if(is_string($campo)){
				echo "<th>".$campo."</th>";
				}
			}
			echo "</tr>";
		}
		echo "<tr>";
		foreach($registros as $campo => $valor){
			if(is_string($campo)){
			echo "<td>".$valor."</td>";
			}
		}
		echo "</tr>";
		$c++;
	}
?>
<?php }?>
</table>