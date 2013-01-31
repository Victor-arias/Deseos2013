/*
- Zoom a 13
- 
- El usuario pone Nombre, mail y mensaje (Puede publicar las veces que desee)
- Salen los pines en el mapa y al dar clic sale el globo con el mensaje
*/
var map;
var ubicacion_usuario;
var markers = new Array();
var highestZIndex = 0;
var hash = window.location.hash;
$(document).ready(function(){
    //Carga del mapa
    google.maps.event.addDomListener(window, 'load', initialize);
    $('.volver a').click(function(){
        location.reload();
    });
    //Si elige validarse con el correo electrónico
    $('.val_email a').click(function(){
        //Primero oculto los botones para elegir twitter o correo
        $('.validar_usuario').hide('fast');
        $('.volver').show();
        $('.email_field').show();
        $('.nombre_field').show();
        $('#email').removeAttr("disabled");
        $('#nombre').removeAttr("disabled");
        $('#mensaje').removeAttr("disabled");
        $('#Validacion').removeAttr("disabled");
        $('#submit').removeAttr("disabled");
    });
    
    
    //Envío del formulario
    $('#submit').click(function(e){
        if(validarEmail($('#email').val())){
            if($('#Validacion').val() == 5){
                var destino = 'guardar_mensaje.php';
                var datos = $('#envio_mensaje').serialize();
                datos += '&ajax=true';
                $.post(destino, datos, function(resultado){
                    $('#envio_mensaje').empty();
                    $('#sidebar').append(generar_aviso(resultado));
                    mostrarUltimoMensaje();
                    $.scrollTo('#map_canvas', 1000);
                });
            }else{
                $('#sidebar').prepend(generar_aviso('Ooops! La suma es incorrecta!'));
                $.scrollTo('#sidebar', 1000);
            }
        }else{
            //mensaje de error
            $('#sidebar').prepend(generar_aviso('Ooops! Parece que el correo electrónico no es correcto'));
            $.scrollTo('#sidebar', 1000);
        }
        e.preventDefault();
        return false;
    });
});

function initialize() {
    //Opciones del mapa a crear
    var mapOptions = {
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
        position: google.maps.ControlPosition.TOP_RIGHT
      },
      panControl: true,
      panControlOptions: {
        position: google.maps.ControlPosition.TOP_RIGHT
      },
      zoomControl: true,
      zoomControlOptions: {
        style: google.maps.ZoomControlStyle.LARGE,
        position: google.maps.ControlPosition.TOP_RIGHT
      },
      scaleControl: true,
      scaleControlOptions: {
        position: google.maps.ControlPosition.RIGHT_TOP
      },
      streetViewControl: false,
      streetViewControlOptions: {
        position: google.maps.ControlPosition.TOP_RIGHT
      }
    };
    //Inicialización del mapa
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    //Posición de medellín
    var medellin = new google.maps.LatLng(6.235925, -75.575137);
    //Centro el mapa en la ubicación actual
    map.setCenter(medellin);
    var iconos = new Array();
    iconos[0] = new google.maps.MarkerImage('img/sobre-1.png',
                    // This marker is 32 pixels wide by 37 pixels tall.
                    new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 18,42.
                    new google.maps.Point(32, 37)
                );

    iconos[1] = new google.maps.MarkerImage('img/sobre-2.png',
                    // This marker is 32 pixels wide by 37 pixels tall.
                    new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 18,42.
                    new google.maps.Point(32, 37)
                );
    iconos[2] = new google.maps.MarkerImage('img/sobre-3.png',
                    // This marker is 32 pixels wide by 37 pixels tall.
                    new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 18,42.
                    new google.maps.Point(32, 37)
                );

    iconos[3] = new google.maps.MarkerImage('img/sobre-4.png',
                    // This marker is 32 pixels wide by 37 pixels tall.
                    new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 18,42.
                    new google.maps.Point(32, 37)
                );
    var icono_usuario = 

                new google.maps.MarkerImage('img/smiley_happy.png',
                    // This marker is 32 pixels wide by 37 pixels tall.
                    new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 18,42.
                    new google.maps.Point(32, 37)
                );

    var i = 1;
    //Traigo los mensajes
    $.getJSON('obtener_mensajes.php', function(resultado){
        var mensajes = resultado;

        $.each(mensajes, function(key, value) {
            //console.log(value.Mensaje);
            i = i % 4;
            var marker = new google.maps.Marker({
                animation: google.maps.Animation.DROP, 
                icon: iconos[i],
                map: map,
                position: new google.maps.LatLng(Number(value.Latitud), Number(value.Longitud)),
                title: value.Mensaje
            });
            var nombre = '';
            if(value.Twitter != null){ nombre = value.Twitter; }
            else{ nombre = value.Nombre; }
            infoWindow = addInfoWindow(marker, nombre, value.Mensaje, value.Url, value.Twitter);
            id = hash.substr(1);
            if(id == value.ID_mensaje)
            {
                map.setCenter(marker.getPosition());
                infoWindow.open(map, marker);
            }
            i++;
        });

    });

    // Intentar la geolocalización por HTML5
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            $('.advertencia').hide();
            $('#envio_mensaje').show();
            $.scrollTo('#sidebar', 1000);
            //Posición del usuario
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            var pos = new google.maps.LatLng(latitude, longitude);
            $('#Lat').val(latitude);
            $('#Lon').val(longitude);
            //Inserto un marcador de la ubicación del usuario
            ubicacion_usuario = new google.maps.Marker({
                animation: google.maps.Animation.DROP,
                map: map,
                position: pos,
                title: 'Aquí te ves'
            });

            map.setCenter(pos);
        
            }, function() {
                handleNoGeolocation(true);
        });
    } else {
      // Browser doesn't support Geolocation
      handleNoGeolocation(false);
    }
     

}//initialize
function mostrarUltimoMensaje(){
    $.getJSON('obtener_ultimo.php', function(resultado){
        var iconito = new google.maps.MarkerImage('img/sobre-4.png',
            new google.maps.Size(32, 37),
            new google.maps.Point(0,0),
            new google.maps.Point(32, 37)
        );
        ubicacion_usuario.setMap(null);
        var marker = new google.maps.Marker({
            animation: google.maps.Animation.DROP,
            icon: iconito, 
            map: map,
            position: new google.maps.LatLng(Number(resultado.Latitud), Number(resultado.Longitud)),
            title: resultado.Mensaje
        });
        var nombre = '';
        if(resultado.Twitter != null){ nombre = resultado.Twitter; }
        else{ nombre = resultado.Nombre; }
        infowindow = addInfoWindow(marker, nombre, resultado.Mensaje, resultado.Url, resultado.Twitter);
        infowindow.open(map, marker);
        map.setCenter(marker.getPosition());
    });
}
function addInfoWindow(marker, name, message, url, twitter) {
    var info = generar_mensaje(name, message, url, twitter);
    getHighestZIndex();
    var infoWindow = new google.maps.InfoWindow({
        content: info,
        zIndex: highestZIndex + 1
    });
    google.maps.event.addListener(marker, 'click', function() {
        infoWindow.close();
        getHighestZIndex();
        infoWindow.setZIndex(highestZIndex + 1);
        infoWindow.open(map, marker);
        marker.setOptions({zIndex:highestZIndex+1});
    });
    return infoWindow;
}

function handleNoGeolocation(errorFlag) {
    if (errorFlag) {
      var content = 'Ooops! Parece que falló el servicio para saber dónde estás ubicado.';
    } else {
      var content = 'Ooops! Parece que tu navegador de internet no permite saber dónde estás ubicado.';
    }

    var options = {
      map: map,
      position: new google.maps.LatLng(6.235925, -75.575137),
      content: content
    };

    var infowindow = new google.maps.InfoWindow(options);
    map.setCenter(options.position);
}
function validarEmail(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if( !emailReg.test( email ) ) {
        return false;
    } else {
        return true;
    }
}
function generar_aviso(mensaje){
    html = '<p class="aviso">';
    html += '<span>';
    html += mensaje; 
    html += '</span>';
    html += '</p>';
    return html;
}
function generar_mensaje(nombre, mensaje, url, twitter){
    if(twitter != null)
    {
        via = "via="+nombre;
    }else
    {
        via = '';
    }
    html = '<style type="text/css">';
    html += '   .mensaje{border: 2px dashed #CCC; color: #000; font-size: 14px; margin: 5px; padding: 5px 5px 15px 5px;} ';
    html += '   .name{background: url(img/cupcake.png) no-repeat; color: #196F74; font-size: 16px; height: 25px; padding-left: 50px; padding-top: 15px;} ';
    html += '   .texto{padding: 0 10px;}';
    html += '</style>';
    html += '<div class="mensaje">';
    html += '   <p class="name">';
    html += '       <strong>';
    html += nombre;
    html += '       </strong>';
    html += '   </p>';
    html += '   <p class="texto">';
    html += mensaje;
    html += '   </p>';
    html += '   <p class="redes">';
    html += '   <a href="https://twitter.com/intent/tweet?text='+mensaje.substring(-80, 80)+'&hashtags=DeseosMed2013&url='+url+'&'+via+'">Compartilo en Twitter</a>';
    html += '   <a target="_blank" type="button_count" name="fb_share" href="https://www.facebook.com/sharer/sharer.php?u='+url+'&t=Buenos Deseos Medellín 2013">Compartilo en Facebook</a>';
    html += '   <script src=”http://static.ak.fbcdn.net/connect.php/js/FB.Share” type=”text/javascript”></script>';
    html += '   </p>';
    html += '</div>';
    return html;
}
//MIRAR http://stackoverflow.com/questions/9339431/changing-z-index-of-marker-on-hover-to-make-it-visible
function getHighestZIndex() {
    // if we haven't previously got the highest zIndex
    // save it as no need to do it multiple times
    if (highestZIndex==0) {
        if (markers.length>0) {
            for (var i=0; i<markers.length; i++) {
                tempZIndex = markers[i].getZIndex();
                if (tempZIndex>highestZIndex) {
                    highestZIndex = tempZIndex;
                }
            }
        }
    }
    return highestZIndex;
}