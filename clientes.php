<?php $pagina = 'clientes'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <div class="panel">
            <h2>Registrar / Editar Cliente</h2>
            <div id="mensaje-clientes" class="mensaje"></div>
            <form id="form-cliente" class="form-grid">
                <label>Cedula
                    <input type="text" id="cedula" required maxlength="15" placeholder="1712345678">
                </label>
                <label>Nombres
                    <input type="text" id="nombres" required maxlength="80">
                </label>
                <label>Apellidos
                    <input type="text" id="apellidos" required maxlength="80">
                </label>
                <label>Telefono
                    <input type="text" id="telefono" required maxlength="20">
                </label>
                <label>Email
                    <input type="email" id="email" maxlength="100">
                </label>
                <label>Direccion
                    <input type="text" id="direccion" maxlength="150">
                </label>
                <div class="acciones-form">
                    <button type="submit" id="btn-guardar" class="btn-primario">Guardar</button>
                    <button type="button" id="btn-cancelar" class="btn-secundario" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Listado de Clientes</h2>
            <table id="tabla-clientes">
                <thead>
                    <tr>
                        <th>ID</th><th>Cedula</th><th>Nombre completo</th><th>Telefono</th><th>Email</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/comun.js"></script>
    <script src="assets/js/clientes.js"></script>
</body>
</html>
