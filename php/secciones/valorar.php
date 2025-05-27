<?php
session_start();
require '../conexion.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;
$libroId = intval($_POST['libro_id']);
$puntuacion = intval($_POST['valor']);

if (!$usuarioId || !$libroId || $puntuacion < 1 || $puntuacion > 5) {
    echo "Datos inválidos.";
    exit;
}

$conn->query("REPLACE INTO valoraciones (usuario_id, libro_id, puntuacion) VALUES ($usuarioId, $libroId, $puntuacion)");

// Calcula promedio de calificaciones de libros
$conn->query("
  UPDATE libros SET calificacion_promedio = (
    SELECT AVG(puntuacion) FROM valoraciones WHERE libro_id = $libroId
  ) WHERE id = $libroId
");

echo "Valoración registrada.";
