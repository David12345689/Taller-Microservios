import { ApiService } from './apiService.js';
import { verificarAcceso, cerrarSesion } from './utils.js';

verificarAcceso('gestor');

const api = new ApiService('http://localhost:8002'); 


window.onload = cargarTickets;


document.getElementById('formCrearTicket').addEventListener('submit', async function (e) {
    e.preventDefault();

    const titulo = document.getElementById('titulo').value;
    const descripcion = document.getElementById('descripcion').value;

    const data = await api.post('/tickets', { titulo, descripcion });
    const msg = document.getElementById('crearMsg');

    if (data.ticket) {
        msg.textContent = 'Ticket creado correctamente';
        document.getElementById('formCrearTicket').reset();
        cargarTickets();
    } else {
        msg.textContent = data.error || 'Error al crear el ticket';
    }
});

async function cargarTickets() {
    const data = await api.get('/tickets');
    const lista = document.getElementById('listaTickets');
    lista.innerHTML = '';

    if (data.tickets?.length > 0) {
        data.tickets.forEach(ticket => {
            lista.innerHTML += `
                <div class="ticket">
                    <h4>${ticket.titulo}</h4>
                    <p>${ticket.descripcion}</p>
                    <p><strong>Estado:</strong> ${ticket.estado}</p>
                </div>`;
        });
    } else {
        lista.innerHTML = '<p>No tienes tickets creados.</p>';
    }
}

window.cerrarSesion = cerrarSesion;