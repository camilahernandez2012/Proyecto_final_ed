<?php
session_start();
require '../conexion.php';

$idLibro = intval($_POST['id']);
$usuarioId = $_SESSION['usuario_id'] ?? 0;

$libro = $conn->query("SELECT estado FROM libros WHERE id = $idLibro")->fetch_assoc();

if (!$libro || $libro['estado'] !== 'disponible') {
  echo "Este libro ya no está disponible.";
  exit();
}

$conn->query("INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo) VALUES ($usuarioId, $idLibro, CURDATE())");

$conn->query("UPDATE libros SET estado = 'prestado' WHERE id = $idLibro");

echo "Libro alquilado exitosamente.";

// verificacion, asignacion y alquila un libro
