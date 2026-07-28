<?php require_once __DIR__ . '/config/auth.php'; requerirLogin(); $pagina = 'tipos'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Habitacion - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Registrar / Editar Tipo de Habitacion</h2>
            <div id="mensaje-tipos" class="mensaje"></div>
            <form id="form-tipo" class="form-grid">
                <label>Nombre
                    <input type="text" id="nombre" required maxlength="50" placeholder="Ej: Suite">
                </label>
                <label>Descripcion
                    <input type="text" id="descripcion" maxlength="200">
                </label>
                <label>Precio por noche ($)
                    <input type="number" id="precio_noche" required min="0.01" step="0.01">
                </label>
                <label>Capacidad (personas)
                    <input type="number" id="capacidad" required min="1" step="1">
                </label>
                <div class="acciones-form">
                    <button type="submit" id="btn-guardar" class="btn-primario">Guardar</button>
                    <button type="button" id="btn-cancelar" class="btn-secundario" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Listado de Tipos de Habitacion</h2>
            <table id="tabla-tipos">
                <thead>
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Descripcion</th><th>Precio/noche</th><th>Capacidad</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/tipos_habitacion.js"></script>
</body>
</html>
