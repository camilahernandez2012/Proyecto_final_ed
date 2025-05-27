<?php
require '../conexion.php';

$id = intval($_POST['id']);

$sql = "DELETE FROM usuarios WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Usuario eliminado correctamente.";
} else {
    echo "Error al eliminar usuario: " . $conn->error;
}

//eliminacion de usuarios
?>
