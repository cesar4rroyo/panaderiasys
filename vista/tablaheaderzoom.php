<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
          <tr height="40"> 
            <td width="16"><img name="tablaadmin01_r1_c1" src="css/<?php echo $_SESSION['R_Estilo'];?>/tabla/tablaadmin01_r1_c1.<?php if($_SESSION['R_Estilo']=='estiloazul') echo 'jpg'; else echo 'png';?>" width="16" height="40" border="0" id="tablaadmin01_r1_c1" alt="" /></td>
            <td background="css/<?php echo $_SESSION['R_Estilo'];?>/tabla/tablaadmin01_r1_c2.<?php if($_SESSION['R_Estilo']=='estiloazul') echo 'jpg'; else echo 'png';?>" style="background-repeat:repeat-x" class="titulo zoom"><?php if($_SESSION['titulo']!='') echo $_SESSION['titulo'];?>&nbsp;</td>
            <td width="16"><img name="tablaadmin01_r1_c3" src="css/<?php echo $_SESSION['R_Estilo'];?>/tabla/tablaadmin01_r1_c3.<?php if($_SESSION['R_Estilo']=='estiloazul') echo 'jpg'; else echo 'png';?>" width="16" height="40" border="0" id="tablaadmin01_r1_c3" alt="" /></td>
          </tr>
          <tr> 
            <td background="css/<?php echo $_SESSION['R_Estilo'];?>/tabla/tablaadmin01_r2_c1.<?php if($_SESSION['R_Estilo']=='estiloazul') echo 'jpg'; else echo 'png';?>" style="background-repeat:repeat-y">&nbsp;</td>
            <td valign="top" bgcolor="#F5F9FA">
<?php $_SESSION['titulo']='';?>