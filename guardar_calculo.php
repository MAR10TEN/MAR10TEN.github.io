<?php
session_start();
// Indicamos que la respuesta ser芍 en formato JSON
header('Content-Type: application/json');

// 1. Verificamos que el usuario est谷 logueado y que la solicitud sea de tipo POST
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si no cumple, enviamos un error y terminamos el script
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

// 2. Leemos los datos enviados desde JavaScript (en formato JSON)
$data = json_decode(file_get_contents('php://input'), true);
$figura = $data['figura'] ?? null;

if (!$figura) {
    // Si no se envi車 el nombre de la figura, enviamos un error
    echo json_encode(['success' => false, 'message' => 'No se proporcion車 el nombre de la figura.']);
    exit;
}

// 3. Incluimos la configuraci車n de la base de datos
require_once 'config.php';

try {
    // 4. Conectamos a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $user_id = $_SESSION['user_id'];
    $fecha_uso = date('Y-m-d H:i:s'); // Obtenemos la fecha y hora actual

    // 5. Preparamos la consulta para insertar los datos de forma segura
    $stmt = $conn->prepare("INSERT INTO uso_figuras (user_id, figura, fecha_uso) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $figura, $fecha_uso);

    // 6. Ejecutamos la consulta y verificamos si fue exitosa
    if ($stmt->execute()) {
        // Si se guard車, enviamos una respuesta de 谷xito
        echo json_encode(['success' => true, 'message' => 'C芍lculo guardado correctamente.']);
    } else {
        // Si fall車, enviamos un error
        echo json_encode(['success' => false, 'message' => 'Error al guardar el c芍lculo.']);
    }

    // 7. Cerramos la conexi車n
    $stmt->close();
    $conn->close();

} catch (mysqli_sql_exception $e) {
    // En caso de un error de conexi車n, lo registramos (idealmente) y enviamos un error gen谷rico
    // error_log('Error de Base de Datos: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
}
?>
