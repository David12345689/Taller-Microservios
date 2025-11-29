import { ApiService } from './apiService.js';
import { verificarAcceso, cerrarSesion } from './utils.js';

verificarAcceso('admin');
const api = new ApiService('http://localhost:8002');

window.onload = cargarTickets;

async function cargarTickets() {
    const data = await api.get('/tickets');
    const lista = document.getElementById('listaTickets');
    lista.innerHTML = '';

    if (data.tickets?.length) {
        data.tickets.forEach(ticket => {
            lista.innerHTML += renderTicket(ticket);
        });
    } else {
        lista.innerHTML = '<p>No hay tickets registrados.</p>';
    }
}

function renderTicket(ticket) {
    return `
    <div class="ticket">
        <h3>${ticket.titulo}</h3>
        <p>${ticket.descripcion}</p>
        <p><strong>Estado:</strong> ${ticket.estado}</p>
        <button onclick="verDetalle(${ticket.id})">Ver Detalles</button>
    </div>`;
}

window.verDetalle = async function (id) {
    const data = await api.get(`/tickets/${id}`);
    const ticket = data.ticket;
    if (!ticket) {
        alert('Error al obtener detalle');
        return;
    }

    const detalles = `
    <div class="detalle-ticket">
        <h3>Ticket #${ticket.id}</h3>
        <p><strong>Título:</strong> ${ticket.titulo}</p>
        <p><strong>Estado:</strong> ${ticket.estado}</p>

        <form onsubmit="return cambiarEstado(${ticket.id})">
            <select id="estado_${ticket.id}">
                <option value="abierto">Abierto</option>
                <option value="en_progreso">En progreso</option>
                <option value="resuelto">Resuelto</option>
                <option value="cerrado">Cerrado</option>
            </select>
            <button type="submit">Cambiar Estado</button>
        </form>

        <form onsubmit="return asignar(${ticket.id})">
            <input type="number" id="adminId_${ticket.id}" placeholder="ID del admin">
            <button type="submit">Asignar</button>
        </form>

        <form onsubmit="return comentar(${ticket.id})">
            <textarea id="comentario_${ticket.id}" required placeholder="Comentario..."></textarea>
            <button type="submit">Comentar</button>
        </form>

        <h4>Comentarios:</h4>
        <ul>
            ${ticket.actividad.map(a => `<li>${a.mensaje} (Usuario ${a.user_id})</li>`).join('')}
        </ul>
    </div>`;

    document.getElementById('listaTickets').innerHTML = detalles;
};

window.cambiarEstado = async function (id) {
    const estado = document.getElementById(`estado_${id}`).value;
    const res = await api.put(`/tickets/${id}/estado`, { estado });

    if (res.mensaje) {
        alert('✅ Estado actualizado');
        cargarTickets();
    } else {
        alert('❌ Error al actualizar estado');
    }
    return false;
};

window.asignar = async function (id) {
    const admin_id = document.getElementById(`adminId_${id}`).value;
    const res = await api.put(`/tickets/${id}/asignar`, { admin_id });

    if (res.mensaje) {
        alert('✅ Ticket asignado');
        cargarTickets();
    } else {
        alert('❌ Error al asignar');
    }
    return false;
};

window.comentar = async function (id) {
    const mensaje = document.getElementById(`comentario_${id}`).value;
    const res = await api.post(`/tickets/${id}/comentario`, { mensaje });

    if (res.mensaje) {
        alert('✅ Comentario agregado');
        verDetalle(id);
    } else {
        alert('❌ Error al comentar');
    }
    return false;
};

window.cerrarSesion = cerrarSesion;