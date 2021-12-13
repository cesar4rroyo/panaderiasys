<?
    $tam=getimagesize("logo.jpg");
    if($tam[0] > 50 OR $tam[1] > 50)
        {
        cambiartam("logo.jpg", "aa.jpg", 50, 50);
        echo "nuevaimagen.jpg";
        }

function cambiartam($nombre,$archivo,$ancho,$alto){

    $strs = explode(".",$nombre);
    $count= count($strs);
    $tmp = ".".$strs[$count-1];
    echo $tmp;

    //$tmp=(split(".",$nombre))[1];
    //echo $tmp[0];

    if (preg_match('/jpg|jpeg|JPG/',$tmp))
        {
        $imagen=imagecreatefromjpeg($nombre);
        }
    if (preg_match('/png|PNG/',$tmp))
        {
        $imagen=imagecreatefrompng($nombre);
        }
    if (preg_match('/gif|GIF/',$tmp))
        {
        $imagen=imagecreatefromgif($nombre);
        }

    $x=imageSX($imagen);
    $y=imageSY($imagen);

    if ($x > $y)
        {
        $w=$ancho;
        $h=$y*($alto/$x);
        }

    if ($x < $y)
        {
        $w=$x*($ancho/$y);
        $h=$alto;
        }

    if ($x == $y)
        {
        $w=$ancho;
        $h=$alto;
        }


    $destino=ImageCreateTrueColor($w,$h);
    imagecopyresampled($destino,$imagen,0,0,0,0,$w,$h,$x,$y);


    if (preg_match("/png/",$tmp))
        {
        imagepng($destino,$archivo);
        }
    if (preg_match("/gif/",$tmp))
        {
        imagegif($destino,$archivo);
        }
    else
        {
        imagejpeg($destino,$archivo);
        }

    imagedestroy($destino);
    imagedestroy($imagen);
}

?>
