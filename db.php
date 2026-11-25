<?php
$servername = "localhost";
$username   = "root";         
$password   = "";              
$dbname     = "duolingo_sho";  

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "estado"  => false,
        "success" => false,
        "mensaje" => "❌ Conexión fallida con la base de datos: " . $conn->connect_error
    ]);
    exit;
}
$conn->set_charset("utf8mb4");