<?php
$host = 'localhost';
$usuario = 'telemede_deseos';
$clave = 'Bv3n05D3530s*';
$bd = 'telemede_buenos-deseos';
$conex = mysql_connect($host, $usuario, $clave);
mysql_select_db($bd);


function comprobar_usuario($twitter = '', $correo = '')
{
	if($correo != '')
		$consulta = 'SELECT ID, Correo, Nombre, Estado FROM usuario WHERE Correo = "' . $correo . '"';	
	elseif($twitter != '')
		$consulta = 'SELECT ID, Twitter, Estado FROM usuario WHERE Twitter = "' . $twitter . '"';	
	else 
		return false;
	$q = mysql_query($consulta);
	return mysql_fetch_object($q);
}

function insertar_usuario($correo = '', $nombre = '', $twitter = '')
{
	if($correo != ''){
	 if(!comprobar_email($correo)) return false;
	 	$consulta = 'INSERT INTO usuario (Correo, Nombre, Estado) VALUES ("'. mysql_real_escape_string($correo) .'", "'. mysql_real_escape_string($nombre) .'",1)';
	}elseif($twitter != ''){
		$consulta = 'INSERT INTO usuario (Twitter, Estado) VALUES ("'. mysql_real_escape_string($twitter) .'", 1)';
	}else return false;
	$q = mysql_query($consulta);
	if($q) return mysql_insert_id();
	else return false;
}

function comprobar_lat_lon($lati, $long)
{
	if(!$lati || !$long) return false;
	$consulta = "SELECT ID FROM mensaje WHERE Latitud LIKE $lati AND Longitud LIKE $long";	
	$q = mysql_query($consulta);
	if(mysql_num_rows($q) > 0) return true;
	else return false;
}

function insertar_mensaje($Id_usuario, $lati, $long, $mensaje, $url)
{
	if($mensaje == '') return false;
	$lat = $lati;
	$lon = $long;
	$fecha = date('Y-m-d H:i:s');
	while(comprobar_lat_lon($lat, $lon))
	{
		$lat = $lat + 0.0005;
		$lon = $lon + 0.0005;
	}
	$consulta = 'INSERT INTO mensaje (Id_usuario, Latitud, Longitud, Fecha, Mensaje, Estado) VALUES (' . $Id_usuario . ', ' . $lat . ', ' . $lon . ', "' . $fecha . '", "' . mysql_real_escape_string($mensaje) . '", 1)';
	$q = mysql_query($consulta);
	if($q) return mysql_insert_id();
	else return false;
}
function insertar_url($Id_mensaje, $url)
{
	if($url == '') return false;
	$consulta = 'UPDATE mensaje SET Url = "' . $url . '" WHERE ID = ' . $Id_mensaje;
	$q = mysql_query($consulta);
	if($q) return true;
	else return false;
}
function obtener_mensajes()
{
	$consulta = 'SELECT *, m.ID AS "ID_mensaje", u.ID AS "ID_usuario" FROM mensaje AS m INNER JOIN usuario AS u ON u.ID = m.Id_usuario WHERE m.Estado = 1 ORDER BY m.ID';	
	$q = mysql_query($consulta);
	return $q;
}
function obtener_ultimo()
{
	$consulta = 'SELECT *, m.ID AS "ID_mensaje", u.ID AS "ID_usuario" FROM mensaje AS m INNER JOIN usuario AS u ON u.ID = m.Id_usuario WHERE m.Estado = 1 ORDER BY m.ID DESC LIMIT 1';	
	$q = mysql_query($consulta);
	return $q;
}
?>