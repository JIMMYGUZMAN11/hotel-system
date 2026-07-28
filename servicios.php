<?php $pagina = 'servicios'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Registrar / Editar Servicio</h2>
            <div id="mensaje-servicios" class="mensaje"></div>
            <form id="form-servicio" class="form-grid">
                <label>Nombre
                    <input type="text" id="nombre" required maxlength="80" placeholder="Ej: Desayuno">
                </label>
                <label>Descripcion
                    <input type="text" id="descripcion" maxlength="200">
                </label>
                <label>Precio ($)
                    <input type="number" id="precio" required min="0.01" step="0.01">
                </label>
                <div class="acciones-form">
                    <button type="submit" id="btn-guardar" class="btn-primario">Guardar</button>
                    <button type="button" id="btn-cancelar" class="btn-secundario" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Listado de Servicios</h2>
            <table id="tabla-servicios">
                <thead>
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Descripcion</th><th>Precio</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/servicios.js"></script>
</body>
</html>
