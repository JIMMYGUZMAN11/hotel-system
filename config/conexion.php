<?php
/**
 * Conexion a la base de datos usando PDO
 * Ajusta $host, $db, $user, $pass segun tu entorno local (XAMPP: user=root, pass='')
 */

$host    = getenv('DB_HOST') ?: 'boo5nkwv1v9klykkbmn0-mysql.services.clever-cloud.com';
$db      = getenv('DB_NAME') ?: 'boo5nkwv1v9klykkbmn0';
$user    = getenv('DB_USER') ?: 'uyhrg2gpoupboipp';
$pass    = getenv('DB_PASS') ?: 'UY4biUhQtxhDHfGDo45W';
$port    = getenv('DB_PORT') ?: '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => true, 'mensaje' => 'Error de conexion a la base de datos: ' . $e->getMessage()]);
    exit;
}
