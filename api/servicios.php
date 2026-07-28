<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';
require_once __DIR__ . '/../config/auth.php';
requerirLoginApi();

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM servicio WHERE id_servicio = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $s = $stmt->fetch();
            if (!$s) errorJson('Servicio no encontrado.', 404);
            respuestaJson($s);
        } else {
            $stmt = $pdo->query('SELECT * FROM servicio ORDER BY id_servicio');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['nombre', 'precio']);
        validarNumerico($data['precio'], 'precio');
        if ($data['precio'] <= 0) errorJson('El precio debe ser mayor a 0.', 422);

        try {
            $stmt = $pdo->prepare('INSERT INTO servicio (nombre, descripcion, precio) VALUES (:nombre, :descripcion, :precio)');
            $stmt->execute([
                ':nombre'      => $data['nombre'],
                ':descripcion' => $data['descripcion'] ?? null,
                ':precio'      => $data['precio'],
            ]);
            respuestaJson(['mensaje' => 'Servicio creado correctamente.', 'id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            errorJson('Error al crear el servicio: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $data = leerEntradaJson();
        validarCampos($data, ['id_servicio', 'nombre', 'precio']);
        validarNumerico($data['precio'], 'precio');

        try {
            $stmt = $pdo->prepare('UPDATE servicio SET nombre = :nombre, descripcion = :descripcion, precio = :precio
                                   WHERE id_servicio = :id');
            $stmt->execute([
                ':nombre'      => $data['nombre'],
                ':descripcion' => $data['descripcion'] ?? null,
                ':precio'      => $data['precio'],
                ':id'          => $data['id_servicio'],
            ]);
            if ($stmt->rowCount() === 0) errorJson('Servicio no encontrado o sin cambios.', 404);
            respuestaJson(['mensaje' => 'Servicio actualizado correctamente.']);
        } catch (PDOException $e) {
            errorJson('Error al actualizar: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) errorJson('Debe indicar el id del servicio.', 422);
        try {
            $stmt = $pdo->prepare('DELETE FROM servicio WHERE id_servicio = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) errorJson('Servicio no encontrado.', 404);
            respuestaJson(['mensaje' => 'Servicio eliminado correctamente.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) errorJson('No se puede eliminar: el servicio tiene gastos asociados.', 409);
            errorJson('Error al eliminar: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
