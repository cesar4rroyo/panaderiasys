<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
//$datos=$_SESSION['datagrafico'];
if(isset($_GET['sesiongrafico'])){
	$sessionGrafico=$_GET['sesiongrafico'];
}else{
	$sessionGrafico='datagrafico';
}
$datos=$_SESSION[$sessionGrafico];
header('Content-Type: image/png');
//header("Content-type: image/jpeg");
if($_GET['anchoGrafico']!='' and isset($_GET['anchoGrafico'])){
	$ancho=$_GET['anchoGrafico'];
}else{
	$ancho=800;
}
if($_GET['altoGrafico']!='' and isset($_GET['altoGrafico'])){
	$alto=$_GET['altoGrafico'];
}else{
	$alto=500;
}

$grafico=imagecreate($ancho,$alto);

//definimos los colores que vamos a usar
$fondo=imagecolorallocate($grafico,250,250,250);
$verde=imagecolorallocate($grafico,0,255,0);
$rojo=imagecolorallocate($grafico,255,0,0);
$amarillo=imagecolorallocate($grafico,255,255,0);
$negro=imagecolorallocate($grafico,0,0,0);
$gris=imagecolorallocate($grafico,200,200,200);

//maximo valor
$maximo=max($datos);

//numero de barras
$num_barras=count($datos);

//numero de espacios entres barras
$esp_barras=$num_barras+1;

//margenes para dibujar el grafico
$margen=70;

//otros margenes
$margen1=$ancho-$margen;//=750
$margen2=$alto-$margen;//=550

//dimensiones del grafico
$altografico=$alto-2*$margen;
$anchografico=$ancho-2*$margen;

//factor por el cual multiplicar
$escala=$altografico/$maximo;

//valores extras para el eje Y
$separacion=40;
$separacion2=8;
$angulo=10;

//lineas guias
$partes=$altografico/4;

imagestring($grafico,3,$margen-$separacion,$margen-$separacion2,ceil(($maximo/4)*4),$negro);
imageline($grafico,$margen-10,$margen,$margen,$margen,$negro);
imageline($grafico,$margen,$margen,$margen+$angulo,$margen-$angulo,$negro);
imageline($grafico,$margen+$angulo,$margen-$angulo,$margen+$angulo+$anchografico,$margen-$angulo,$negro);

imagestring($grafico,3,$margen-$separacion,$margen+1*$partes-$separacion2,ceil(($maximo/4)*3),$negro);
imageline($grafico,$margen-10,$margen+1*$partes,$margen,$margen+1*$partes,$negro);
imageline($grafico,$margen,$margen+1*$partes,$margen+$angulo,$margen+1*$partes-$angulo,$negro);
imageline($grafico,$margen+$angulo,$margen+1*$partes-$angulo,$margen+$angulo+$anchografico,$margen+1*$partes-$angulo,$negro);

imagestring($grafico,3,$margen-$separacion,$margen+2*$partes-$separacion2,ceil(($maximo/4)*2),$negro);
imageline($grafico,$margen-10,$margen+2*$partes,$margen,$margen+2*$partes,$negro);
imageline($grafico,$margen,$margen+2*$partes,$margen+$angulo,$margen+2*$partes-$angulo,$negro);
imageline($grafico,$margen+$angulo,$margen+2*$partes-$angulo,$margen+$angulo+$anchografico,$margen+2*$partes-$angulo,$negro);

imagestring($grafico,3,$margen-$separacion,$margen+3*$partes-$separacion2,ceil(($maximo/4)*1),$negro);
imageline($grafico,$margen-10,$margen+3*$partes,$margen,$margen+3*$partes,$negro);
imageline($grafico,$margen,$margen+3*$partes,$margen+$angulo,$margen+3*$partes-$angulo,$negro);
imageline($grafico,$margen+$angulo,$margen+3*$partes-$angulo,$margen+$angulo+$anchografico,$margen+3*$partes-$angulo,$negro);

imagestring($grafico,3,$margen-$separacion,$margen+4*$partes-$separacion2,(($maximo/4)*0),$negro);
imageline($grafico,$margen-10,$margen+4*$partes,$margen,$margen+4*$partes,$negro);
imageline($grafico,$margen,$margen+4*$partes,$margen+$angulo,$margen+4*$partes-$angulo,$negro);
imageline($grafico,$margen+$angulo,$margen+4*$partes-$angulo,$margen+$angulo+$anchografico,$margen+4*$partes-$angulo,$negro);

//--------------------------------------------------------------------------------------------------
imageline($grafico,$margen1,$margen2,$margen+$angulo+$anchografico,$margen+4*$partes-$angulo,$negro);
//--------------------------------------------------------------------------------------------------

//ancho de las barras
$anchobarra=$anchografico/($num_barras+$esp_barras);

//ancho de los espacios
$anchoespacios=$anchobarra;

//----------VAMOS A DIBUJAR EL GRAFICO------------------
$x=$margen+$anchoespacios;
foreach ($datos as $valor=>$indice) {
    $altobarra=$escala*$indice;
    $x1=$x;
    $x2=$x+$anchobarra;
    $y1=$margen2-($altobarra);
    $y2=$margen2;
    $rojo =rand(100,255);
	$verde=rand(100,255);
	$azul =rand(100,255);
	$color=imagecolorallocate($grafico,$rojo,$verde,$azul);
    for ($i = 0; $i < 10; $i ++){

        $factorsombra=50;
        $sombra=imagecolorallocate($grafico,$rojo-$factorsombra,$verde-$factorsombra,$azul-$factorsombra);
        imagefilledrectangle($grafico,$x1+$i,$y1-$i,$x2+$i,$y2-$i,$sombra);
    }
    imagefilledrectangle($grafico,$x1,$y1,$x2,$y2,$color);
    imagestring($grafico,3,$x1+(5),$y1,$indice,$negro);
	//si se aumentan las dimensiones de la imagen, aumentar esta variable
	$tamaño=9;
    imagettftext($grafico,$tamaño,45,$x1,$y2+60,$negro,"arial.ttf",$valor);
    
    $x+=$anchoespacios+$anchobarra;
}
//ejes
imageline($grafico,$margen,$margen,$margen,$margen2,$negro);//eje Y
imageline($grafico,$margen,$margen2,$margen1,$margen2,$negro);//eje X

//mandamos la imagen
imagepng($grafico);
//imagejpeg($grafico);
//limpiar recursos
imagedestroy($grafico);
?>