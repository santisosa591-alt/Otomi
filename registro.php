<?php
header("Content-Type: application/json");

include("db.php");

$entrada = file_get_contents("php://input");
$data = json_decode($entrada, true);

// Verificar que lleguen todos los campos
if (!$data || !isset($data['nombre'], $data['email'], $data['contrasena'], $data['fecha_nac'], $data['pais'], $data['nivel'])) {
    echo json_encode(["estado" => false, "mensaje" => "Faltan datos"]);
    exit;
}

$nombre = $data['nombre'];
$email = $data['email'];
$contrasena = password_hash($data['contrasena'], PASSWORD_DEFAULT);
$fecha_nac = $data['fecha_nac'];
$pais = $data['pais'];
$nivel = $data['nivel'];
$fecha_registro = date("Y-m-d");

// Verificar correo repetido
$verificar = $conn->prepare("SELECT ID_usuario FROM usuario WHERE email = ?");
$verificar->bind_param("s", $email);
$verificar->execute();
$verificar->store_result();
if ($verificar->num_rows > 0) {
    echo json_encode(["estado" => false, "mensaje" => "❌ Este correo ya está registrado"]);
    $verificar->close();
    $conn->close();
    exit;
}
$verificar->close();

// Insertar en la tabla
$sql = "INSERT INTO usuario
(nombre, email, contraseña, fecha_registro, fecha_nac, pais, nivel)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssi",
    $nombre,
    $email,
    $contrasena,
    $fecha_registro,
    $fecha_nac,
    $pais,
    $nivel
);

if ($stmt->execute()) {
    echo json_encode(["estado" => true, "mensaje" => "✅ Registrado correctamente"]);
} else {
    echo json_encode(["estado" => false, "mensaje" => "❌ Error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
