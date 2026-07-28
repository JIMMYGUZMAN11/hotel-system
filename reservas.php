<?php $pagina = 'reservas'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Registrar / Editar Reserva</h2>
            <p style="font-size:13px;color:#667;">El total se calcula automaticamente segun el precio por
            noche del tipo de habitacion y el numero de noches entre las fechas seleccionadas.</p>
            <div id="mensaje-reservas" class="mensaje"></div>
            <form id="form-reserva" class="form-grid">
                <label>Cliente
                    <select id="id_cliente" required></select>
                </label>
                <label>Habitacion
                    <select id="id_habitacion" required></select>
                </label>
                <label>Fecha de entrada
                    <input type="date" id="fecha_entrada" required>
                </label>
                <label>Fecha de salida
                    <input type="date" id="fecha_salida" required>
                </label>
                <label>Estado
                    <select id="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Finalizada">Finalizada</option>
                    </select>
                </label>
                <div class="acciones-form">
                    <button type="submit" id="btn-guardar" class="btn-primario">Guardar</button>
                    <button type="button" id="btn-cancelar" class="btn-secundario" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Listado de Reservas</h2>
            <table id="tabla-reservas">
                <thead>
                    <tr>
                        <th>ID</th><th>Cliente</th><th>Habitacion</th><th>Entrada</th><th>Salida</th>
                        <th>Estado</th><th>Total</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/reservas.js"></script>
</body>
</html>
