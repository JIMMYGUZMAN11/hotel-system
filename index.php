<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/auth.php';
requerirLogin();
$pagina = 'index';

$totalClientes    = $pdo->query('SELECT COUNT(*) FROM cliente')->fetchColumn();
$totalHabitaciones= $pdo->query('SELECT COUNT(*) FROM habitacion')->fetchColumn();
$habDisponibles   = $pdo->query("SELECT COUNT(*) FROM habitacion WHERE estado = 'Disponible'")->fetchColumn();
$reservasActivas  = $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado IN ('Pendiente','Confirmada')")->fetchColumn();
$ingresosTotales  = $pdo->query("SELECT COALESCE(SUM(total),0) FROM reserva WHERE estado != 'Cancelada'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Resumen General</h2>
            <div class="dashboard-grid">
                <div class="tarjeta">
                    <div class="numero"><?= $totalClientes ?></div>
                    <div class="etiqueta">Clientes registrados</div>
                </div>
                <div class="tarjeta">
                    <div class="numero"><?= $habDisponibles ?>/<?= $totalHabitaciones ?></div>
                    <div class="etiqueta">Habitaciones disponibles</div>
                </div>
                <div class="tarjeta">
                    <div class="numero"><?= $reservasActivas ?></div>
                    <div class="etiqueta">Reservas activas</div>
                </div>
                <div class="tarjeta">
                    <div class="numero">$<?= number_format($ingresosTotales, 2) ?></div>
                    <div class="etiqueta">Ingresos totales</div>
                </div>
            </div>
        </div>
        <div class="panel">
            <h2>Modulos del Sistema</h2>
            <p>Utilice el menu superior para gestionar clientes, tipos de habitacion, habitaciones,
            reservas y servicios adicionales. Cada modulo permite crear, editar, eliminar y consultar
            registros directamente sobre la base de datos <strong>hotel_db</strong>.</p>
        </div>
    </main>
</body>
</html>
