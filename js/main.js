$(document).ready(function(){
    $('.NavLateral-DropDown').on('click', function(e){
        e.preventDefault();
        var DropMenu=$(this).next('ul');
        var CaretDown=$(this).children('i.NavLateral-CaretDown');
        DropMenu.slideToggle('fast');
        if(CaretDown.hasClass('NavLateral-CaretDownRotate')){
            CaretDown.removeClass('NavLateral-CaretDownRotate');    
        }else{
            CaretDown.addClass('NavLateral-CaretDownRotate');    
        }
         
    });
    $('.tooltipped').tooltip({delay: 50});
    $('.ShowHideMenu').on('click', function(){
        var MobileMenu=$('.NavLateral');
        if(MobileMenu.css('opacity')==="0"){
            MobileMenu.addClass('Show-menu');   
        }else{
            MobileMenu.removeClass('Show-menu'); 
        }   
    }); 
    $('.btn-ExitSystem').on('click', function(e){
        e.preventDefault();
        swal({ 
            title: "Estas seguro que deseas salir?",   
            text: "La sesión actual se cerrará y abandonará el sistema",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "SI",
            animation: "slide-from-top",   
            closeOnConfirm: false,
            cancelButtonText: "Cancelar"
        }, function(){   
            window.location='cerrarSesion.php'; 
        });
    }); 
    $('.btn-Perfil').on('click', function(e){
        e.preventDefault();
        var nombre = $(this).attr("nombre-perfil");
        swal({
            title: nombre,
            text: "<div class='row'><div class='col s12 center'><a class='btn confirm' href='#!'  onclick=\"javascript: swal.close(); setRun('vista/mantPerfilUsuario', '&id_clase=16', 'frame', 'carga', 'imgloading')\";>MI PERFIL</a></div></div><div class='row'><div class='col s12 center'><a class='btn' href='#!' onclick=\"swal.close(); setRun('vista/mantUsuarioClave', '&id_clase=16', 'frame', 'carga', 'imgloading');\">CAMBIAR CONTRASEÑA</a></div></div>",
            imageUrl: "assets/img/user.png",
            confirmButtonColor: "#d50000",
            confirmButtonText: "CERRAR",
            html: true
        });
    });
//    $('.btn-Search').on('click', function(e){
//        e.preventDefault();
//        swal({   
//            title: "What are you looking for?",   
//            text: "Write what you want",   
//            type: "input",   
//            showCancelButton: true,   
//            closeOnConfirm: false,   
//            animation: "slide-from-top",   
//            inputPlaceholder: "Write here",
//            confirmButtonText: "Search",
//            cancelButtonText: "Cancel" 
//        }, function(inputValue){   
//            if (inputValue === false) return false;      
//            if (inputValue === "") {     swal.showInputError("You must write something");     
//            return false   
//            }      
//            swal("Nice!", "You wrote: " + inputValue, "success"); 
//        });    
//    });
    $('.btn-Notification').on('click', function(){
        var NotificationArea=$('.NotificationArea');
        if(NotificationArea.hasClass('NotificationArea-show')){
            NotificationArea.removeClass('NotificationArea-show');
        }else{
            NotificationArea.addClass('NotificationArea-show');
        }
    });     
});
(function($){
    $(window).load(function(){
        $(".NavLateral-content").mCustomScrollbar({
            theme:"light-thin",
            scrollbarPosition: "inside",
            autoHideScrollbar: true,
            scrollButtons:{ enable: true }
        });
        $(".ContentPage, .NotificationArea").mCustomScrollbar({
            theme:"dark-thin",
            scrollbarPosition: "inside",
            autoHideScrollbar: true,
            scrollButtons:{ enable: true }
        });
    });
})(jQuery);