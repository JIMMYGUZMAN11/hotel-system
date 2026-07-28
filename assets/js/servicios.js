const API_SERVICIOS = 'api/servicios.php';
let editandoIdServ = null;

document.addEventListener('DOMContentLoaded', cargarServicios);
document.getElementById('form-servicio').addEventListener('submit', guardarServicio);
document.getElementById('btn-cancelar').addEventListener('click', cancelarEdicionServ);

async function cargarServicios() {
    try {
        const servicios = await llamarApi(API_SERVICIOS);
        const tbody = document.querySelector('#tabla-servicios tbody');
        tbody.innerHTML = '';
        servicios.forEach(s => {
            tbody.innerHTML += `
                <tr>
                    <td>${s.id_servicio}</td>
                    <td>${escaparHtml(s.nombre)}</td>
                    <td>${escaparHtml(s.descripcion)}</td>
                    <td>$${parseFloat(s.precio).toFixed(2)}</td>
                    <td class="acciones-tabla">
                        <button class="btn-editar" onclick='editarServicio(${JSON.stringify(s)})'>Editar</button>
                        <button class="btn-peligro" onclick="eliminarServicio(${s.id_servicio})">Eliminar</button>
                    </td>
                </tr>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-servicios', err.message, 'error');
    }
}

async function guardarServicio(e) {
    e.preventDefault();
    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        descripcion: document.getElementById('descripcion').value.trim(),
        precio: document.getElementById('precio').value,
    };

    try {
        if (editandoIdServ) {
            datos.id_servicio = editandoIdServ;
            await llamarApi(API_SERVICIOS, 'PUT', datos);
            mostrarMensaje('mensaje-servicios', 'Servicio actualizado correctamente.', 'exito');
        } else {
            await llamarApi(API_SERVICIOS, 'POST', datos);
            mostrarMensaje('mensaje-servicios', 'Servicio creado correctamente.', 'exito');
        }
        cancelarEdicionServ();
        cargarServicios();
    } catch (err) {
        mostrarMensaje('mensaje-servicios', err.message, 'error');
    }
}

function editarServicio(s) {
    editandoIdServ = s.id_servicio;
    document.getElementById('nombre').value = s.nombre;
    document.getElementById('descripcion').value = s.descripcion || '';
    document.getElementById('precio').value = s.precio;
    document.getElementById('btn-guardar').textContent = 'Actualizar';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicionServ() {
    editandoIdServ = null;
    document.getElementById('form-servicio').reset();
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('btn-cancelar').style.display = 'none';
}

async function eliminarServicio(id) {
    if (!confirm('¿Desea eliminar este servicio?')) return;
    try {
        await llamarApi(`${API_SERVICIOS}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-servicios', 'Eliminado correctamente.', 'exito');
        cargarServicios();
    } catch (err) {
        mostrarMensaje('mensaje-servicios', err.message, 'error');
    }
}
