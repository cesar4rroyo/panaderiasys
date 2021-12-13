<?php
$mobile_browser = '0';
//echo $_SERVER['HTTP_USER_AGENT'].'<br>';

//$_SERVER['HTTP_USER_AGENT'] -> el agente de usuario que está accediendo a la página.
//preg_match -> Realizar una comparación de expresión regular
if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|mobi)/i',strtolower($_SERVER['HTTP_USER_AGENT']))){    $mobile_browser++;
}

//$_SERVER['HTTP_ACCEPT'] -> Indica los tipos MIME que el cliente puede recibir.
if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or    ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))){    
$mobile_browser++;
}

$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
$mobile_agents = array(    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',    'newt','noki','palm','pana','pant','phil','play','port','prox',    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',    'wapr','webc','winw','winw','xda','xda-');

//buscar agentes en el array de agentes
if(in_array($mobile_ua,$mobile_agents)){    
	$mobile_browser++;
}
	
//$_SERVER['ALL_HTTP'] -> Todas las cabeceras HTTP
//strpos -> Primera aparicion de una cadena dentro de otra
if(strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {    
	$mobile_browser++;
}

	
if($mobile_browser>0){        
	// Mostrar contenido para dispositivos móviles        
	// Estos pueden ser más ligeros: un titulo, resumen y algunos enlaces.        
	// Aca puede redirigir a la ruta donde este el contenido para moviles        
	// Por ejemplo: http://miweb.com/movil ó http://movil.miweb.com   
	 //echo 'hola navegador cell phone';
	 header("Location: loginMozo.php");
	 exit;
}/*else{        
	 // Contenido que se puede apreciar en navegadores de escritorio        
	 echo 'hola navegador normal';
}*/
?>