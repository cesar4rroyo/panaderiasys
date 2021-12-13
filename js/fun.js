function loading(opt, id, lugar, image, modo) {
	if (opt == true) {
            if(document.getElementById(lugar)){
                var refer = document.getElementById(lugar);
                //console.log(refer);
			//alert("Uno");
		var referHeight = refer.offsetHeight;
		var referWith = refer.offsetWith;

		//refer.style.textAlign = 'center';
		var img = document.createElement('img');
		if(image.substr(0,3)=="../"){
			img.setAttribute('src',image);
		}else{
			img.setAttribute('src','img/'+image);
		}
		img.setAttribute('id',id);
		img.setAttribute('align','center');
		refer.style.background = (1/100);
		img.setAttribute('left','0');
		if(modo==true){
			img.style.position='absolute';
		}
		img.style.textAlign = 'center';
		if (!document.getElementById(id)) {
                    //alert("CARGANDO");
                    //console.log(refer);
                    $(refer).html('<div id="'+id+'" class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div>');
			//refer.insertBefore(img, refer.firstChild);
		}
            }
	} else if (opt == false) {
		
		var imgLoading = document.getElementById(id);
		if (imgLoading) {
			imgLoading.parentNode.removeChild(imgLoading);
		}
	}
}
function redireccionar(url){ 
	location.href=url; 
}

function getFormData(objf) {
	var formObj =document.getElementById(objf);
	var elementos = document.getElementById(objf).elements.length;
	for (var i=0;i<elementos;i++){
		if (formObj.elements[i].type != undefined && formObj.elements[i].name != undefined){
			switch (formObj.elements[i].type)
			{
			case "checkbox":
				if(formObj.elements[i].checked==true){
			  		g_ajaxGrabar.setParameter(formObj.elements[i].name, "S");
				}else{
					g_ajaxGrabar.setParameter(formObj.elements[i].name, "N");
				}
			  	break;
			case "radio":
				var radios = document.getElementsByName(formObj.elements[i].name);
				var cant = radios.length;
				for (var j=0;j<cant;j++){
					var myOpt = radios[j];
					if(myOpt.checked){
						g_ajaxGrabar.setParameter(formObj.elements[i].name,myOpt.value);
					}
				}
			  	break;
			case "select-one":
				if(formObj.elements[i].name.substr(3,3)=="Mul"){
					var optiones = document.getElementById(formObj.elements[i].name);
					var cant = optiones.length;
					var Lista= new Array();
					var k=0;
					for (var j=0;j<cant;j++){
						var myOpt = optiones[j];
						Lista[k] = myOpt.value;
						k++;
					}
					g_ajaxGrabar.setParameter(formObj.elements[i].name+"[]", Lista);
				}else{
					g_ajaxGrabar.setParameter(formObj.elements[i].name, formObj.elements[i].value);
				}
			  	break;
			case "select-multiple":
				var optiones = document.getElementById(formObj.elements[i].name);
				var cant = optiones.length;
				var Lista= new Array();
				var k=0;
				for (var j=0;j<cant;j++){
					var myOpt = optiones[j];
					if(myOpt.selected){
						Lista[k] = myOpt.value;
						k++;
					}
				}
				g_ajaxGrabar.setParameter(formObj.elements[i].name+"[]", Lista);
			  	break;
			default:
			  g_ajaxGrabar.setParameter(formObj.elements[i].name, formObj.elements[i].value);
			}
		}
	}
}

function setValidar(objf) {
	var formComplete=true;
	var alertMsg = "";
	var iSet = 0
	var formObj =document.getElementById(objf);
	/*var elementos = document.getElementById(objf).elements.length;
	for (var i=0;i<elementos;i++){
		if(formObj.elements[i].type!="button"){
			var elemValLength = formObj.elements[i].value;
			switch (formObj.elements[i].type)
			{
			case "select-one":
				if (formObj.elements[i].title != null && formObj.elements[i].title != "") {
					
					if(elemValLength==0 | elemValLength=='0'){
						alertMsg = formObj.elements[i].title;
						iSet = i;
						formComplete = false;
						break;
					}
				}
				break;
			case "select":
				if (formObj.elements[i].title != null && formObj.elements[i].title != "") {
					
					if(elemValLength==0 | elemValLength=='0'){
						alertMsg = formObj.elements[i].title;
						iSet = i;
						formComplete = false;
						break;
					}
				}
				break;
			case "select-multiple":
				if (formObj.elements[i].title != null && formObj.elements[i].title != "") {
					
					if(elemValLength==0 | elemValLength=='0'){
						alertMsg = formObj.elements[i].title;
						iSet = i;
						formComplete = false;
						break;
					}
				}
				break;
			default:
			  if (formObj.elements[i].title != null && formObj.elements[i].title != "") {
					
					if(elemValLength.length < 1 ){
						alertMsg = formObj.elements[i].title;
						iSet = i;
						formComplete = false;
						break;
					}
				}
			}
		}
	}
	if (!formComplete){
		alert(alertMsg);
		formObj.elements[iSet].focus();
		return false;
	} else {*/
		return true;
	//}
}

function valEmail(valor){
     re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/
     if(!re.exec(valor))    {
         return false;
     }else{
         return true;
     }
 }

function accionMultiple(accion){
	//alert(accion);
	eval(accion);
}

function chekearTodo(valor){
	var radios = document.getElementsByName("chkSeleccion[]");
	var cant = radios.length;
	for (var j=0;j<cant;j++){
		var myOpt = radios[j];
		myOpt.checked = valor;
		vObj=myOpt.parentNode.parentNode;
		if(myOpt.checked){
			document.getElementById(vObj.id).className = 'sombra';
		}else{
			vDato=vObj.id.substr(2,1);
			if(vDato=='0'){
				document.getElementById(vObj.id).className='odd';
			}else{
				document.getElementById(vObj.id).className='even';
			}
		}
	}
}

function validarsololetras(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/[A-Za-z\s]/; // 4
    //patron =/\d/;
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

function validarsolonumeros(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    //patron =/[A-Za-z\s]/; // 4
    patron =/\d/;
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

function validarsololetrasynumeros(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/[A-Za-z\d\s]/; // 4
    //patron =/\d/;
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

function validarsolonumerosdecimales(e,valor) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron = /[0-9\,\.]/;
    te = String.fromCharCode(tecla); // 5
    r = patron.test(te); // 6
	if(r){
		formato = /^[0-9]*(\.[0-9]{0,2})?$/;
		if(!formato.exec(valor+te))    {
		 return false;
		}else{
		 return true;
		}
	}else{
		return false;
	}
	
}

function validarsolonumerosdecimales4(e,valor) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron = /[0-9\,\.]/;
    te = String.fromCharCode(tecla); // 5
    r = patron.test(te); // 6
	if(r){
		formato = /^[0-9]*(\.[0-9]{0,4})?$/;
		if(!formato.exec(valor+te))    {
		 return false;
		}else{
		 return true;
		}
	}else{
		return false;
	}
	
}

function validarsolonumerosdecimales3(e,valor) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron = /[0-9\,\.]/;
    te = String.fromCharCode(tecla); // 5
    r = patron.test(te); // 6
	if(r){
		formato = /^[0-9]*(\.[0-9]{0,3})?$/;
		if(!formato.exec(valor+te))    {
		 return false;
		}else{
		 return true;
		}
	}else{
		return false;
	}
	
}

function validarnumeroconserie(valor) {
    patron =/^(\d){3}-(\d){6}-(\d){4}/;
    if(!patron.exec(valor))    {
         return false;
     }else{
         return true;
     }
}

function valFecha(valor){
     re=/^(?:(?:0?[1-9]|1\d|2[0-8])(\/|-)(?:0?[1-9]|1[0-2]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:31(\/|-)(?:0?[13578]|1[02]))|(?:(?:29|30)(\/|-)(?:0?[1,3-9]|1[0-2])))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(29(\/|-)0?2)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/

     if(!re.exec(valor))    {
         return false;
     }else{
         return true;
     }
 }
 
function valHora(valor){
     re=/^(0[1-9]|1\d|2[0-3]):([0-5]\d):([0-5]\d)$/
     if(!re.exec(valor))    {
         return false;
     }else{
         return true;
     }
 }
 
function valFechaHora(valor){
	 fecha=valor.substr(0,10);
	 separador=valor.substr(10,1);
	 hora=valor.substr(11,8);
    /*alert(fecha);
	alert(separador);
	alert(hora);
	alert(valFecha(fecha));*/
	if(separador==' '){
		 if(valFecha(fecha)){//alert("fecha correcta");
			 if(valHora(hora)){//alert("hora correcta");
				 return true;
			 }else{
				 return false;
			 }
		 }else{
			 //alert("fecha incorrecta");
			 return false;
		 }
	}else{
		return false;
	}	 
 }
 
//FUENTE: http://tunait.com/javascript/?s=mascara#codigo 
//var patron = new Array(2,2,4)
//var patron2 = new Array(1,3,3,3,3)
function mascara(d,sep,pat,nums){
if(d.valant != d.value){
	val = d.value
	largo = val.length
	val = val.split(sep)
	val2 = ''
	for(r=0;r<val.length;r++){
		val2 += val[r]	
	}
	if(nums){
		for(z=0;z<val2.length;z++){
			if(isNaN(val2.charAt(z))){
				letra = new RegExp(val2.charAt(z),"g")
				val2 = val2.replace(letra,"")
			}
		}
	}
	val = ''
	val3 = new Array()
	for(s=0; s<pat.length; s++){
		val3[s] = val2.substring(0,pat[s])
		val2 = val2.substr(pat[s])
	}
	for(q=0;q<val3.length; q++){
		if(q ==0){
			val = val3[q]
		}
		else{
			if(val3[q] != ""){
				val += sep + val3[q]
				}
		}
	}
	d.value = val
	d.valant = val
	}
}
