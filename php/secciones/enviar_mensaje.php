<?php
session_start();
require '../conexion.php';

$de = $_SESSION['usuario_id'] ?? 0;
$para = $_POST['para'] ?? 0;
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$de || !$para || $mensaje === '') {
  echo "Datos incompletos.";
  exit;
}

$stmt = $conn->prepare("INSERT INTO mensajes (de_id, para_id, contenido, fecha_envio) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $de, $para, $mensaje);
if ($stmt->execute()) {
  echo "Mensaje enviado.";
} else {
  echo "Error al enviar mensaje.";
}
$stmt->close();
