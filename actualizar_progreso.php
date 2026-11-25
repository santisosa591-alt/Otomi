<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/db.php"; 

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["success" => false, "mensaje" => "Debes iniciar sesión"]);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);
$puntajeGanado = (int)($datos["experienciaGanada"] ?? 0);
$idUsuario     = (int)$_SESSION["id_usuario"];

$chk = $conn->prepare("SELECT 1 FROM progreso WHERE id_usuario = ?");
$chk->bind_param("i", $idUsuario);
$chk->execute();
$chk->store_result();
$existe = $chk->num_rows > 0;
$chk->close();

if (!$existe) {
    $ins = $conn->prepare("INSERT INTO progreso (id_usuario, experiencia, racha, porcentaje) VALUES (?, 0, 0, 0)");
    $ins->bind_param("i", $idUsuario);
    $ins->execute();
    $ins->close();
}

$stmt = $conn->prepare(
    "UPDATE progreso
     SET experiencia = experiencia + ?,
         racha = racha + 1,
         porcentaje = LEAST(porcentaje + 5, 100)
     WHERE id_usuario = ?"
);
$stmt->bind_param("ii", $puntajeGanado, $idUsuario);
$ok = $stmt->execute();

echo json_encode([
    "success" => $ok,
    "mensaje" => $ok ? "Progreso actualizado" : "No se pudo actualizar el progreso: " . $stmt->error
]);

$stmt->close();
$conn->close();