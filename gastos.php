<?php require_once __DIR__ . '/config/auth.php'; requerirLogin(); $pagina = 'reservas'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos de Reserva - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2 id="titulo-reserva">Gastos de la reserva</h2>
            <div id="mensaje-gastos" class="mensaje"></div>
            <form id="form-gasto" class="form-grid">
                <input type="hidden" id="id_reserva">
                <label>Servicio
                    <select id="id_servicio" required></select>
                </label>
                <label>Cantidad
                    <input type="number" id="cantidad" required min="1" step="1" value="1">
                </label>
                <div class="acciones-form">
                    <button type="submit" class="btn-primario">Agregar gasto</button>
                    <a href="reservas.php"><button type="button" class="btn-secundario">Volver a Reservas</button></a>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Gastos registrados — Total: <span id="total-gastos">$0.00</span></h2>
            <table id="tabla-gastos">
                <thead>
                    <tr>
                        <th>ID</th><th>Servicio</th><th>Cantidad</th><th>Precio unit.</th>
                        <th>Subtotal</th><th>Fecha</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/gastos.js"></script>
</body>
</html>
