const API_HABITACIONES = 'api/habitaciones.php';
const API_TIPOS_H = 'api/tipos_habitacion.php';
let editandoIdHab = null;

document.addEventListener('DOMContentLoaded', () => {
    cargarTiposSelect();
    cargarHabitaciones();
});
document.getElementById('form-habitacion').addEventListener('submit', guardarHabitacion);
document.getElementById('btn-cancelar').addEventListener('click', cancelarEdicionHab);

async function cargarTiposSelect() {
    try {
        const tipos = await llamarApi(API_TIPOS_H);
        const select = document.getElementById('id_tipo');
        select.innerHTML = '<option value="">Seleccione...</option>';
        tipos.forEach(t => {
            select.innerHTML += `<option value="${t.id_tipo}">${escaparHtml(t.nombre)} ($${parseFloat(t.precio_noche).toFixed(2)}/noche)</option>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-habitaciones', err.message, 'error');
    }
}

async function cargarHabitaciones() {
    try {
        const habitaciones = await llamarApi(API_HABITACIONES);
        const tbody = document.querySelector('#tabla-habitaciones tbody');
        tbody.innerHTML = '';
        habitaciones.forEach(h => {
            const claseBadge = 'badge-' + h.estado.toLowerCase();
            tbody.innerHTML += `
                <tr>
                    <td>${h.id_habitacion}</td>
                    <td>${escaparHtml(h.numero)}</td>
                    <td>${h.piso}</td>
                    <td>${escaparHtml(h.tipo_nombre)}</td>
                    <td><span class="badge ${claseBadge}">${h.estado}</span></td>
                    <td class="acciones-tabla">
                        <button class="btn-editar" onclick='editarHabitacion(${JSON.stringify(h)})'>Editar</button>
                        <button class="btn-peligro" onclick="eliminarHabitacion(${h.id_habitacion})">Eliminar</button>
                    </td>
                </tr>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-habitaciones', err.message, 'error');
    }
}

async function guardarHabitacion(e) {
    e.preventDefault();
    const datos = {
        numero: document.getElementById('numero').value.trim(),
        piso: document.getElementById('piso').value,
        id_tipo: document.getElementById('id_tipo').value,
        estado: document.getElementById('estado').value,
    };

    try {
        if (editandoIdHab) {
            datos.id_habitacion = editandoIdHab;
            await llamarApi(API_HABITACIONES, 'PUT', datos);
            mostrarMensaje('mensaje-habitaciones', 'Habitacion actualizada correctamente.', 'exito');
        } else {
            await llamarApi(API_HABITACIONES, 'POST', datos);
            mostrarMensaje('mensaje-habitaciones', 'Habitacion creada correctamente.', 'exito');
        }
        cancelarEdicionHab();
        cargarHabitaciones();
    } catch (err) {
        mostrarMensaje('mensaje-habitaciones', err.message, 'error');
    }
}

function editarHabitacion(h) {
    editandoIdHab = h.id_habitacion;
    document.getElementById('numero').value = h.numero;
    document.getElementById('piso').value = h.piso;
    document.getElementById('id_tipo').value = h.id_tipo;
    document.getElementById('estado').value = h.estado;
    document.getElementById('btn-guardar').textContent = 'Actualizar';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicionHab() {
    editandoIdHab = null;
    document.getElementById('form-habitacion').reset();
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('btn-cancelar').style.display = 'none';
}

async function eliminarHabitacion(id) {
    if (!confirm('¿Desea eliminar esta habitacion?')) return;
    try {
        await llamarApi(`${API_HABITACIONES}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-habitaciones', 'Eliminada correctamente.', 'exito');
        cargarHabitaciones();
    } catch (err) {
        mostrarMensaje('mensaje-habitaciones', err.message, 'error');
    }
}
