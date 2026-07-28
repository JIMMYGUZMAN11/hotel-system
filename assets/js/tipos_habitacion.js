const API_TIPOS = 'api/tipos_habitacion.php';
let editandoIdTipo = null;

document.addEventListener('DOMContentLoaded', cargarTipos);
document.getElementById('form-tipo').addEventListener('submit', guardarTipo);
document.getElementById('btn-cancelar').addEventListener('click', cancelarEdicionTipo);

async function cargarTipos() {
    try {
        const tipos = await llamarApi(API_TIPOS);
        const tbody = document.querySelector('#tabla-tipos tbody');
        tbody.innerHTML = '';
        tipos.forEach(t => {
            tbody.innerHTML += `
                <tr>
                    <td>${t.id_tipo}</td>
                    <td>${escaparHtml(t.nombre)}</td>
                    <td>${escaparHtml(t.descripcion)}</td>
                    <td>$${parseFloat(t.precio_noche).toFixed(2)}</td>
                    <td>${t.capacidad}</td>
                    <td class="acciones-tabla">
                        <button class="btn-editar" onclick='editarTipo(${JSON.stringify(t)})'>Editar</button>
                        <button class="btn-peligro" onclick="eliminarTipo(${t.id_tipo})">Eliminar</button>
                    </td>
                </tr>`;
        });
    } catch (err) {
        mostrarMensaje('mensaje-tipos', err.message, 'error');
    }
}

async function guardarTipo(e) {
    e.preventDefault();
    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        descripcion: document.getElementById('descripcion').value.trim(),
        precio_noche: document.getElementById('precio_noche').value,
        capacidad: document.getElementById('capacidad').value,
    };

    try {
        if (editandoIdTipo) {
            datos.id_tipo = editandoIdTipo;
            await llamarApi(API_TIPOS, 'PUT', datos);
            mostrarMensaje('mensaje-tipos', 'Tipo de habitacion actualizado correctamente.', 'exito');
        } else {
            await llamarApi(API_TIPOS, 'POST', datos);
            mostrarMensaje('mensaje-tipos', 'Tipo de habitacion creado correctamente.', 'exito');
        }
        cancelarEdicionTipo();
        cargarTipos();
    } catch (err) {
        mostrarMensaje('mensaje-tipos', err.message, 'error');
    }
}

function editarTipo(t) {
    editandoIdTipo = t.id_tipo;
    document.getElementById('nombre').value = t.nombre;
    document.getElementById('descripcion').value = t.descripcion || '';
    document.getElementById('precio_noche').value = t.precio_noche;
    document.getElementById('capacidad').value = t.capacidad;
    document.getElementById('btn-guardar').textContent = 'Actualizar';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicionTipo() {
    editandoIdTipo = null;
    document.getElementById('form-tipo').reset();
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('btn-cancelar').style.display = 'none';
}

async function eliminarTipo(id) {
    if (!confirm('¿Desea eliminar este tipo de habitacion?')) return;
    try {
        await llamarApi(`${API_TIPOS}?id=${id}`, 'DELETE');
        mostrarMensaje('mensaje-tipos', 'Eliminado correctamente.', 'exito');
        cargarTipos();
    } catch (err) {
        mostrarMensaje('mensaje-tipos', err.message, 'error');
    }
}
