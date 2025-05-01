<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener y limpiar datos del formulario
    $nombre = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
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

    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "tienda_videojuegos");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error en la conexión."]);
        exit();
    }

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Este correo ya está registrado."]);
        exit();
    }

    // Encriptar la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado con éxito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario."]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
?>
