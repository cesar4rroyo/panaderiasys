<?php 
session_start();
include '../modelo/mdlSolicitud.php';
$mdlSolicitud = new mdlSolicitud();
$id_solicitud = $_GET["idsolicitud"];
$objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud);
$numero = $objSolicitud["serie"]."-".str_pad($objSolicitud["correlativo"],8,"0",STR_PAD_LEFT);
?>
<section>
    
    <div class="row contenido">
        <div class="col s12 m8 offset-m2 l6 offset-l3">
            <form id="frmEmail" action="controlador/contComprobante.php?funcion=enviaremail">
                <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud;?>">
                <div class="col s12">
                    <div class="input-field">
                        Número: <?php echo $numero; ?>
                    </div>
                </div>
                <div class="col s11">
                    <div class="input-field">
                        <input type="email" name="emailtext" id="emailtext" value="" onkeyup="validaremail();">
                        <label for="emailtext">Email</label>
                    </div>
                </div>

                <div class="col s1">
                    <div class="input-field">
                        <button type="button" id="btnValidar" onclick="anadiremail()" class="btn btn-floating green" disabled="true"><i class="material-icons">add</i></button>
                    </div>
                </div>
                
                <table id="lstcorreo" class="highlight responsive-table">
                        <tbody >
                                
                        </tbody>  
                </table>
                
                <div class=" col s12">
                    <div class="input-field">
                        <textarea id="comentario" class="materialize-textarea" name ="comentario"></textarea>
                        <label for="comentario">Comentario</label>
                    </div>
                </div>
                
                
                <div class="row col s12 right-align divEnviar" id="btnGuardar">
                    <button type="button" class="btn btn-submit" onclick="mandaemail();">ENVIAR<i class="material-icons left">save</i></button>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    $("select").material_select();
    <?php
    
        echo 'agregarRuta(false,"Enviar Email","frmEmail.php?'.$_SERVER["QUERY_STRING"].'");';
    
    ?>
    
   
    activeLabels();
    agregarOpciones([]);

    function validaremail(){
        var email = $("#emailtext").val();
        var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
        //var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@([a-zA-Z0-9-])+$/);
        if (caract.test(email) == false){
            $("#btnValidar").attr("disabled","disabled");

        }else{
            $("#btnValidar").removeAttr("disabled");

        }
    }

    function anadiremail(){
        email = $("#emailtext").val();
                $('#lstcorreo > tbody').append('<tr> '+
                                                '<td ><input type="hidden" name="emails[]" value="'+email+'"><div style="margin-left:7%";>'+email+
                                                '<div></td>'+
                                                '<td style="text-align: center;">'+'<button type="button"  class="btn btn-floating red"><i class="material-icons" onclick="eliminaremail(this)">clear</i></button>'+
                                                '</td>'+
                                            '</tr>'
                );
        $("#emailtext").val("");
        $("#btnValidar").attr("disabled","disabled");
        
        
    }
    function eliminaremail(boton){
        event.preventDefault();
        boton.closest('tr').remove();
    }

    function mandaemail(){
        var j= 0;
        $('input[name^="emails"]').each(function(){
            j++;
        });
        if(j>0){
            enviarForm('frmEmail','btnGuardar');
            
        }else{
            alerta("DEBE INGRESAR EL(LOS) CORREO(S)");
        }
    }
</script>