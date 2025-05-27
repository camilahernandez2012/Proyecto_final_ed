<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    if (isset($_SESSION['email']) && $_SESSION['email'] === 'camilahernandezpena@gmail.com') {
        echo "admin";
    } else {
        echo "lector";
    }
} else {
    echo "unauthenticated";
}
?>
