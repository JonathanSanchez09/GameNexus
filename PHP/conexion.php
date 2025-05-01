<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tienda_videojuegos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>