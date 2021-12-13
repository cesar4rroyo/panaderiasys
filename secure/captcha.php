<?php
session_start();
function randomText($length) {
    $pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for($i=0;$i<$length;$i++) {
      $key .= $pattern{rand(0,35)};
    }
    return $key;
}
//$_SESSION['R_CAPTCHA_CODE'] = strtoupper(randomText(6));
$_SESSION['R_CAPTCHA_CODE'] = randomText(4);
//$captcha = imagecreatefromgif("bgcaptcha2.gif");

$captcha=imagecreate(100,40);
$fondo=imagecolorallocate($captcha,250,250,250);
$negro = imagecolorallocate($captcha, 0, 0, 0);
$fondo_amarillo=imagecolorallocate($captcha,255,255,0);
$azul=imagecolorallocate($captcha,50,50,255);
$verde=imagecolorallocate($captcha,0,255,0);
//$ttf = "../fuentes/arial.ttf";
$ttf = "../fuentes/JOKERMAN.TTF";

$color_basura = imagecolorallocate($captcha,mt_rand(80,200)+55,mt_rand(80,200)+55,mt_rand(100,250)+5);
//ensuciamos la imagen con letras
for($i=1;$i<20;$i++){
	$x = mt_rand(0,250);
	$y = mt_rand(0,250);
	$angulo = mt_rand(3,180);
	imagettftext($captcha, 17, $angulo, $x,$y, $color_basura,$ttf,randomText(1));
}
//ensuciamos la imagen con lineas
for($i=1;$i<70;$i++){
	$x = mt_rand(0,250);
	$y = mt_rand(0,250);
	imageline($captcha,$x,$y,$x+mt_rand(-40,40),$y+mt_rand(-40,40),$color_basura);
}

//imagestring($captcha, 5, 16, 7, $_SESSION['R_CAPTCHA_CODE'], $colText);
imagettftext($captcha, 18, mt_rand(-5,5), 18, 27, $azul,$ttf,$_SESSION['R_CAPTCHA_CODE']);

header("Content-type: image/gif");
imagegif($captcha);
?>