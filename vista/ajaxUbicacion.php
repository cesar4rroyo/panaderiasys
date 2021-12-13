<?php
session_start();
require("../modelo/clsUbicacion.php");
$action = $_POST["accion"];
$objUbicacion = new clsUbicacion($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$consulta = $objUbicacion->buscarUbicacionxCodigo($_POST["IdUbicacion"]);
$registro=$consulta->fetchObject();
$tc= $registro->totalcolumnas;
$tf= $registro->totalfilas;

if($action=="genera_cboColFila")
{
		$Columna="<table width=500><tr><td width=160>Columna&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><select name='cboColumna' id='cboColumna'>";
		if($consulta->rowCount()>0)
		{
			for($c=1 ; $c<=$tc ; $c++)
				{
					$Columna=$Columna."<option id=".$c." value=".$c.">".$c."</option>";
				}
		$Columna=$Columna."</select></td><tr>";
		$Columna=utf8_encode($Columna);
		echo $Columna;
		}


		$Fila="<tr><td>Fila &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><select name='cboFila' id='cboFila'>";
		if($consulta->rowCount()>0)
		{
			for($f=1 ; $f<=$tf ; $f++)
			{
				$Fila=$Fila."<option id=".$f." value=".$f.">".$f."</option>";
			}
		$Fila=$Fila."</select></td></tr></table>";
		$Fila=utf8_encode($Fila);
		echo $Fila;
	    }
}
?>