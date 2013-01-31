<?php
include('php/db.php');

$mensajes = obtener_ultimo();
$mensaje = mysql_fetch_object($mensajes);
echo json_encode($mensaje);
?>