<?php 
require("php/config.php");  
require("php/twitteroauth/twitteroauth.php");  
session_start();
if($_SESSION['Mensaje'])
{
  $mensaje = $_SESSION['Mensaje'];
  unset($_SESSION['Mensaje']);
}
$validar_twitter = true;
if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){     
  // TwitterOAuth instance, with two new parameters we got in twitter_login.php
  $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
  // Let's request the access token
  $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
  $_SESSION['access_token'] = $access_token;
  $_SESSION['status'] = 'verified';
  $otro = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
  $result =  $otro->get('account/verify_credentials');
  if(!$result->errors){
    // Save it in a session var
    $validar_twitter = false;
  }else{
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);
    session_destroy();
    $validar_twitter = true;
  }
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Buenos Deseos Medellín 2013</title>
	<meta name="description" content="Llená con nosotros de buenos deseos la ciudad, deseamos que Medellín sea un hogar lleno de paz, armonía y fraternidad. ¿Cuáles son tus buenos deseos para la ciudad?">
	<meta name="author" content="Telemedellín">
  <link rel="shortcut icon" href="favicon.ico">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body> 
  <div id="container">
    <div id="map_canvas"></div>
    <div id="sidebar">
      <h1><img src="img/BuenosDeseos-banner-App.jpg" alt="Llená con nosotros de buenos deseos la ciudad" /></h1>
      <?php if($mensaje):?>
        <div class="aviso"><span><?php echo $mensaje; ?></span></div>
      <?php endif; ?>
      <p class="advertencia">
        Para dejar un mensaje debés permitir que la aplicación conozca tu localización. Así, tu sobre quedará ubicado en el mapa. 
      </p>
      <form id="envio_mensaje" action="guardar_mensaje.php" style="display:none">
        <div class="validar_usuario" <?php if(!$validar_twitter) echo 'style="display: none"';?>>
          <p class="val_twitter">
              <?php if($validar_twitter): ?>
                  <a href="autorizacion.php">Ingresá con Twitter</a>
              <?php endif; ?>
          </p>
          <p class="val_email">
              <a href="#">Ingresá con tu correo electrónico</a>
          </p>
        </div>
        <div class="formulario">
        <p class="volver" <?php if($validar_twitter) echo 'style="display: none"';?>>
          <a href="#">Volver</a>
        </p>
        <?php if(!$validar_twitter): ?>
        <p class="twitter">
            <img src=<?php echo '"'.$result->profile_image_url.'"'; ?> />
            <span>@<?php echo $result->screen_name; ?></span>
            <input type="hidden" name="twitter" value="<?php echo $result->screen_name; ?>" />
        </p>
        <?php endif; ?>
        <p class="email_field" style="display:none;">
          <label for="email">Correo electrónico:</label>
          <input id="email" name="email" type="email" disabled="disabled" />
        </p>
        <p class="nombre_field" style="display:none;">
          <label for="nombre">Nombre:</label>
          <input id="nombre" name="nombre" type="text" disabled="disabled" />
        </p>
        <p>
          <label for="mensaje">Mensaje:</label>
          <textarea id="mensaje" name="mensaje" <?php if($validar_twitter) echo 'disabled="disabled"';?> required ></textarea>
        </p>
        <p>
          <label for="Validacion">Cuánto es 3 + 2</label>
          <input id="Validacion" name="Validacion" type="text" class="required" size="5" <?php if($validar_twitter) echo 'disabled="disabled"';?> required />
        </p>
        <p class="no_tuit" <?php if($validar_twitter) echo 'style="display: none;"'?>>
          <input id="no_tuit" name="no_tuit" type="checkbox" value="No" <?php if($validar_twitter) echo 'disabled="disabled"';?> />
          <label for="no_tuit">No publicar este mensaje en mi perfil de Twitter</label>
        </p>
        <p>
          <input type="submit" id="submit" value="Enviar mensaje" <?php if($validar_twitter) echo 'disabled="disabled"';?> />
          <input type="hidden" name="Lat" id="Lat" value="" />
          <input type="hidden" name="Lon" id="Lon" value="" />
        </p>
        </div>
      </form>
    </div>
    <a id="telemedellin" href="http://www.telemedellin.tv" target="_blank"><img src="img/logo-tm-digital.png" alt="Telemedellín, Aquí te ves!" /></a>
  </div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>

<!--<script src="js/libs/bootstrap/bootstrap.min.js"></script>
<script src="js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
<script src="js/libs/jquery.scrollTo-1.4.3.1-min.js"></script>-->
<script src="http://maps.googleapis.com/maps/api/js?sensor=true&key=AIzaSyCnWqNysl3l24-FNU6K1lJT3xnokc6-krY" type="text/javascript"></script>
<script src="js/plugins.js"></script>
<script src="js/script.js"></script>
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-5650687-14']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>