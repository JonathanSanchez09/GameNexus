<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "tienda_videojuegos");

    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "❌ Todos los campos son obligatorios.";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "❌ Las contraseñas no coinciden.";
        exit();
    }

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "❌ Este correo ya está registrado. <a href='../index.html'>Inicia sesión</a>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (email, password, username) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $hashed_password, $username);

        if ($stmt->execute()) {
            // Redirigir al login automáticamente
            header("Location: ../HTML/login.html");
            exit();
        } else {
            echo "❌ Error al registrar: " . $stmt->error;
        }
    }

    $conn->close();
} else {
    echo "Acceso denegado.";
}
