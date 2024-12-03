
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error) {
        if (error === 'contrasena_incorrecta' || error === 'usuario_no_encontrado') {
            document.getElementById('usuarioError').textContent = 'Usuario o contraseña incorrecto';
            document.getElementById('contraError').textContent = 'Usuario o contraseña incorrecto';
        } else if (error === 'campos_vacios') {
            document.getElementById('usuarioError').textContent = 'Por favor, complete todos los campos';
            document.getElementById('apellidoError').textContent = 'Por favor, complete todos los campos';
            document.getElementById('contraError').textContent = 'Por favor, complete todos los campos';
        } else if (error === 'usuario_invalido') {
            document.getElementById('usuarioError').textContent = 'El usuario debe ser valido';
        } else if (error === 'apellido_invalido') {
            document.getElementById('apellidoError').textContent = 'El apellido debe contener solo letras';
        } else if (error === 'contrasena_invalida') {
            document.getElementById('contraError').textContent = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial';
        }
    }
});
