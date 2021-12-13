<?
	   function  rand_pos($de=0,$max=0,$cuantos=false){
       $i=0;
       $rand_nums=array();
       $de=(!preg_match('/^[0-9]+$/',$de)||$de>$max)?0:$de;
       $max=(!preg_match('/^[0-9]+$/',$max))?0:$max;
       $cuantos=($cuantos&&!preg_match('/^[0-9]+$/',$cuantos))?1:$cuantos; 
       while($i<=$max){
       while(in_array($rand=rand($de,$max),$rand_nums));
       $rand_nums[]=$rand;
       if($cuantos&&$i == $cuantos-1)
       return $rand_nums;
       $i++;
       }
        return $rand_nums;
        }
		
		
   function asignar_valor($check)
   {
      if($check==true){
		  $valor=1;
		  }else{
		 $valor=0;
		}
      return $valor;
   }
		
	function ver_estado_check($valor){
      if($valor==0){
		  $check=" ";
		}else{
		 $check="checked";
		}
      return $check;
   }	
   
 function verifica_menus($valor,$mostrar){
      if($valor==0){
		  $mostrar="";
	  }elseif($valor==1){
		 $mostrar=$mostrar;
	}
      return $mostrar;
   }     
?>