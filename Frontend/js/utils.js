export function getToken() {
    return localStorage.getItem('token');
}

export function getRol() {
    return localStorage.getItem('rol');
}

export function verificarAcceso(rolEsperado) {
    const token = getToken();
    const rol = getRol();

    if (!token || rol !== rolEsperado) {
        window.location.href = 'login.html';
    }
}

export function cerrarSesion() {
    localStorage.clear();
    window.location.href = 'login.html';
}