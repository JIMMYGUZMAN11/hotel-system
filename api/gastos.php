<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT g.*, s.nombre AS servicio_nombre, s.precio
                                   FROM gasto g JOIN servicio s ON g.id_servicio = s.id_servicio
                                   WHERE g.id_gasto = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $g = $stmt->fetch();
            if (!$g) errorJson('Gasto no encontrado.', 404);
            respuestaJson($g);
        } elseif (isset($_GET['id_reserva'])) {
            $stmt = $pdo->prepare('SELECT g.*, s.nombre AS servicio_nombre, s.precio
                                   FROM gasto g JOIN servicio s ON g.id_servicio = s.id_servicio
                                   WHERE g.id_reserva = :idr ORDER BY g.fecha DESC');
            $stmt->execute([':idr' => $_GET['id_reserva']]);
            respuestaJson($stmt->fetchAll());
        } else {
            $stmt = $pdo->query('SELECT g.*, s.nombre AS servicio_nombre, s.precio
                                  FROM gasto g JOIN servicio s ON g.id_servicio = s.id_servicio
                                  ORDER BY g.fecha DESC');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['id_reserva', 'id_servicio', 'cantidad']);
        validarNumerico($data['cantidad'], 'cantidad');
        if ($data['cantidad'] <= 0) errorJson('La cantidad debe ser mayor a 0.', 422);

        $stmt = $pdo->prepare('SELECT precio FROM servicio WHERE id_servicio = :id');
        $stmt->execute([':id' => $data['id_servicio']]);
        $servicio = $stmt->fetch();
        if (!$servicio) errorJson('El servicio indicado no existe.', 404);

        $subtotal = round($servicio['precio'] * $data['cantidad'], 2);

        $pdo->beginTransaction();
        try {
            $ins = $pdo->prepare('INSERT INTO gasto (id_reserva, id_servicio, cantidad, subtotal)
                                   VALUES (:id_reserva, :id_servicio, :cantidad, :subtotal)');
            $ins->execute([
                ':id_reserva'  => $data['id_reserva'],
                ':id_servicio' => $data['id_servicio'],
                ':cantidad'    => $data['cantidad'],
                ':subtotal'    => $subtotal,
            ]);

            // Actualiza el total de la reserva sumando el nuevo gasto
            $upd = $pdo->prepare('UPDATE reserva SET total = total + :subtotal WHERE id_reserva = :id');
            $upd->execute([':subtotal' => $subtotal, ':id' => $data['id_reserva']]);

            $pdo->commit();
            respuestaJson(['mensaje' => 'Gasto registrado correctamente.', 'id' => $pdo->lastInsertId(), 'subtotal' => $subtotal], 201);
        } catch (PDOException $e) {
            $pdo->rollBack();
            errorJson('Error al registrar el gasto: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) errorJson('Debe indicar el id del gasto.', 422);

        $stmt = $pdo->prepare('SELECT id_reserva, subtotal FROM gasto WHERE id_gasto = :id');
        $stmt->execute([':id' => $id]);
        $gasto = $stmt->fetch();
        if (!$gasto) errorJson('Gasto no encontrado.', 404);

        $pdo->beginTransaction();
        try {
            $del = $pdo->prepare('DELETE FROM gasto WHERE id_gasto = :id');
            $del->execute([':id' => $id]);

            $upd = $pdo->prepare('UPDATE reserva SET total = GREATEST(total - :subtotal, 0) WHERE id_reserva = :idr');
            $upd->execute([':subtotal' => $gasto['subtotal'], ':idr' => $gasto['id_reserva']]);

            $pdo->commit();
            respuestaJson(['mensaje' => 'Gasto eliminado correctamente.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            errorJson('Error al eliminar: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
