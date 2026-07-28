<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$estadosValidos = ['Pendiente', 'Confirmada', 'Cancelada', 'Finalizada'];

function calcularTotal($pdo, $id_habitacion, $fecha_entrada, $fecha_salida) {
    $stmt = $pdo->prepare('SELECT t.precio_noche FROM habitacion h
                            JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo
                            WHERE h.id_habitacion = :id');
    $stmt->execute([':id' => $id_habitacion]);
    $hab = $stmt->fetch();
    if (!$hab) return null;

    $d1 = new DateTime($fecha_entrada);
    $d2 = new DateTime($fecha_salida);
    $noches = $d2->diff($d1)->days;
    if ($noches <= 0) return null;

    return round($noches * $hab['precio_noche'], 2);
}

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT r.*, c.nombres, c.apellidos, h.numero AS habitacion_numero
                                   FROM reserva r
                                   JOIN cliente c ON r.id_cliente = c.id_cliente
                                   JOIN habitacion h ON r.id_habitacion = h.id_habitacion
                                   WHERE r.id_reserva = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $r = $stmt->fetch();
            if (!$r) errorJson('Reserva no encontrada.', 404);
            respuestaJson($r);
        } else {
            $stmt = $pdo->query('SELECT r.*, c.nombres, c.apellidos, h.numero AS habitacion_numero
                                  FROM reserva r
                                  JOIN cliente c ON r.id_cliente = c.id_cliente
                                  JOIN habitacion h ON r.id_habitacion = h.id_habitacion
                                  ORDER BY r.id_reserva DESC');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['id_cliente', 'id_habitacion', 'fecha_entrada', 'fecha_salida']);
        validarFecha($data['fecha_entrada'], 'fecha_entrada');
        validarFecha($data['fecha_salida'], 'fecha_salida');

        if ($data['fecha_salida'] <= $data['fecha_entrada']) {
            errorJson('La fecha de salida debe ser posterior a la fecha de entrada.', 422);
        }

        $total = calcularTotal($pdo, $data['id_habitacion'], $data['fecha_entrada'], $data['fecha_salida']);
        if ($total === null) errorJson('No se pudo calcular el total: verifique la habitacion y las fechas.', 422);

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO reserva (id_cliente, id_habitacion, fecha_entrada, fecha_salida, estado, total)
                                   VALUES (:id_cliente, :id_habitacion, :entrada, :salida, :estado, :total)');
            $stmt->execute([
                ':id_cliente'   => $data['id_cliente'],
                ':id_habitacion'=> $data['id_habitacion'],
                ':entrada'      => $data['fecha_entrada'],
                ':salida'       => $data['fecha_salida'],
                ':estado'       => $data['estado'] ?? 'Pendiente',
                ':total'        => $total,
            ]);
            $idReserva = $pdo->lastInsertId();

            // Marca la habitacion como Ocupada si la reserva se crea confirmada
            if (($data['estado'] ?? 'Pendiente') === 'Confirmada') {
                $upd = $pdo->prepare('UPDATE habitacion SET estado = "Ocupada" WHERE id_habitacion = :id');
                $upd->execute([':id' => $data['id_habitacion']]);
            }

            $pdo->commit();
            respuestaJson(['mensaje' => 'Reserva creada correctamente.', 'id' => $idReserva, 'total' => $total], 201);
        } catch (PDOException $e) {
            $pdo->rollBack();
            errorJson('Error al crear la reserva: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $data = leerEntradaJson();
        validarCampos($data, ['id_reserva', 'id_cliente', 'id_habitacion', 'fecha_entrada', 'fecha_salida', 'estado']);
        validarFecha($data['fecha_entrada'], 'fecha_entrada');
        validarFecha($data['fecha_salida'], 'fecha_salida');
        if (!in_array($data['estado'], $estadosValidos)) errorJson('Estado invalido.', 422);
        if ($data['fecha_salida'] <= $data['fecha_entrada']) {
            errorJson('La fecha de salida debe ser posterior a la fecha de entrada.', 422);
        }

        $total = calcularTotal($pdo, $data['id_habitacion'], $data['fecha_entrada'], $data['fecha_salida']);
        if ($total === null) errorJson('No se pudo calcular el total: verifique la habitacion y las fechas.', 422);

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE reserva SET id_cliente = :id_cliente, id_habitacion = :id_habitacion,
                                   fecha_entrada = :entrada, fecha_salida = :salida, estado = :estado, total = :total
                                   WHERE id_reserva = :id');
            $stmt->execute([
                ':id_cliente'    => $data['id_cliente'],
                ':id_habitacion' => $data['id_habitacion'],
                ':entrada'       => $data['fecha_entrada'],
                ':salida'        => $data['fecha_salida'],
                ':estado'        => $data['estado'],
                ':total'         => $total,
                ':id'            => $data['id_reserva'],
            ]);
            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                errorJson('Reserva no encontrada o sin cambios.', 404);
            }

            // Sincroniza el estado de la habitacion segun el estado de la reserva
            if ($data['estado'] === 'Confirmada') {
                $upd = $pdo->prepare('UPDATE habitacion SET estado = "Ocupada" WHERE id_habitacion = :id');
                $upd->execute([':id' => $data['id_habitacion']]);
            } elseif (in_array($data['estado'], ['Cancelada', 'Finalizada'])) {
                $upd = $pdo->prepare('UPDATE habitacion SET estado = "Disponible" WHERE id_habitacion = :id');
                $upd->execute([':id' => $data['id_habitacion']]);
            }

            $pdo->commit();
            respuestaJson(['mensaje' => 'Reserva actualizada correctamente.', 'total' => $total]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            errorJson('Error al actualizar la reserva: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) errorJson('Debe indicar el id de la reserva.', 422);
        try {
            $stmt = $pdo->prepare('DELETE FROM reserva WHERE id_reserva = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) errorJson('Reserva no encontrada.', 404);
            respuestaJson(['mensaje' => 'Reserva eliminada correctamente.']);
        } catch (PDOException $e) {
            errorJson('Error al eliminar: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
