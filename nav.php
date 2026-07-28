<header class="topbar">
    <h1>🏨 Sistema de Gestion Hotelera</h1>
    <div class="sesion-info">
        <span>👤 <?= htmlspecialchars(usuarioActual() ?? '') ?></span>
        <a href="logout.php" class="btn-salir">Cerrar sesion</a>
    </div>
</header>
<nav class="menu">
    <a href="index.php" class="<?= $pagina === 'index' ? 'activo' : '' ?>">Inicio</a>
    <a href="clientes.php" class="<?= $pagina === 'clientes' ? 'activo' : '' ?>">Clientes</a>
    <a href="tipos_habitacion.php" class="<?= $pagina === 'tipos' ? 'activo' : '' ?>">Tipos de Habitacion</a>
    <a href="habitaciones.php" class="<?= $pagina === 'habitaciones' ? 'activo' : '' ?>">Habitaciones</a>
    <a href="reservas.php" class="<?= $pagina === 'reservas' ? 'activo' : '' ?>">Reservas</a>
    <a href="servicios.php" class="<?= $pagina === 'servicios' ? 'activo' : '' ?>">Servicios</a>
</nav>
