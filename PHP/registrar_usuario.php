<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nombre) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }

    if (strlen($nombre) < 3) {
        echo json_encode(["success" => false, "message" => "El nombre debe tener al menos 3 caracteres."]);
        exit();
    }

    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "El correo electrónico no es válido."]);
        exit();
    }

    $conn = new mysqli("db", "usuario", "contrasena", "tienda_videojuegos");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error en la conexión: " . $conn->connect_error]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Este correo ya está registrado."]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error al preparar la inserción: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("sss", $nombre, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado con éxito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario: " . $stmt->error]);
    }

    $conn->close();

} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
?>
