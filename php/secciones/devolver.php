<?php
session_start();
require '../conexion.php';
require_once '../../estructuras/ColaPrioridad.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;
$libroId = intval($_POST['id']);

if (!$usuarioId || !$libroId) {
    echo "Error en los datos.";
    exit;
}

$conn->query("UPDATE prestamos 
              SET fecha_devolucion = CURDATE(), estado = 'devuelto' 
              WHERE libro_id = $libroId AND usuario_id = $usuarioId AND fecha_devolucion IS NULL");

$sqlCola = "SELECT * FROM colas WHERE libro_id = $libroId AND atendido = 0 ORDER BY fecha_entrada ASC LIMIT 1";
$resultadoCola = $conn->query($sqlCola);

if ($resultadoCola->num_rows > 0) {
    $espera = $resultadoCola->fetch_assoc();
    $nuevoUsuarioId = $espera['usuario_id'];
    $colaId = $espera['id'];

    $conn->query("INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, estado) 
                  VALUES ($nuevoUsuarioId, $libroId, CURDATE(), 'prestado')");

    $conn->query("UPDATE colas SET atendido = 1 WHERE id = $colaId");

    $conn->query("UPDATE libros SET estado = 'prestado' WHERE id = $libroId");

    echo "Libro devuelto. Fue asignado automáticamente al siguiente lector en espera.";
} else {
    $conn->query("UPDATE libros SET estado = 'disponible' WHERE id = $libroId");
    echo "Libro devuelto y ahora está disponible.";
}

// en este archivo se manejan os prestamos de los libros por medio de la cola de prioridad, verifica si la persona lo tiene, quien sigue en la cola
// a quien se le asignaria, atiende el llamado de la cola y finalmente si esta vacio lo pone disponible con normalidad
?>
