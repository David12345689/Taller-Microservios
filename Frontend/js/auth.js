document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMsg = document.getElementById('errorMsg');

    try {
        const res = await fetch('http://localhost:8001/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (!res.ok) {
            errorMsg.textContent = data.error || 'Credenciales incorrectas';
            return;
        }

        // Guardar token y rol
        localStorage.setItem('token', data.token);
        localStorage.setItem('rol', data.usuario.rol);

        // Redirigir según el rol
        if (data.usuario.rol === 'admin') {
            window.location.href = 'admin.html';
        } else if (data.usuario.rol === 'gestor') {
            window.location.href = 'gestor.html';
        } else {
            errorMsg.textContent = 'Rol no reconocido';
        }

    } catch (err) {
        errorMsg.textContent = 'Error de conexión con el servidor';
    }
});