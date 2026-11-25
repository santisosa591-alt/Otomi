<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = $_POST;
}

$email      = trim($data["Email"] ?? $data["email"] ?? "");
$contrasena = $data["Contrasena"] ?? $data["contrasena"] ?? "";

if ($email === "" || $contrasena === "") {
    echo json_encode(["estado" => false, "mensaje" => "❌ Ingresa email y contraseña"]);
    exit;
}

$stmt = $conn->prepare("SELECT Id_usuario, nombre, contrasena, nivel FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$u = $res->fetch_assoc();

if ($u && password_verify($contrasena, $u["contrasena"])) {
    session_regenerate_id(true);
    $_SESSION["id_usuario"] = $u["Id_usuario"];
    $_SESSION["nombre"]     = $u["nombre"];
    $_SESSION["nivel"]      = $u["nivel"];
    echo json_encode(["estado" => true, "mensaje" => "✅ Bienvenido, " . $u["nombre"], "redirect" => "menu.html"]);
} else {
    echo json_encode(["estado" => false, "mensaje" => "❌ Email o contraseña incorrectos"]);
}

$stmt->close();
$conn->close();
