<?php
include('php/db.php');

$mensajes = obtener_mensajes();
$i = 1;
$resultado = '{';
while ($mensaje = mysql_fetch_object($mensajes)) {
  $resultado .= '"' . $i . '":' . json_encode($mensaje) . ',';
  $i++;
}
$resultado = substr($resultado, 0, -1);
$resultado .= '}';
echo $resultado;
?>