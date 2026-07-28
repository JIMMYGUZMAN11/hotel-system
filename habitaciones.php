<?php require_once __DIR__ . '/config/auth.php'; requerirLogin(); $pagina = 'habitaciones'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Habitaciones - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Registrar / Editar Habitacion</h2>
            <div id="mensaje-habitaciones" class="mensaje"></div>
            <form id="form-habitacion" class="form-grid">
                <label>Numero
                    <input type="text" id="numero" required maxlength="10" placeholder="Ej: 305">
                </label>
                <label>Piso
                    <input type="number" id="piso" required min="0" step="1">
                </label>
                <label>Tipo de habitacion
                    <select id="id_tipo" required></select>
                </label>
                <label>Estado
                    <select id="estado" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupada">Ocupada</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                    </select>
                </label>
                <div class="acciones-form">
                    <button type="submit" id="btn-guardar" class="btn-primario">Guardar</button>
                    <button type="button" id="btn-cancelar" class="btn-secundario" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Listado de Habitaciones</h2>
            <table id="tabla-habitaciones">
                <thead>
                    <tr>
                        <th>ID</th><th>Numero</th><th>Piso</th><th>Tipo</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/habitaciones.js"></script>
</body>
</html>
