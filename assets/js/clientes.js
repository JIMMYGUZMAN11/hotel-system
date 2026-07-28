const API_CLIENTES = 'api/clientes.php';
let editandoId = null;

document.addEventListener('DOMContentLoaded', cargarClientes);
document.getElementById('form-cliente').addEventListener('submit', guardarCliente);
document.getElementById('btn-cancelar').addEventListener('click', cancelarEdicion);

async function cargarClientes() {
    try {
        const clientes = await llamarApi(API_CLIENTES);
        const tbody = document.querySelector('#tabla-clientes tbody');
        tbody.innerHTML = '';
        clientes.forEach(c => {
            tbody.innerHTML += `
                <tr>
                    <td>${c.id_cliente}</td>
                    <td>${escaparHtml(c.cedula)}</td>
                    <td>${escaparHtml(c.nombres)} ${escaparHtml(c.apellidos)}</td>
                    <td>${escaparHtml(c.telefono)}</td>
                    <td>${escaparHtml(c.email)}</td>
                    <td class="acciones-tabla">
                        <button class="btn-editar" onclick='editarCliente(${JSON.stringify(c)})'>Editar</button>
                        <button class="btn-peligro" onclick="eliminarCliente(${c.id_cliente})">Eliminar</button>
                    </td>
                </tr>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-clientes', err.message, 'error');
    }
}

async function guardarCliente(e) {
    e.preventDefault();
    const datos = {
        cedula: document.getElementById('cedula').value.trim(),
        nombres: document.getElementById('nombres').value.trim(),
        apellidos: document.getElementById('apellidos').value.trim(),
        telefono: document.getElementById('telefono').value.trim(),
        email: document.getElementById('email').value.trim(),
        direccion: document.getElementById('direccion').value.trim(),
    };

    try {
        if (editandoId) {
            datos.id_cliente = editandoId;
            await llamarApi(API_CLIENTES, 'PUT', datos);
            mostrarMensaje('mensaje-clientes', 'Cliente actualizado correctamente.', 'exito');
        } else {
            await llamarApi(API_CLIENTES, 'POST', datos);
            mostrarMensaje('mensaje-clientes', 'Cliente creado correctamente.', 'exito');
        }
        cancelarEdicion();
        cargarClientes();
    } catch (err) {
        mostrarMensaje('mensaje-clientes', err.message, 'error');
    }
}

function editarCliente(c) {
    editandoId = c.id_cliente;
    document.getElementById('cedula').value = c.cedula;
    document.getElementById('nombres').value = c.nombres;
    document.getElementById('apellidos').value = c.apellidos;
    document.getElementById('telefono').value = c.telefono;
    document.getElementById('email').value = c.email || '';
    document.getElementById('direccion').value = c.direccion || '';
    document.getElementById('btn-guardar').textContent = 'Actualizar';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicion() {
    editandoId = null;
    document.getElementById('form-cliente').reset();
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('btn-cancelar').style.display = 'none';
}

async function eliminarCliente(id) {
    if (!confirm('¿Desea eliminar este cliente?')) return;
    try {
        await llamarApi(`${API_CLIENTES}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-clientes', 'Cliente eliminado correctamente.', 'exito');
        cargarClientes();
    } catch (err) {
        mostrarMensaje('mensaje-clientes', err.message, 'error');
    }
}
