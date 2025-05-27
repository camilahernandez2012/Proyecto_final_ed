<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_de_datos = "biblioteca";

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// conexión con ek bd
?>
