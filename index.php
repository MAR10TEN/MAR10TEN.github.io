
<?php
session_start();
// Incluimos nuestras credenciales y configuración
require_once 'config.php';

// Inicializar sesión de invitado si no existe
if (!isset($_SESSION['usuario']) || !isset($_SESSION['user_id'])) {
    $_SESSION['usuario'] = 'invitado';
    $_SESSION['user_id'] = 0;
}

// Variable para almacenar los items del historial
$historial_items = [];
$historial_error = null; // Para guardar un mensaje de error si algo falla

// --- LÓGICA DE LA BASE DE DATOS ---
// Solo intentamos conectar si el usuario está logueado
if ($_SESSION['user_id'] > 0) {
    try {
        // La conexión usa las constantes de config.php
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Es una buena práctica establecer el charset
        $conn->set_charset("utf8mb4");

        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT figura, fecha_uso FROM uso_figuras WHERE user_id = ? ORDER BY fecha_uso DESC LIMIT 10");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Guardamos los resultados en nuestro array
        while ($row = $result->fetch_assoc()) {
            $historial_items[] = $row;
        }

        $stmt->close();
        $conn->close();

    } catch (mysqli_sql_exception $e) {
        $historial_error = "No se pudo cargar el historial. Intente más tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Figuras Avanzada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
<style>
    .calc-button {
    display: block;
    background: #3498db;
    color: white;
    padding: 12px 24px;         /* Espaciado interno (más horizontal) */
    margin: 10px auto;          /* auto para centrar horizontalmente */
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    width: fit-content;         /* Que se ajuste al contenido */
    max-width: 300px;           /* O cualquier otro límite */
    text-align: center;
    transition: background 0.3s;
    }

    .calc-button:hover {
    background: #2980b9;
    }
</style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Calculadora de Áreas y Perímetros</h1>
        <p class="text-end"><small>Usuario: <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></small></p>

        <form id="calculadora-form">
            <div class="mb-3">
                <label class="form-label"><b>1. Seleccione una figura</b></label>
                <select id="figura" class="form-select" required>
                    <option value="circulo">Círculo</option>
                    <option value="cuadrado">Cuadrado</option>
                    <option value="triangulo" selected>Triángulo</option>
                    <option value="pentagono">Pentágono</option>
                    <option value="rombo">Rombo</option>
                    <option value="dodecagono">Dodecágono</option>
                    <option value="dodecaedro">Dodecaedro</option>
                </select>
            </div>
            <div class="text-center mb-4">
                <canvas id="animationCanvas" width="300" height="200"></canvas>
            </div>
            <!-- Contenedor para los campos de entrada -->
            <div id="campos-calculo"></div>
            
            <!-- Contenedor para errores específicos del triángulo -->
            <div id="error-triangulo" class="alert alert-danger mt-3" style="display:none;"></div>

            <button type="submit" class="btn btn-primary mt-3 w-100">Calcular</button>
        </form>

        <div id="resultado" class="alert mt-4" style="display:none; white-space: pre-wrap;"></div>
    </div>

    <hr class="mt-5">
    
    <div class="container mb-5">
        <h4>Historial de figuras usadas</h4>
        <ul id="historial-lista" class="list-group">
        <?php
        if ($_SESSION['user_id'] > 0) {
            if ($historial_error) {
                echo "<li class='list-group-item text-danger'>{$historial_error}</li>";
            } elseif (empty($historial_items)) {
                echo "<li class='list-group-item placeholder-glow'><span class='placeholder col-8'></span></li>";
            } else {
                foreach ($historial_items as $item) {
                    $fecha = new DateTime($item['fecha_uso']);
                    echo "<li class='list-group-item'>" . htmlspecialchars($item['figura']) . " <span class='text-muted small'>(" . $fecha->format('d/m/Y H:i') . ")</span></li>";
                }
            }
        } else {
            echo "<li class='list-group-item'>Historial no disponible para invitados.</li>";
        }
        ?>
        </ul>
<a href="/login/index.php" class="calc-button">Ir al menú</a>  <!-- ✅ Ruta absoluta -->

    </div>
    <script src="js/main.js"></script>
    <script src="js/animacion.js"></script>

</body>
</html>

