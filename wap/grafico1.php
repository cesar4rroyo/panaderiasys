<?php
session_start();
$datos=$_SESSION['R_data'];
$cantidad=count($datos);
$maximo=max($datos);

$anchoimagen=300;
$altoimagen=200;
$margen=50;
$anchografico=$anchoimagen-($margen*2);
$altografico=$altoimagen-($margen*2);
$escala=$altografico/$maximo;
$img=imagecreate($anchoimagen,$altoimagen);
$fondo=imagecolorallocate($img,250,250,250);
$azul=imagecolorallocate($img,0,0,255);
$verde=imagecolorallocate($img,0,255,0);
$rojo=imagecolorallocate($img,255,0,0);
$amarillo=imagecolorallocate($img,255,255,0);
$negro=imagecolorallocate($img,0,0,0);
$gris=imagecolorallocate($img,100,100,100);

// líneas de ejes
$margen2=$anchoimagen-$margen; //margen derecho
$margen3=$altoimagen-$margen; //margen inferior
imageline($img,$margen,$margen,$margen,$margen3,$negro);
imageline($img,$margen,$margen3,$margen2,$margen3,$negro);

// líneas guía
$parte=$altografico/4;
imageline($img,$margen,$margen3-($parte),$margen2,$margen3-($parte),$gris);
imagestring($img,2,$margen-30,$margen3-($parte),ceil($maximo/4),$negro);

imageline($img,$margen,$margen3-($parte*2),$margen2,$margen3-($parte*2),$gris);
imagestring($img,2,$margen-30,$margen3-($parte*2),ceil($maximo/2),$negro);

imageline($img,$margen,$margen3-($parte*3),$margen2,$margen3-($parte*3),$gris);
imagestring($img,2,$margen-30,$margen3-($parte*3),ceil($maximo*(3/4)),$negro);

imagestring($img,2,$margen-30,$margen3-($parte*4),ceil($maximo),$negro);

$anchobarra=$anchografico/($cantidad*2);
$distancia=$anchobarra*2;
$y2=$margen3;
$x1=0;
// barras
$sw=0;
foreach($datos as $indice => $valor){
	if($sw==0) $x1=$margen+$anchobarra;
	else $x1=$x1+$distancia;
	$sw=1;
	$x2=$x1+$anchobarra;
	$y1=$margen3-($valor*$escala);
	$rojo =rand(100,255);
	$verde=rand(100,255);
	$azul =rand(100,255);
	$color=imagecolorallocate($img,$rojo,$verde,$azul);
	imagefilledrectangle($img,$x1,$y1,$x2,$y2,$color);
	imagestring($img,2,$x1+5,$y1-15,$valor,$negro);
	imagestring($img,2,$x1+5,$margen3+20,$indice,$negro);
}

// titulo del gráfico
if($_GET['tipo']==1){
imagestring($img,5,120,20,"Gráfico de Reporte de Actas de Nacimiento ".$_GET['ano'],$negro);
}
else{if($_GET['tipo']==2){
imagestring($img,5,120,20,"Gráfico de Reporte de Actas de Matrimonio ".$_GET['ano'],$negro);
}
else{
if($_GET['tipo']==3){
imagestring($img,5,120,20,"Gráfico de Reporte de Actas de Defunción ".$_GET['ano'],$negro);
}
else{imagestring($img,3,15,20,"Gráfico de Reporte de Pedidos por Mesero",$negro);
}
}
}
//titulo ejes
imagestring($img,2,15,35,'Cant',$negro);
imagestring($img,2,270,170,'N°',$negro);
header("Content-type: image/jpeg");
imagejpeg($img);
?>