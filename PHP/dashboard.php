<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html"); // Redirige al login si no está logueado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Principal</title>
</head>
<body>
  <h1>¡Hola, <?php echo $_SESSION['email']; ?>! Bienvenido 🎮</h1>
  <a href="logout.php">Cerrar sesión</a>
</body>
</html>
