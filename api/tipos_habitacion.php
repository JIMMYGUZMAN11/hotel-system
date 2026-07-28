<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM tipo_habitacion WHERE id_tipo = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $tipo = $stmt->fetch();
            if (!$tipo) errorJson('Tipo de habitacion no encontrado.', 404);
            respuestaJson($tipo);
        } else {
            $stmt = $pdo->query('SELECT * FROM tipo_habitacion ORDER BY id_tipo');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['nombre', 'precio_noche', 'capacidad']);
        validarNumerico($data['precio_noche'], 'precio_noche');
        validarNumerico($data['capacidad'], 'capacidad');
        if ($data['precio_noche'] <= 0) errorJson('El precio por noche debe ser mayor a 0.', 422);
        if ($data['capacidad'] <= 0) errorJson('La capacidad debe ser mayor a 0.', 422);

        try {
            $stmt = $pdo->prepare('INSERT INTO tipo_habitacion (nombre, descripcion, precio_noche, capacidad)
                                   VALUES (:nombre, :descripcion, :precio, :capacidad)');
            $stmt->execute([
                ':nombre'      => $data['nombre'],
                ':descripcion' => $data['descripcion'] ?? null,
                ':precio'      => $data['precio_noche'],
                ':capacidad'   => $data['capacidad'],
            ]);
            respuestaJson(['mensaje' => 'Tipo de habitacion creado correctamente.', 'id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            errorJson('Error al crear el tipo de habitacion: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $data = leerEntradaJson();
        validarCampos($data, ['id_tipo', 'nombre', 'precio_noche', 'capacidad']);
        validarNumerico($data['precio_noche'], 'precio_noche');
        validarNumerico($data['capacidad'], 'capacidad');

        try {
            $stmt = $pdo->prepare('UPDATE tipo_habitacion SET nombre = :nombre, descripcion = :descripcion,
                                   precio_noche = :precio, capacidad = :capacidad WHERE id_tipo = :id');
            $stmt->execute([
                ':nombre'      => $data['nombre'],
                ':descripcion' => $data['descripcion'] ?? null,
                ':precio'      => $data['precio_noche'],
                ':capacidad'   => $data['capacidad'],
                ':id'          => $data['id_tipo'],
            ]);
            if ($stmt->rowCount() === 0) errorJson('Tipo de habitacion no encontrado o sin cambios.', 404);
            respuestaJson(['mensaje' => 'Tipo de habitacion actualizado correctamente.']);
        } catch (PDOException $e) {
            errorJson('Error al actualizar: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) errorJson('Debe indicar el id del tipo de habitacion.', 422);
        try {
            $stmt = $pdo->prepare('DELETE FROM tipo_habitacion WHERE id_tipo = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) errorJson('Tipo de habitacion no encontrado.', 404);
            respuestaJson(['mensaje' => 'Tipo de habitacion eliminado correctamente.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) errorJson('No se puede eliminar: hay habitaciones asociadas a este tipo.', 409);
            errorJson('Error al eliminar: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
