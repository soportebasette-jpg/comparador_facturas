<?php
// ============================================================
// guardar_lead_comparador.php
// Recibe nombre y teléfono desde el comparador y los guarda
// en la tabla lead_comparador_facturas de tu base de datos IONOS.
// ============================================================

// --- CORS: permite que el comparador (en GitHub Pages) pueda llamar a este script ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Responde rápido a la petición de comprobación que hace el navegador (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

// ============================================================
// DATOS DE CONEXIÓN — RELLENA ESTO CON TUS DATOS REALES DE IONOS
// Los encuentras en: Panel de IONOS > Bases de datos > tu base "lead"
// ============================================================
$DB_HOST = "TU_HOST_MYSQL_IONOS";      // ej: db5012345678.hosting-data.io
$DB_NAME = "lead";                      // tu base de datos existente
$DB_USER = "TU_USUARIO_MYSQL";          // ej: dbu1234567
$DB_PASS = "TU_CONTRASEÑA_MYSQL";

// ============================================================
// Leer datos enviados desde el formulario (JSON)
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);

$nombre = isset($input['nombre']) ? trim($input['nombre']) : '';
$telefono = isset($input['telefono']) ? trim($input['telefono']) : '';

// Validación básica
if ($nombre === '' || $telefono === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan datos (nombre o teléfono)']);
    exit;
}

if (mb_strlen($nombre) > 100) {
    $nombre = mb_substr($nombre, 0, 100);
}
if (strlen($telefono) > 20) {
    $telefono = substr($telefono, 0, 20);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? null;

// ============================================================
// Conexión e inserción
// ============================================================
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare(
        "INSERT INTO lead_comparador_facturas (nombre, telefono, ip_origen) VALUES (:nombre, :telefono, :ip)"
    );
    $stmt->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':ip' => $ip
    ]);

    echo json_encode(['ok' => true, 'mensaje' => 'Lead guardado correctamente']);

} catch (PDOException $e) {
    http_response_code(500);
    // En producción no mostramos el detalle del error al usuario final
    echo json_encode(['ok' => false, 'error' => 'Error al guardar en la base de datos']);
    // Si quieres depurar, puedes registrar $e->getMessage() en un log en vez de mostrarlo
}
