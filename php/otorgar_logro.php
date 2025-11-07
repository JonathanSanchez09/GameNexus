<?php
function otorgar_logro($conn, $usuario_id, $requisito) {
    // 1. Obtener el ID del logro
    $stmt_logro = $conn->prepare("SELECT id FROM logros WHERE requisito = ?");
    $stmt_logro->bind_param("s", $requisito);
    $stmt_logro->execute();
    $resultado_logro = $stmt_logro->get_result();
    $logro = $resultado_logro->fetch_assoc();
    $logro_id = $logro['id'];
    $stmt_logro->close();

    // 2. Verificar si el usuario ya tiene el logro
    $stmt_check = $conn->prepare("SELECT id FROM usuario_logros WHERE usuario_id = ? AND logro_id = ?");
    $stmt_check->bind_param("ii", $usuario_id, $logro_id);
    $stmt_check->execute();
    $resultado_check = $stmt_check->get_result();

    // 3. Si no lo tiene, otorgárselo
    if ($resultado_check->num_rows === 0) {
        $stmt_insert = $conn->prepare("INSERT INTO usuario_logros (usuario_id, logro_id) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $usuario_id, $logro_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        return true; // Retorna true si se otorgó el logro
    }
    $stmt_check->close();
    return false; // Retorna false si el usuario ya lo tenía
}
?>