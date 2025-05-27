<?php
session_start();
header("Content-Type: text/plain");

require '../conexion.php';
require_once '../../estructuras/ColaPrioridad.php'; 

$libroId = intval($_POST['libro_id'] ?? 0);
$usuarioId = $_SESSION['usuario_id'] ?? 0;

if (!$usuarioId || !$libroId) {
    echo "Error: datos incompletos.";
    exit;
}

$resLibro = $conn->query("SELECT estado FROM libros WHERE id = $libroId");
if (!$resLibro || $resLibro->num_rows === 0) {
    echo "Libro no encontrado.";
    exit;
}

$estadoLibro = $resLibro->fetch_assoc()['estado'];
if ($estadoLibro === 'disponible') {
    echo "El libro está disponible. No necesitas unirte a la cola.";
    exit;
}

$yaEnCola = $conn->query("SELECT id FROM colas WHERE libro_id = $libroId AND usuario_id = $usuarioId AND atendido = 0");
if ($yaEnCola->num_rows > 0) {
    echo "Ya estás en la cola de espera para este libro.";
    exit;
}

$insert = $conn->prepare("INSERT INTO colas (libro_id, usuario_id) VALUES (?, ?)");
$insert->bind_param("ii", $libroId, $usuarioId);

if ($insert->execute()) {
    echo "Te has unido a la cola de espera correctamente.";
} else {
    echo "Error al unirte a la cola.";
}

// verificamos si el libro esta prestado, si uno mismo ya esta en la cola o si se va a unir a la cola