<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$estadosValidos = ['Disponible', 'Ocupada', 'Mantenimiento'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT h.*, t.nombre AS tipo_nombre, t.precio_noche
                                   FROM habitacion h JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo
                                   WHERE h.id_habitacion = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $hab = $stmt->fetch();
            if (!$hab) errorJson('Habitacion no encontrada.', 404);
            respuestaJson($hab);
        } else {
            $stmt = $pdo->query('SELECT h.*, t.nombre AS tipo_nombre, t.precio_noche
                                  FROM habitacion h JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo
                                  ORDER BY h.numero');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['numero', 'piso', 'id_tipo']);
        validarNumerico($data['piso'], 'piso');
        validarNumerico($data['id_tipo'], 'id_tipo');
        $estado = $data['estado'] ?? 'Disponible';
        if (!in_array($estado, $estadosValidos)) errorJson('Estado invalido.', 422);

        try {
            $stmt = $pdo->prepare('INSERT INTO habitacion (numero, piso, id_tipo, estado)
                                   VALUES (:numero, :piso, :id_tipo, :estado)');
            $stmt->execute([
                ':numero' => $data['numero'],
                ':piso'   => $data['piso'],
                ':id_tipo'=> $data['id_tipo'],
                ':estado' => $estado,
            ]);
            respuestaJson(['mensaje' => 'Habitacion creada correctamente.', 'id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) errorJson('Ya existe una habitacion con ese numero.', 409);
            errorJson('Error al crear la habitacion: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $data = leerEntradaJson();
        validarCampos($data, ['id_habitacion', 'numero', 'piso', 'id_tipo', 'estado']);
        if (!in_array($data['estado'], $estadosValidos)) errorJson('Estado invalido.', 422);

        try {
            $stmt = $pdo->prepare('UPDATE habitacion SET numero = :numero, piso = :piso, id_tipo = :id_tipo,
                                   estado = :estado WHERE id_habitacion = :id');
            $stmt->execute([
                ':numero'  => $data['numero'],
                ':piso'    => $data['piso'],
                ':id_tipo' => $data['id_tipo'],
                ':estado'  => $data['estado'],
                ':id'      => $data['id_habitacion'],
            ]);
            if ($stmt->rowCount() === 0) errorJson('Habitacion no encontrada o sin cambios.', 404);
            respuestaJson(['mensaje' => 'Habitacion actualizada correctamente.']);
        } catch (PDOException $e) {
            errorJson('Error al actualizar: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) errorJson('Debe indicar el id de la habitacion.', 422);
        try {
            $stmt = $pdo->prepare('DELETE FROM habitacion WHERE id_habitacion = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) errorJson('Habitacion no encontrada.', 404);
            respuestaJson(['mensaje' => 'Habitacion eliminada correctamente.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) errorJson('No se puede eliminar: la habitacion tiene reservas asociadas.', 409);
            errorJson('Error al eliminar: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
