<?php
// Datos de conexión
$servername = "localhost";
$username = "root"; // usuario por defecto en XAMPP
$password = "";     // contraseña vacía por defecto
$dbname = "tualingo_otomi";


// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);


// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


// Obtener datos del formulario
$email = $_POST['email'];
$pass = $_POST['contraseña'];


// Preparar consulta segura
$stmt = $conn->prepare("SELECT * FROM usuario WHERE email = ? AND contraseña= ?");
$stmt->bind_param("ss", $email, $pass);
$stmt->execute();
$result = $stmt->get_result();


// Validar usuario
if ($result->num_rows > 0) {
 session_start();
    $_SESSION['usuario'] = $email;
    // Redirección con JavaScript para evitar el mensaje
    echo "<script>window.location.href='unidad1.html';</script>";
    exit();
} else {
    // Usuario no encontrado → mensaje
    echo "<script>alert('Usuario no encontrado'); window.location.href='login.html';</script>";
}


$stmt->close();
$conn->close();
?>
