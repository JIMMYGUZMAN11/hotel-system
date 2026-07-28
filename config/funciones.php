<?php
/**
 * Funciones auxiliares compartidas por todos los endpoints de la API
 */

function respuestaJson($data, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function errorJson($mensaje, $codigo = 400) {
    respuestaJson(['error' => true, 'mensaje' => $mensaje], $codigo);
}

function leerEntradaJson() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        errorJson('JSON invalido enviado en la peticion.', 400);
    }
    return $data ?? [];
}

/**
 * Valida que los campos requeridos existan y no esten vacios
 * @param array $data
 * @param array $campos
 */
function validarCampos($data, $campos) {
    $faltantes = [];
    foreach ($campos as $campo) {
        if (!isset($data[$campo]) || trim((string)$data[$campo]) === '') {
            $faltantes[] = $campo;
        }
    }
    if (!empty($faltantes)) {
        errorJson('Faltan campos obligatorios: ' . implode(', ', $faltantes), 422);
    }
}

function validarNumerico($valor, $nombreCampo) {
    if (!is_numeric($valor)) {
        errorJson("El campo '{$nombreCampo}' debe ser numerico.", 422);
    }
}

function validarFecha($valor, $nombreCampo) {
    $d = DateTime::createFromFormat('Y-m-d', $valor);
    if (!$d || $d->format('Y-m-d') !== $valor) {
        errorJson("El campo '{$nombreCampo}' debe tener formato de fecha valido (YYYY-MM-DD).", 422);
    }
}
