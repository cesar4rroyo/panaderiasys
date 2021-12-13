<?php 
session_start();
$id_solicitud = $_GET["id_solicitud"];
include_once '../modelo/mdlFichero.php';
$mdlFichero = new mdlFichero();
$ficheros = $mdlFichero->listarFicheros($id_solicitud);
?>
<table class="centered bordered">
    <thead class="indigo white-text">
        <tr>
            <th>NOMBRE</th>
            <th>FECHA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ficheros as $fichero) {?>
        <tr class="white">
            <td><?php echo $fichero["nombre_fichero"].$fichero["extension_fichero"];?></td>
            <td><?php echo date_format(date_create($fichero["fechahora_fichero"]),"d/m/Y h:i:s A");?></td>
            <td style="padding: 0px;">
                <button class="btn btn-floating amber tooltipped" data-position="top" data-delay="50" data-tooltip="DESCARGAR" onclick="download('<?php echo addslashes("nombre=".$fichero["nombre_fichero"].$fichero["extension_fichero"]."&ruta=".$fichero["direccion_fichero"]."&type=".$fichero["extension_fichero"]);?>');"><i class="material-icons">file_download</i></button>
            </td>
        </tr>
        <?php }?>
    </tbody>
</table>