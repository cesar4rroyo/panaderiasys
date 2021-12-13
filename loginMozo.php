<?php
session_start();

if(!isset($_SESSION['R_ini_ses'])){
    //echo "Variables de Session no se pudieron crear";
    header("location: indexmozo.php");
}
if(isset($_POST['cmdEnviar'])) {
    $Captcha = (string) $_POST["CAPTCHA_CODE"];
    if($Captcha != $_SESSION["R_CAPTCHA_CODE"]){
        header("location: loginMozo.php");
    }else {
        include("verificaSesion.php");
        exit();  
    }
}
$_SESSION["R_Formulario"]="Mozo";
require_once 'modelo/cado.php';
$acceso= new clsAccesoDatos('','');
$acceso->gIdTabla = 46;
$acceso->gIdSucursal = 1;
$sql = "Select Distinct P.idsucursal, P.IdPersona, Apellidos,Nombres, CASE WHEN tipopersona='VARIOS' THEN 'DNI' ELSE 'RUC' END as tipodoc, nrodoc,us.nombreusuario as usuario 
 From Persona P 
 inner join PersonaMaestro PM on PM.IdPersonaMaestro=P.IdPersonaMaestro 
 inner join rolpersona rp on rp.idpersona=P.idpersona and rp.idsucursal=P.idsucursal 
 INNER JOIN SUCURSAL s on s.idsucursal=P.idsucursal
 INNER JOIN usuario us on us.idpersona=P.idpersona and us.idsucursal=s.idsucursal   
 Where P.estado='N' ";
$sql .= " and idrol in (1) and s.idempresa=1 and us.idperfil=4";
//Emprea 1 por el potrero y perfil 5 por mozos
$rst = $acceso->obtenerDataSQL($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>GesRest <?php echo $_SESSION['R_Version'];?></title>
    <link rel="shortcut icon" href="img/24 Custom.ico" />
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/materialize.min.css">
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="css/sweetalert.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
        .numero{
            font-size: 1.6rem;
        }
    </style>
    <script>
    function ingresar(numero){
        document.getElementById("txtClave").value=document.getElementById("txtClave").value + numero;
        document.getElementById("inptClave").value=document.getElementById("inptClave").value + numero;
    }
    function vaciar(){
        document.getElementById("txtClave").value="";
        document.getElementById("inptClave").value="";
    }
    function enviar(){
        $("#formLogin").submit();
    }
    function usuario(us){
        document.getElementById("txtUsuario").value=us;
        $("#modalUsuario").closeModal();
        //modalPass();
    }
    </script>
</head>
<body class="font-cover" id="login2">

    <div class="container">
        <div class="row valign-wrapper" style="width: 90%;
        position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    color: #333;">
            <div class="col m5 l3 hide-on-small-only">
                <img class="responsive-img right" style="/*max-height: 1000px; max-width: 200px;" src="assets/img/logo.png1">
            </div>
            <div class="col s12 m7 l7 offset-l1 loginmozo card teal lighten-5" style="border-radius: 15px;">
                <!--div class="row hide-on-small-only">
                    <div class="col s12 center" style="margin-left: 15px">
                        <h1 style="font-weight: 900">CAFE TOSTAO</h1>
                    </div>
                </div-->
                <form id="formLogin" method="post" action="verificaSesion.php">
                    <input type="hidden" name="Origen" id="Origen" value="Mozo" />
                    <input type="hidden" id="txtCampo" value="txtClave" />
                    <input type="hidden" id="txtUsuario" name="txtUsuario">
                    <input type="hidden" name="txtClave" value="123">
                    <!--div class="row">
                        <div class="col s4 m3 l2">
                            <i class="zmdi zmdi-account-box zmdi-hc-5x right" onclick="modalUsuario();"></i>
                        </div>
                        <div class="input-field col s8 m9 l10">
                            <input name="txtUsuario" id="txtUsuario" placeholder="NOMBRE DE USUARIO" readonly="true" type="text" class="col s11 validate" name="txtUsuario" required onclick="modalUsuario();">
                            <label for="txtUsuario" id="lblUsuario" onclick="modalUsuario();"></label>
                        </div>
                    </div-->
                    
                    <div class="row">
                        <h2 class="teal-text darken-text-4 center hide-on-small-only" style="font-size: 2rem;">USUARIOS</h2>
                        <div class="divider"></div>
                        <?php
                        $cont=1;
                        while($reg=$rst->fetchObject()){
                            if(($cont-1)%2==0){
                                  //echo '<div class="row">';
                            }
                            $nombre = explode(",",$reg->nombres);
                            $apellido = explode(" ", $reg->apellidos);
                            $band=false;
                            if($_SERVER['REMOTE_ADDR']=="192.168.1.51" && $reg->usuario=="caja1"){//primer piso
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.0.51" && $reg->usuario=="caja2"){//segundo piso
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.53" && $reg->usuario=="caja3"){//
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.54" && $reg->usuario=="caja4"){//
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.58" && $reg->usuario=="caja5"){//
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.56" && $reg->usuario=="caja6"){//
                                $band=true;
                            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.57" && $reg->usuario=="boleteria"){//boleteria
                                $band=true;
                            }elseif($reg->usuario=="caja1" && $_SERVER['REMOTE_ADDR']!="192.168.0.51" ){ //&& $_SERVER['REMOTE_ADDR']!="192.168.1.51" && $_SERVER['REMOTE_ADDR']!="192.168.1.53" && $_SERVER['REMOTE_ADDR']!="192.168.1.54" && $_SERVER['REMOTE_ADDR']!="192.168.1.58" && $_SERVER['REMOTE_ADDR']!="192.168.1.56" && $_SERVER['REMOTE_ADDR']!="192.168.1.57" && $_SERVER['REMOTE_ADDR']=="192.168.1.49"){
                                $band=true;
                            }
                            if($band){
                                echo '<div class="col s6 m6 l6 center"><button style="margin-top: 7%;" class="btn-large teal darken-4 truncate" onclick="usuario(\''.$reg->usuario.'\')">'.substr($apellido[0],0,1).". ".substr($nombre[0],0,10).'</button></div>';
                                if($cont%2==0){
                                   //echo "</div>";
                                }
                                $cont++;
                            }
                        }
                        ?>
                    </div>
                    <!--div class="row">
                        <div class="col s4 m3 l2">
                            <i class="zmdi zmdi-key zmdi-hc-5x right" onclick="modalPass();"></i>
                        </div>
                        <div class="input-field col s8 m9 l10">
                            <input type="password" name="txtClave" id="txtClave" placeholder="CONTRASEÑA" readonly="true" class="col s11 validate" name="txtUsuario" required onclick="modalPass();">
                            <label for="txtClave" onclick="modalPass();"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 center">
                            <button type="submit" class="btn deep-orange darken-4">INGRESAR</button>
                        </div>
                    </div-->
                </form>
            </div>
        </div>
    </div>

    <div id="modalUsuario" class="modal modal-fixed-footer grey darken-1">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 center">
                    <h2 class="white-text" style="font-weight: 900;">MESEROS<div class="divider"></div></h2>
                </div>
            </div>
            <div class="row">
            <?php
//            $cont=1;
//            while($reg=$rst->fetchObject()){
//                if(($cont-1)%2==0){
//                      //echo '<div class="row">';
//                }
//                $nombre = explode(" ",$reg->nombres);
//                $apellido = explode(" ", $reg->apellidos);
//                echo '<div class="col s12 m6 l6 center"><button style="margin-top: 7%;" class="btn-large deep-orange darken-4" onclick="usuario(\''.$reg->usuario.'\')">'.substr($apellido[0],0,1).". ".substr($nombre[0],0,10).'</button></div>';
//                if($cont%2==0){
//                   //echo "</div>";
//                }
//                $cont++;
//            }
            ?>
            </div>
        </div>
        <div class="modal-footer red darken-2">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat white-text">CERRAR</a>
        </div>
    </div>

    <div id="modalPass" class="modal modal-fixed-footer grey darken-1">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 center">
                    <input class="center white-text" id="inptClave" type="password" name="" maxlength="10">
                </div>
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(1)">1</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(2)">2</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(3)">3</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(4)">4</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(5)">5</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(6)">6</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(7)">7</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(8)">8</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(9)">9</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="enviar()"><i class="zmdi zmdi-check"></i></button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="ingresar(0)">0</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" onclick="vaciar()"><i class="zmdi zmdi-close"></i></button>
                </div>
            </div>
        </div>
        <div class="modal-footer red darken-2">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat white-text">CERRAR</a>
        </div>
    </div>
    <script src="js/sweetalert.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-2.2.0.min.js"><\/script>')</script>
    <script src="js/materialize.min.js"></script>
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/materialize.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#modalUsuario').modal();
            $('#modalPass').modal();
        });
        function modalUsuario(){
            $('#modalUsuario').openModal({ 
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .0, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '4%', // Starting top style attribute
                ending_top: '10%', // Ending top style attribute
                ready: function(modal, trigger) {},
                complete: function() {
                    $('.lean-overlay').remove();
                }
            });
        }

        function modalPass(){
            $('#modalPass').openModal({ 
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .0, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '4%', // Starting top style attribute
                ending_top: '10%', // Ending top style attribute
                ready: function(modal, trigger) {},
                complete: function() {
                    $('.lean-overlay').remove();
                }
            });
        }
    </script>
</body>
</html>