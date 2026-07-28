const API_RESERVAS = 'api/reservas.php';
const API_CLIENTES_R = 'api/clientes.php';
const API_HABITACIONES_R = 'api/habitaciones.php';
let editandoIdReserva = null;

document.addEventListener('DOMContentLoaded', () => {
    cargarSelectClientes();
    cargarSelectHabitaciones();
    cargarReservas();
});
document.getElementById('form-reserva').addEventListener('submit', guardarReserva);
document.getElementById('btn-cancelar').addEventListener('click', cancelarEdicionReserva);

async function cargarSelectClientes() {
    try {
        const clientes = await llamarApi(API_CLIENTES_R);
        const select = document.getElementById('id_cliente');
        select.innerHTML = '<option value="">Seleccione...</option>';
        clientes.forEach(c => {
            select.innerHTML += `<option value="${c.id_cliente}">${escaparHtml(c.nombres)} ${escaparHtml(c.apellidos)} (${escaparHtml(c.cedula)})</option>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-reservas', err.message, 'error');
    }
}

async function cargarSelectHabitaciones() {
    try {
        const habitaciones = await llamarApi(API_HABITACIONES_R);
        const select = document.getElementById('id_habitacion');
        select.innerHTML = '<option value="">Seleccione...</option>';
        habitaciones.forEach(h => {
            select.innerHTML += `<option value="${h.id_habitacion}">Hab. ${escaparHtml(h.numero)} - ${escaparHtml(h.tipo_nombre)} ($${parseFloat(h.precio_noche).toFixed(2)}/noche)</option>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-reservas', err.message, 'error');
    }
}

async function cargarReservas() {
    try {
        const reservas = await llamarApi(API_RESERVAS);
        const tbody = document.querySelector('#tabla-reservas tbody');
        tbody.innerHTML = '';
        reservas.forEach(r => {
            const claseBadge = 'badge-' + r.estado.toLowerCase();
            tbody.innerHTML += `
                <tr>
                    <td>${r.id_reserva}</td>
                    <td>${escaparHtml(r.nombres)} ${escaparHtml(r.apellidos)}</td>
                    <td>${escaparHtml(r.habitacion_numero)}</td>
                    <td>${r.fecha_entrada}</td>
                    <td>${r.fecha_salida}</td>
                    <td><span class="badge ${claseBadge}">${r.estado}</span></td>
                    <td>$${parseFloat(r.total).toFixed(2)}</td>
                    <td class="acciones-tabla">
                        <button class="btn-editar" onclick='editarReserva(${JSON.stringify(r)})'>Editar</button>
                        <button class="btn-peligro" onclick="eliminarReserva(${r.id_reserva})">Eliminar</button>
                        <a href="gastos.php?reserva=${r.id_reserva}"><button type="button" class="btn-secundario">Gastos</button></a>
                    </td>
                </tr>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-reservas', err.message, 'error');
    }
}

async function guardarReserva(e) {
    e.preventDefault();
    const datos = {
        id_cliente: document.getElementById('id_cliente').value,
        id_habitacion: document.getElementById('id_habitacion').value,
        fecha_entrada: document.getElementById('fecha_entrada').value,
        fecha_salida: document.getElementById('fecha_salida').value,
        estado: document.getElementById('estado').value,
    };

    try {
        if (editandoIdReserva) {
            datos.id_reserva = editandoIdReserva;
            const r = await llamarApi(API_RESERVAS, 'PUT', datos);
            mostrarMensaje('mensaje-reservas', `Reserva actualizada. Total: $${r.total.toFixed(2)}`, 'exito');
        } else {
            const r = await llamarApi(API_RESERVAS, 'POST', datos);
            mostrarMensaje('mensaje-reservas', `Reserva creada. Total: $${r.total.toFixed(2)}`, 'exito');
        }
        cancelarEdicionReserva();
        cargarReservas();
        cargarSelectHabitaciones();
    } catch (err) {
        mostrarMensaje('mensaje-reservas', err.message, 'error');
    }
}

function editarReserva(r) {
    editandoIdReserva = r.id_reserva;
    document.getElementById('id_cliente').value = r.id_cliente;
    document.getElementById('id_habitacion').value = r.id_habitacion;
    document.getElementById('fecha_entrada').value = r.fecha_entrada;
    document.getElementById('fecha_salida').value = r.fecha_salida;
    document.getElementById('estado').value = r.estado;
    document.getElementById('btn-guardar').textContent = 'Actualizar';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicionReserva() {
    editandoIdReserva = null;
    document.getElementById('form-reserva').reset();
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('btn-cancelar').style.display = 'none';
}

async function eliminarReserva(id) {
    if (!confirm('¿Desea eliminar esta reserva?')) return;
    try {
        await llamarApi(`${API_RESERVAS}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-reservas', 'Eliminada correctamente.', 'exito');
        cargarReservas();
    } catch (err) {
        mostrarMensaje('mensaje-reservas', err.message, 'error');
    }
}
