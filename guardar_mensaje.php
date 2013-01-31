<?php
require("php/config.php");  
require("php/twitteroauth/twitteroauth.php");  
session_start();
if($_POST['Lat'] == '' || $_POST['Lon'] == '' || $_POST['Validacion'] != 5){
  if(!isset($_POST['ajax']))
  {
	 $_SESSION['Mensaje'] = 'Ocurrió un error enviando el mensaje';
	 header('Location: index.php');
  }else{
    echo 'Ocurrió un error enviando la información_';
    exit;
  }
}

include('php/db.php');
include('php/bitly.php');

$email = trim($_POST['email']);
$nombre = trim($_POST['nombre']);
$twitter = trim($_POST['twitter']);
$lat = $_POST['Lat'];
$lon = $_POST['Lon'];
$mensaje = trim($_POST['mensaje']);

//Compruebo si el usuario existe
$Id_usuario = comprobar_usuario($twitter ,$email);

if(!$Id_usuario)
	$Id_usuario = insertar_usuario($email, $nombre, $twitter);
else
  $Id_usuario = $Id_usuario->ID;

$Id_mensaje = insertar_mensaje($Id_usuario, $lat, $lon, $mensaje, $url);

//$url = 'http://bit.ly/TskpKA';
$url = acortar_url($Id_mensaje);
insertar_url($Id_mensaje, $url);

if($twitter != '' && !isset($_POST['no_tuit']) && !$_POST['no_tuit'] == 'No')
{
  $url_lenght = count($url);
  $tuit = substr($mensaje, 0, (121 - $url_lenght)).'... ' . $url . ' #DeseosMed2013';
  $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
  $result = $twitteroauth->post('statuses/update', 
    array('status' => $tuit, 
          'display_coordinates' => 'true',
          'lat' => $lat,
          'long' => $lon)
  );
}

if($Id_mensaje)
{
  $_SESSION['Mensaje'] = 'El mensaje se guardó correctamente, compartilo: <a href="' . $url . '" target="_blank">'.$url.'</a>';
}else
{
  $_SESSION['Mensaje'] = 'El mensaje no se pudo guardar';
}

if(!$_POST['ajax'])
{
  header('Location: index.php');
}else
{
  echo $_SESSION['Mensaje'];
  unset($_SESSION['Mensaje']);  
}

//header('Location: index.php');

function acortar_url($Id_mensaje = 0)
{
  if($Id_mensaje == 0) return false;
  $url = 'http://deseos2013.telemedellin.tv/';
  $newurl = $url . '#' . $Id_mensaje;
  $surl = bitly_v3_shorten($newurl);
  return $surl['url'];
}

function comprobar_email($email){
    $mail_correcto = 0;
    //compruebo unas cosas primeras
    if ((strlen($email) >= 10) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
       if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
          //miro si tiene caracter .
          if (substr_count($email,".")>= 1){
             //obtengo la terminacion del dominio
             $term_dom = substr(strrchr ($email, '.'),1);
             //compruebo que la terminación del dominio sea correcta
             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                //compruebo que lo de antes del dominio sea correcto
                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                if ($caracter_ult != "@" && $caracter_ult != "."){
                   $mail_correcto = 1;
                }
             }
          }
       }
    }
    if ($mail_correcto)
       return true;
    else
       return false;
} 

?>