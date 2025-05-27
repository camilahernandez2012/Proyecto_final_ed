<?php
require '../conexion.php';

$titulo = trim($_POST['titulo']);
$autor = trim($_POST['autor']);
$anio = intval($_POST['anio']);
$categoria = trim($_POST['categoria']);

// Validar año
if ($anio < 1000 || $anio > 9999) {
    echo "Año inválido.";
    exit();
}

// Insertar libro de forma segura
$sql = "INSERT INTO libros (titulo, autor, anio, categoria, estado)
        VALUES (?, ?, ?, ?, 'disponible')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $titulo, $autor, $anio, $categoria);

if ($stmt->execute()) {
    echo "Libro agregado correctamente.";
} else {
    echo "Error al agregar libro: " . $conn->error;
}

//agregar libros a la lista
?>
