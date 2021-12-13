<?php
$gTipoBD = 1;
//GMC: 19/03/2011 : Agrege parametro onchange
function genera_cboGeneral($idTabla,$tabla,$seleccionado,$disabled='',$obj=null,$onchange='',$todos='',$valida='')
{
	$consulta = $obj->consultarCombo($idTabla, 2);
	if(isset($onchange)) $onchange="onChange='".$onchange."'";
	echo "<select name='cbo".$tabla."' id='cbo".$tabla."' ".$disabled." ".$onchange." title='".$valida."'>";
	if($todos!='') echo "<option value='0'>".$todos."</option>";
	while($registro=$consulta->fetch())
	{
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected";
		echo "<option value='".$registro[0]."' ".$seleccionar.">".umill($registro[1])."</option>";
	}
	echo "</select>";
}

function genera_cboGeneralSQL($sql,$tabla,$seleccionado,$disabled='',$obj=null,$onchange='',$todos='',$valida='')
{
	$consulta = $obj->obtenerDataSQL($sql);
	if(isset($onchange)) $onchange="onChange='".$onchange."'";
	echo "<select name='cbo".$tabla."' id='cbo".$tabla."' ".$disabled." ".$onchange." title='".$valida."'>";
	if($todos!='') echo "<option value='0'>".$todos."</option>";
	while($registro=$consulta->fetch())
	{
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected";
		echo "<option value='".$registro[0]."' ".$seleccionar.">".umill(str_replace(' ','&nbsp;',$registro[1]))."</option>";
	}
	echo "</select>";
}

function genera_cboGeneralFun($funcion,$tabla,$seleccionado,$disabled='',$obj=null,$onchange='',$todos='',$valida='')
{
	eval("\$consulta =\$obj->$funcion;");
	if(isset($onchange)) $onchange="onChange='".$onchange."'";
	echo "<select name='cbo".$tabla."' id='cbo".$tabla."' ".$disabled." ".$onchange." title='".$valida."'>";
	if($todos!='') echo "<option value='0'>".$todos."</option>";
	while($registro=$consulta->fetch())
	{
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected";
		echo "<option value='".$registro[0]."' ".$seleccionar.">".umill($registro[1])."</option>";
	}
	echo "</select>";
}

//retorna el idsucursal-id
function genera_cboGeneralFun2($funcion,$tabla,$seleccionado,$disabled='',$obj=null,$onchange='',$todos='',$valida='')
{
	eval("\$consulta =\$obj->$funcion;");
	if(isset($onchange)) $onchange="onChange='".$onchange."'";
	echo "<select name='cbo".$tabla."' id='cbo".$tabla."' ".$disabled." ".$onchange." title='".$valida."'>";
	if($todos!='') echo "<option value='0'>".$todos."</option>";
	while($registro=$consulta->fetch())
	{
		$seleccionar="";
		if($registro[0]."-".$registro[1]==$seleccionado) $seleccionar="selected";
		echo "<option value='".$registro[0]."-".$registro[1]."' ".$seleccionar.">".umill($registro[2])."</option>";
	}
	echo "</select>";
}

function genera_listGeneralFun($funcion,$tabla,$obj=null)
{
	eval("\$consulta =\$obj->$funcion;");
	echo "<ul style='float:left'>";
	while($registro=$consulta->fetch())
	{
		echo "<li><input type='button' value='".umill($registro[2])."'></li>";
	}
	echo "</ul>";
}

function genera_bloqueNumerico($class,$campo,$ingresar,$enviar='',$value=''){
    $registro= '<table align="center" class="'.$class.'">';
    $cont=1;
    while($cont<10){
        if(($cont-1)%3==0){
            $registro.="<tr>";
        }
        $registro.='<td class="'.$class.'"><input type="button" value="'.$cont.'"  class="'.$class.'" onclick="'.$ingresar.'(this.value)"/></td>';
        if($cont%3==0)
        $registro.="</tr>";
        $cont++;
    }
    $registro.='<tr>
                    <td align="center" class="'.$class.'"><a href="#" onclick="'.$enviar.'('."$value".')"><img  src="img/activar.png" height="50px" width="50px" class="'.$class.'" /></a></td>
                    <td class="'.$class.'"><input class="'.$class.'" type="button" value="'."0".'" onclick="'.$ingresar.'(this.value)"/></td>
                    <td align="center" class="'.$class.'"><a href="#" onclick="var campo = document.getElementById(\'txtCampo\').value; document.getElementById(campo).value='."''".'"><img src="img/b_drop.png" height="50px" width="50px" class="'.$class.'" /></a></td>
                </tr>';
    $registro.="</tr></table>";
    echo $registro;
}
?>