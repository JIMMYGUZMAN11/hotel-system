/**
 * Funciones comunes reutilizadas por todos los modulos del frontend
 */

async function llamarApi(url, metodo = 'GET', body = null) {
    const opciones = { method: metodo, headers: { 'Content-Type': 'application/json' } };
    if (body !== null) opciones.body = JSON.stringify(body);

    try {
        const respuesta = await fetch(url, opciones);
        const datos = await respuesta.json();
        if (!respuesta.ok) {
            throw new Error(datos.mensaje || 'Ocurrio un error inesperado.');
        }
        return datos;
    } catch (err) {
        throw new Error(err.message || 'Error de conexion con el servidor.');
    }
}

function mostrarMensaje(idContenedor, texto, tipo = 'exito') {
    const el = document.getElementById(idContenedor);
    if (!el) return;
    el.textContent = texto;
    el.className = 'mensaje ' + tipo;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function escaparHtml(texto) {
    if (texto === null || texto === undefined) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
