<?php
session_start();
require '../conexion.php';

$de = $_SESSION['usuario_id'];
$para = intval($_POST['para']);
$mensaje = trim($_POST['mensaje']);

if ($de && $para && $mensaje) {
  $stmt = $conn->prepare("INSERT INTO mensajes (de_id, para_id, contenido, fecha) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iis", $de, $para, $mensaje);
  $stmt->execute();
  echo "Mensaje enviado.";
} else {
  echo "Datos inválidos.";
}
