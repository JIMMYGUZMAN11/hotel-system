const API_GASTOS = 'api/gastos.php';
const API_SERVICIOS_G = 'api/servicios.php';

const params = new URLSearchParams(window.location.search);
const idReserva = params.get('reserva');

document.addEventListener('DOMContentLoaded', () => {
    if (!idReserva) {
        mostrarMensaje('mensaje-gastos', 'Debe acceder a esta pagina desde una reserva especifica.', 'error');
        return;
    }
    document.getElementById('id_reserva').value = idReserva;
    document.getElementById('titulo-reserva').textContent = 'Gastos de la reserva #' + idReserva;
    cargarSelectServicios();
    cargarGastos();
});

document.getElementById('form-gasto').addEventListener('submit', guardarGasto);

async function cargarSelectServicios() {
    try {
        const servicios = await llamarApi(API_SERVICIOS_G);
        const select = document.getElementById('id_servicio');
        select.innerHTML = '<option value="">Seleccione...</option>';
        servicios.forEach(s => {
            select.innerHTML += `<option value="${s.id_servicio}">${escaparHtml(s.nombre)} ($${parseFloat(s.precio).toFixed(2)})</option>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-gastos', err.message, 'error');
    }
}

async function cargarGastos() {
    try {
        const gastos = await llamarApi(`${API_GASTOS}?id_reserva=${idReserva}`);
        const tbody = document.querySelector('#tabla-gastos tbody');
        tbody.innerHTML = '';
        let total = 0;
        gastos.forEach(g => {
            total += parseFloat(g.subtotal);
            tbody.innerHTML += `
                <tr>
                    <td>${g.id_gasto}</td>
                    <td>${escaparHtml(g.servicio_nombre)}</td>
                    <td>${g.cantidad}</td>
                    <td>$${parseFloat(g.precio).toFixed(2)}</td>
                    <td>$${parseFloat(g.subtotal).toFixed(2)}</td>
                    <td>${new Date(g.fecha).toLocaleString()}</td>
                    <td class="acciones-tabla">
                        <button class="btn-peligro" onclick="eliminarGasto(${g.id_gasto})">Eliminar</button>
                    </td>
                </tr>`;
        });
        document.getElementById('total-gastos').textContent = '$' + total.toFixed(2);
    } catch (err) {
        mostrarMensaje('mensaje-gastos', err.message, 'error');
    }
}

async function guardarGasto(e) {
    e.preventDefault();
    const datos = {
        id_reserva: idReserva,
        id_servicio: document.getElementById('id_servicio').value,
        cantidad: document.getElementById('cantidad').value,
    };

    try {
        await llamarApi(API_GASTOS, 'POST', datos);
        mostrarMensaje('mensaje-gastos', 'Gasto registrado correctamente.', 'exito');
        document.getElementById('form-gasto').reset();
        document.getElementById('id_reserva').value = idReserva;
        cargarGastos();
    } catch (err) {
        mostrarMensaje('mensaje-gastos', err.message, 'error');
    }
}

async function eliminarGasto(id) {
    if (!confirm('¿Desea eliminar este gasto?')) return;
    try {
        await llamarApi(`${API_GASTOS}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-gastos', 'Eliminado correctamente.', 'exito');
        cargarGastos();
    } catch (err) {
        mostrarMensaje('mensaje-gastos', err.message, 'error');
    }
}
