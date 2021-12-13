	//FUNCION QUE ME PERMITE OCULTAR LA LISTA AL PERDER EL FOCO DE  LA CAJA
	function autocompletar_blur(div) {
	    window.setTimeout('autocompletar_blur2(\'' + div + '\')', 300);
	}
	 
	function autocompletar_blur2(div) {
	  document.getElementById(div).style.display="none";
	}
	/*FUNCION PARA NAVEGAR POR LA LISTA, es importante que envíes el id del div del listado y de la tabla, no olvides que cada fila tr debe tener un id también.*/
	function autocompletar_teclado(div, tabladiv, keyc) {
	    var child = document.getElementById(tabladiv).rows;
	    var indice = -1;
	 
	    for(var i=0; i < child.length; i++) {
	        if(child[i].className == 'tr_hover') {
	            indice = i;
	        }
	        if(i % 2==0){
	            child[i].className = 'impar';
	        }else{
	            child[i].className = 'par';}
	    }
	 
	    // return
	    if(keyc == 13) {
			if(indice != -1){
				var seleccionado = '';
		 
				if(child[indice].id) {
					seleccionado = child[indice].id;
				} else {
				 seleccionado = child[indice].id;
				}
		 
				mostrarPersona(seleccionado,div);
			}
	    } else {
	        // abajo
	        if(keyc == 40) {
	            if(indice == (child.length - 1)) {
	                indice = 1;
	            } else {
	                if(indice==-1) indice=0;
	                indice++;
	            }
	 
	        // arriba
	        } else if(keyc == 38) {
	            indice--;
	            if(indice==0) indice=-1;
	            if(indice < 0) {
	                indice = (child.length - 1);
	            }
	        }
	 
	        child[indice].className = 'tr_hover';
	    }
	}


	function autocompletar_teclado2(div, tabladiv, keyc) {
	    var child = document.getElementById(tabladiv).rows;
	    var indice = -1;
	 
	    for(var i=0; i < child.length; i++) {
	        if(child[i].className == 'tr_hover') {
	            indice = i;
	        }
	        if(i % 2==0){
	            child[i].className = 'impar';
	        }else{
	            child[i].className = 'par';}
	    }
	 
	    // return
	    if(keyc == 13) {
			if(indice != -1){
				var seleccionado = '';
		 
				if(child[indice].id) {
					seleccionado = child[indice].id;
				} else {
				 seleccionado = child[indice].id;
				}
		 		datos=seleccionado.split('-');
				mostrarPersona(datos[0],datos[1],div);
			}
	    } else {
	        // abajo
	        if(keyc == 40) {
	            if(indice == (child.length - 1)) {
	                indice = 1;
	            } else {
	                if(indice==-1) indice=0;
	                indice++;
	            }
	 
	        // arriba
	        } else if(keyc == 38) {
	            indice--;
	            if(indice==0) indice=-1;
	            if(indice < 0) {
	                indice = (child.length - 1);
	            }
	        }
	 
	        child[indice].className = 'tr_hover';
	    }
	}
	
	
	function autocompletarProducto_teclado2(div, tabladiv, keyc) {
	    var child = document.getElementById(tabladiv).rows;
	    var indice = -1;
	 
	    for(var i=0; i < child.length; i++) {
	        if(child[i].className == 'tr_hover') {
	            indice = i;
	        }
	        if(i % 2==0){
	            child[i].className = 'impar';
	        }else{
	            child[i].className = 'par';}
	    }
	 
	    // return
	    if(keyc == 13) {
			if(indice != -1){
				var seleccionado = '';
		 
				if(child[indice].id) {
					seleccionado = child[indice].id;
				} else {
				 seleccionado = child[indice].id;
				}
				datos=seleccionado.split('-');
				seleccionar(datos[0],datos[1]);
			}
	    } else {
	        // abajo
	        if(keyc == 40) {
	            if(indice == (child.length - 1)) {
	                indice = 1;
	            } else {
	                if(indice==-1) indice=0;
	                indice++;
	            }
	 
	        // arriba
	        } else if(keyc == 38) {
	            indice--;
	            if(indice==0) indice=-1;
	            if(indice < 0) {
	                indice = (child.length - 1);
	            }
	        }
	 
	        child[indice].className = 'tr_hover';
	    }
	}
	
	function autocompletarPedido_teclado2(div, tabladiv, keyc) {
	    var child = document.getElementById(tabladiv).rows;
	    var indice = -1;
	 
	    for(var i=0; i < child.length; i++) {
	        if(child[i].className == 'tr_hover') {
	            indice = i;
	        }
	        if(i % 2==0){
	            child[i].className = 'impar';
	        }else{
	            child[i].className = 'par';}
	    }
	 
	    // return
	    if(keyc == 13) {
			if(indice != -1){
				var seleccionado = '';
		 
				if(child[indice].id) {
					seleccionado = child[indice].id;
				} else {
				 seleccionado = child[indice].id;
				}
				seleccionarpedido(seleccionado);
			}
	    } else {
	        // abajo
	        if(keyc == 40) {
	            if(indice == (child.length - 1)) {
	                indice = 1;
	            } else {
	                if(indice==-1) indice=0;
	                indice++;
	            }
	 
	        // arriba
	        } else if(keyc == 38) {
	            indice--;
	            if(indice==0) indice=-1;
	            if(indice < 0) {
	                indice = (child.length - 1);
	            }
	        }
	 
	        child[indice].className = 'tr_hover';
	    }
	}