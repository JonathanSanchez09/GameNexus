<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }
    
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos."]);
        exit();
    }
    
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            echo json_encode(["success" => true]);  // Login exitoso, no rediriges aquí, lo hace JS
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
