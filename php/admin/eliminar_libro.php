<?php
require '../conexion.php';

$id = $_POST['id'];

$sql = "DELETE FROM libros WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Libro eliminado.";
} else {
    echo "Error al eliminar libro.";
}

//eliminacionde libros
?>
