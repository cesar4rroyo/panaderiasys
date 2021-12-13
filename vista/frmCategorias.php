<?php
session_start();
require("../modelo/clsMovimiento.php");
require("../modelo/clsProducto.php");
try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
                <!--DIV DERECHO DEL DETALLE DONDE LISTA TODOS LOS PRODUCTOS DEL SISTEMA EMPIEZA-->
                <div class="col s12 m6 l6" id="listaCategorias">
                  <div class="col s12 center DetalleMesa-Der">
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field col s12">
                              <i class="material-icons prefix IconoInput">search</i>
                              <input id="inpt_Busq_Producto" type="text" class="autocomplete">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                              <ul class="collapsible brown lighten-5" data-collapsible="accordion">
                                <?php
                                if($_GET["tipo"]=="C"){
                                    $comida="S";
                                    $bar="N";
                                }else{
                                    $comida="N";
                                    $bar="S";                                    
                                }
                                $sql="Select vIdCategoria, vAbreviatura,vIdCategoriaref,vDescripcion as Descripcion,vimagen from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") where vnivel=1 and vcomida='$comida' and vbar='$bar' order by vDescripcion ASC";
                                $consulta2 = $objMantenimiento->obtenerDataSQL($sql);
                                while($dato=$consulta2->fetchObject()){
                                    $sql="Select vIdCategoria, vAbreviatura,vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") where vidcategoriaref=$dato->vidcategoria order by vOrden ASC";
                                    $consulta = $objMantenimiento->obtenerDataSQL($sql);
                                    if($consulta->rowCount()>0){

                                    }else{
                                        $band=false;
                                        $rst=$objProducto->consultarProductoInterna(10000, 1, 1, 1, 0, "", $dato->vidcategoria,0,'','P');
                                        while($dato2=$rst->fetchObject()){
                                            //if($dato2->precioventa>=0){
                                                $band=true;
                                            //}
                                        }
                                        if($band){
                                        ?>
                                <li>
                                  <div class="collapsible-header <?php if($_GET["tipo"]=="C"){?>light-blue darken-4 white-text<?php }else{?>green darken-4 white-text<?php }?>"><i class="material-icons">restaurant</i><?php echo $dato->descripcion;?></div>
                                  <div class="collapsible-body">
                                        <div class="row"><script type="text/javascript">alert();</script>
                                        <?php
                                        $rst=$objProducto->consultarProductoInterna(10000, 1, 1, 1, 0, "", $dato->vidcategoria,0,'','P');
                                        while($dato2=$rst->fetchObject()){
                                            if($dato2->precioventa>0){
                                        ?>

                                              <div class="col s6 m6 l3">
                                                  <div onclick="modalPropiedades(<?=$dato2->idproducto?>,'<?=$dato2->descripcion?>','',1,<?=$dato2->idsucursal?>,'Nuevo');" class="card center hoverable z-depth-1 CardProducto">
                                                    <div class="card-image">
                                                      <img src="img/<?php echo $dato->vimagen;?>" class="responsive-img">
                                                    </div>
                                                    <div class="card-content">
                                                      <div data-funcion="<?=$dato2->idproducto?>,'<?=$dato2->descripcion?>','',1,<?=$dato2->idsucursal?>" class="descripcion_card_producto truncate"><?=$dato2->descripcion?></div>
                                                      <div><?=$dato2->precioventa?></div>
                                                    </div>
                                                  </div>
                                              </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        </div>
                                    </div>
                                    </li>
                                <?
                                        }
                                    }
                                }
                                ?>
                              </ul>
                        </div>
                    </div>
                  </div>
                </div>
                <div id="FINALDIVPRODUCTOS"></div>
                <!--DIV DERECHO DEL DETALLE DONDE LISTA TODOS LOS PRODUCTOS DEL SISTEMA ACABA-->
</body>