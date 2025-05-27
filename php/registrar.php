<?php
require 'conexion.php';

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// limitar email del admin
if ($email === 'camilahernandezpena@gmail.com') {
    echo "Este correo está reservado para el administrador.";
    exit();
}

// regstro de lectores nuevos
$sql = "INSERT INTO usuarios (nombre, email, password, rol)
        VALUES ('$nombre', '$email', '$password', 'lector')";

if ($conn->query($sql) === TRUE) {
    echo "ok";
} else {
    echo "Error al registrar: " . $conn->error;
}
?>
