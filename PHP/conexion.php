<?php
$servername = "db";  // <-- Esto cambia de 'localhost' a 'db'
$username = "usuario";  // <-- El mismo que definiste en docker-compose.yml
$password = "contrasena";
$dbname = "tienda_videojuegos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>
