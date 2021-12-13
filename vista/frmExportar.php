<?php
require("../modelo/clsTabla.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
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

$rstTabla = $objMantenimiento->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}

$rst = $objMantenimiento->consultarTablas(0,"%%",'S','B');
if(is_string($rst)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
}else{
	$dato = $rst;
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
//<![CDATA[
    var bodyWidth=null; var bodyHeight=null;
    if (document.getElementById('textSQLDUMP')) {
        bodyWidth  = self.innerWidth;
        bodyHeight = self.innerHeight;
        if (!bodyWidth && !bodyHeight) {
            if (document.compatMode && document.compatMode == "BackCompat") {
                bodyWidth  = document.body.clientWidth;
                bodyHeight = document.body.clientHeight;
            } else if (document.compatMode && document.compatMode == "CSS1Compat") {
                bodyWidth  = document.documentElement.clientWidth;
                bodyHeight = document.documentElement.clientHeight;
            }
        }
        /*document.getElementById('textSQLDUMP').style.width=(bodyWidth-300) + 'px';
        document.getElementById('textSQLDUMP').style.height=(bodyHeight-250) + 'px';*/
    }
//]]>
function grabar(){

}
</script>
</head>
<body>
<div class="row">
    <div class="container">
        <div class="col s12">
            <form id="frmMantTabla" action="" method="POST">
                <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
                <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
                <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
                <div class="row">
                    <div class="input-field col s12">
                        <textarea name="sqldump" id="textSQLDUMP" cols="50" rows="30" wrap="OFF" readonly style="background-color: white; height: 50%;">
                <?php
                echo "SISREST Dump\n";
                echo "Versión: ".$_SESSION['R_Version']."\n";
                date_default_timezone_set('America/Lima');
                echo "Fecha y hora: ".date("d/m/Y H:i:s")."\n";
                echo "Empresa: ".$_SESSION['R_IdEmpresa']." ".$_SESSION['R_NombreEmpresa']."\n";
                echo "Sucursal: ".$_SESSION['R_IdSucursal']." ".$_SESSION['R_NombreSucursal']."\n";
                echo "-------------------------------------------------------------------------------------------";
                while($value=$dato->fetch()){
                $c=0;
                ?>
                <?php //echo 'SELECT * FROM '.$value['descripcion'].' WHERE idsucursal='.$id_cliente.';'."\n";?>
                <?php
                    echo "\n".'TABLA: '.$value['descripcion']."\n\n";
                    $rst = $objMantenimiento->obtenerDatosTabla($value['descripcion'],$id_cliente,$id_empresa);
                    while($registros=$rst->fetch()){
                        //print_r($registros);
                        if($c==0){
                        foreach($registros as $campo => $valor){
                                if(is_string($campo)){
                                echo $campo."|";
                                }
                        }
                        echo "\n";
                        }
                        foreach($registros as $campo => $valor){
                                if(is_string($campo)){
                                echo $valor."|";
                                }
                        }	
                        echo "\n";
                        $c++;
                    }
                ?>
                <?php }?>
                        </textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</HTML>