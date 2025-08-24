<?php
// Mostrar errores para depuración (sólo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }

    // **Usuario y contraseña deben coincidir con docker-compose.yml**
    $conn = new mysqli("db", "usuario", "contrasena", "tienda_videojuegos");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos."]);
        exit();
    }

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la consulta SQL."]);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nombre'] = $usuario['nombre'];
            echo json_encode(["success" => true]);
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "El usuario no existe."]);
        exit();
    }

    $conn->close();

} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
