<?php
session_start();
$id_clase=46;//clase de las mesas
?>
<?php require("fun.php"); ?>
<form method="post">
<input type="hidden" id="Disponible" value="false" />

<div class="row">
    <div class="col s12">
      <ul class="tabs blue" style="overflow-x: hidden;">
        <?php 
        require("../modelo/clsMovimiento.php");
        $objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
        $rst = $objMantenimiento->obtenerDataSQL("select * from salon where estado='N' and idsucursal=".$_SESSION['R_IdSucursal']." order by imagen");
        $c=0;
        while($dato=$rst->fetchObject() and $c<8){
            echo '<li class="tab col s6 m3 l3 Tab-activo" id="'.$dato->idsalon.'"><a href="#" onclick="javascript:genera_cboMesas(\''.$dato->idsalon.'\',\''.trim($dato->abreviatura).'\');">'.$dato->abreviatura.'</a></li>';
            $c++;
        };
        ?>
      </ul>
    </div>
    <div id="divdiagramaMesa" class="col s12">
        
    </div>
</div>
<script>
function ingresar(numero){
    document.getElementById("txtMesa").value=document.getElementById("txtMesa").value + numero;
}

function verificarmesa(numero,idsalon){
        g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificarmesa");
		g_ajaxPagina.setParameter("txtMesa",numero);
        g_ajaxPagina.setParameter("idsalon",idsalon);
		g_ajaxPagina.response = function(text){
		    eval(text);
            document.getElementById("Disponible").value = vdisponible;
            document.getElementById("Idmesa").value = vidmesa;
            document.getElementById("Nropersonas").value = vnropersonas;
            enviar(numero);
		};
		g_ajaxPagina.request();
}
function enviar(numero){
    if(document.getElementById("Disponible").value=="true"){
        setRun("vista/frmComanda","&idmesa="+document.getElementById("Idmesa").value+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
        //$("#frame").addClass("row");
    }else{
        g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificarusuario");
		g_ajaxPagina.setParameter("IdMesa",document.getElementById("Idmesa").value);
		g_ajaxPagina.response = function(text){
            var text = JSON.parse(text);
            if(text.modificar==true){
                setRun("vista/frmComanda","&idmesa="+document.getElementById("Idmesa").value+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=ACTUALIZAR","frame","frame","imgloading");
            }else{
                alert("ESTA MESA YA ESTA SIENDO ATENDIDA");
            }
	   };
        g_ajaxPagina.request();
        //$("#frame").addClass("row");
    }     
}
function genera_cboMesas(idsalon,abreviatura){
    if(idsalon==2){
        var tabs = $(".tab");
        for(var i=0;i<tabs.length;i++){
            var tab = tabs[i];
            $(tab).removeClass("Tab-activo");
            $(tab).removeClass("Tab-inactivo");
            $(tab).addClass("Tab-inactivo");
        }
        $("#"+idsalon).removeClass("Tab-inactivo");
        $("#"+idsalon).addClass("Tab-activo");
        var recipiente = document.getElementById('divdiagramaMesa');
        g_ajaxPagina = new AW.HTTP.Request;
        g_ajaxPagina.setURL("vista/ajaxPedido.php");
        g_ajaxPagina.setRequestMethod("POST");
        g_ajaxPagina.setParameter("accion", "genera_diagramaMesasMozo");
        g_ajaxPagina.setParameter("IdSalon", idsalon);
        g_ajaxPagina.response = function(text){
            recipiente.innerHTML = text;
            document.getElementById("Salon").value=abreviatura;	
        };
        g_ajaxPagina.request();
    }else{
        document.getElementById("Salon").value="CLIENTE";
        setRun("vista/frmComanda","idmesa=10&mesa=0&salon=CLIENTE&accion=NUEVO","frame","frame","imgloading");	
    }
}
genera_cboMesas(<?php if($_SERVER['REMOTE_ADDR']=="192.168.1.120"||$_SERVER['REMOTE_ADDR']=="::1"){ echo "2";}else{ echo "2";}?>,'<?php if($_SESSION['R_IdSucursal']) echo "SALON";else echo "SALON";?>');
</script>
