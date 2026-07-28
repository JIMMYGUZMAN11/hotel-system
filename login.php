<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/auth.php';

// Si ya inicio sesion, mandar directo al dashboard
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Ingrese usuario y contraseña.';
    } else {
        $stmt = $pdo->prepare('SELECT id_usuario, usuario, password_hash, nombre FROM usuario WHERE usuario = ? LIMIT 1');
        $stmt->execute([$usuario]);
        $fila = $stmt->fetch();

        if ($fila && password_verify($password, $fila['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $fila['id_usuario'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesion - Sistema Hotelero</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            padding: 36px 32px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 360px;
        }
        .login-box h1 {
            font-size: 20px;
            margin-bottom: 4px;
            text-align: center;
        }
        .login-box p.subtitulo {
            text-align: center;
            color: #667;
            font-size: 13px;
            margin-bottom: 24px;
        }
        .login-box label {
            display: block;
            margin-bottom: 14px;
            font-size: 14px;
        }
        .login-box input {
            width: 100%;
            padding: 9px 10px;
            margin-top: 5px;
            border: 1px solid #ccd;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .login-box button {
            width: 100%;
            margin-top: 8px;
        }
        .login-error {
            background: #fdecea;
            color: #b3261e;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <h1>🏨 Sistema de Gestion Hotelera</h1>
            <p class="subtitulo">Ingrese sus credenciales para continuar</p>

            <?php if ($error): ?>
                <div class="login-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <label>Usuario
                    <input type="text" name="usuario" required autofocus>
                </label>
                <label>Contraseña
                    <input type="password" name="password" required>
                </label>
                <button type="submit" class="btn-primario">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
