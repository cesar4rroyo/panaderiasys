<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	//echo "Variables de Session no se pudieron crear";
	header("location: index.php");
}
if(isset($_POST['cmdEnviar'])) {
	$Captcha = (string) $_POST["CAPTCHA_CODE"];
	if($Captcha != $_SESSION["R_CAPTCHA_CODE"]){
		header("location: login.php");
	}else {
		include("verificaSesion.php");
		exit();  
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>GesRest <?php echo $_SESSION['R_Version'];?></title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/materialize.min.css">
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="css/sweetalert.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="img/24 Custom.ico" />
</head>
<body class="font-cover" id="login" onLoad="document.getElementById('UserName').focus();">
<div class="container">
    <div class="row valign-wrapper" style="width: 90%;
        position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    color: #333;">
        <div class="col m5 l3 hide-on-med-and-down">
            <img class="responsive-img right" style="min-height: 150px;min-width: 280px;/*max-height: 1000px; max-width: 200px;" src="assets/img/logo.png">
        </div>
        <div class="col s12 m7 l7 offset-l1 loginmozo">
            <div class="container-login center-align card" style="border-radius: 15px;">
                <div style="margin:15px 0;">
                    <h4>Sistema Est&aacute;ndar para Panaderia</h4>
                    <i class="zmdi zmdi-account-circle zmdi-hc-5x"></i>
                    <p>Inicia sesión con tu cuenta</p>   
                </div>
                <form name="frmSesion" method="post" action="verificaSesion.php">
                    <div class="input-field col s12">
                        <input id="UserName" type="text" class="validate" name="txtUsuario" required>
                        <label for="UserName"><i class="zmdi zmdi-account"></i>&nbsp; Nombre</label>
                    </div>
                    <div class="input-field col s12">
                        <input id="Password" type="password" class="validate" name="txtClave" required>
                        <label for="Password"><i class="zmdi zmdi-lock"></i>&nbsp; Contraseña</label>
                    </div>
                    <?php if($_SESSION['R_ContSecure']>20){?>
                    <div class="row">
                        <div class="input-field col s5">
                            <img src="secure/captcha.php" class="responsive-img">
                        </div>
                        <div class="input-field col s7">
                            <input id="codigo" name="CAPTCHA_CODE" type="text" size="15" class="caja" style="text-transform:uppercase" required>
                            <label for="codigo"><i class="zmdi zmdi-lock"></i>&nbsp; Código</label>
                        </div>
                    </div>
                    <?php }?>
                    <button class="waves-effect waves-teal btn teal darken-3">Ingresar &nbsp; <i class="zmdi zmdi-mail-send"></i></button>
                </form>
                <div class="divider" style="margin: 20px 0;"></div>
                <!--a href="home.html">Crear cuenta</a-->
            </div>
        </div>
    </div>
</div>
    

    <script src="js/sweetalert.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-2.2.0.min.js"><\/script>')</script>
    <script src="js/materialize.min.js"></script>
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>