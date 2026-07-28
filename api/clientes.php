<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM cliente WHERE id_cliente = :id');
            $stmt->execute([':id' => $_GET['id']]);
            $cliente = $stmt->fetch();
            if (!$cliente) {
                errorJson('Cliente no encontrado.', 404);
            }
            respuestaJson($cliente);
        } else {
            $stmt = $pdo->query('SELECT * FROM cliente ORDER BY id_cliente DESC');
            respuestaJson($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = leerEntradaJson();
        validarCampos($data, ['cedula', 'nombres', 'apellidos', 'telefono']);

        if (!preg_match('/^\d{6,15}$/', $data['cedula'])) {
            errorJson('La cedula debe contener solo numeros (6 a 15 digitos).', 422);
        }
        if (isset($data['email']) && $data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            errorJson('El correo electronico no es valido.', 422);
        }

        try {
            $sql = 'INSERT INTO cliente (cedula, nombres, apellidos, telefono, email, direccion)
                    VALUES (:cedula, :nombres, :apellidos, :telefono, :email, :direccion)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cedula'    => $data['cedula'],
                ':nombres'   => $data['nombres'],
                ':apellidos' => $data['apellidos'],
                ':telefono'  => $data['telefono'],
                ':email'     => $data['email'] ?? null,
                ':direccion' => $data['direccion'] ?? null,
            ]);
            respuestaJson(['mensaje' => 'Cliente creado correctamente.', 'id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                errorJson('Ya existe un cliente registrado con esa cedula.', 409);
            }
            errorJson('Error al crear el cliente: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $data = leerEntradaJson();
        validarCampos($data, ['id_cliente', 'cedula', 'nombres', 'apellidos', 'telefono']);

        if (isset($data['email']) && $data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            errorJson('El correo electronico no es valido.', 422);
        }

        try {
            $sql = 'UPDATE cliente SET cedula = :cedula, nombres = :nombres, apellidos = :apellidos,
                    telefono = :telefono, email = :email, direccion = :direccion
                    WHERE id_cliente = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cedula'    => $data['cedula'],
                ':nombres'   => $data['nombres'],
                ':apellidos' => $data['apellidos'],
                ':telefono'  => $data['telefono'],
                ':email'     => $data['email'] ?? null,
                ':direccion' => $data['direccion'] ?? null,
                ':id'        => $data['id_cliente'],
            ]);
            if ($stmt->rowCount() === 0) {
                errorJson('Cliente no encontrado o sin cambios.', 404);
            }
            respuestaJson(['mensaje' => 'Cliente actualizado correctamente.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                errorJson('Ya existe un cliente registrado con esa cedula.', 409);
            }
            errorJson('Error al actualizar el cliente: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $params);
        $id = $_GET['id'] ?? $params['id'] ?? null;
        if (!$id) {
            errorJson('Debe indicar el id del cliente a eliminar.', 422);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM cliente WHERE id_cliente = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                errorJson('Cliente no encontrado.', 404);
            }
            respuestaJson(['mensaje' => 'Cliente eliminado correctamente.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                errorJson('No se puede eliminar: el cliente tiene reservas asociadas.', 409);
            }
            errorJson('Error al eliminar el cliente: ' . $e->getMessage(), 500);
        }
        break;

    default:
        errorJson('Metodo no permitido.', 405);
}
