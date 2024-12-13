document.addEventListener("DOMContentLoaded", function () {
    const nombreUser = document.getElementById('nombre_user');
    const nombreReal = document.getElementById('nombre_real');
    const apeUsuario = document.getElementById('ape_usuario');
    const contrasena = document.getElementById('contrasena');
    const rolUser = document.getElementById('rol_user');
    const nombreUserError = document.getElementById('nombreUserError');
    const nombreRealError = document.getElementById('nombreRealError');
    const apeUsuarioError = document.getElementById('apeUsuarioError');
    const contrasenaError = document.getElementById('contrasenaError');
    const rolUserError = document.getElementById('rolUserError');
    const formulario = document.getElementById('registrationForm'); 

    // Limpiar mensajes y estilos al cargar la página
    nombreUserError.textContent = "";
    nombreRealError.textContent = "";
    apeUsuarioError.textContent = "";
    contrasenaError.textContent = "";
    rolUserError.textContent = "";

    nombreUser.style.borderColor = "";
    nombreReal.style.borderColor = "";
    apeUsuario.style.borderColor = "";
    contrasena.style.borderColor = "";
    rolUser.style.borderColor = "";

    // Asignar eventos
    nombreUser.onblur = nombreCorrecto;
    nombreReal.onblur = nombreRealCorrecto;
    apeUsuario.onblur = apeUsuarioCorrecto;
    contrasena.onblur = contrasenaCorrecto;
    rolUser.onclick = rolUserCorrecto;
    // Función para verificar el nombre de usuario
    function nombreCorrecto() {
        const value = nombreUser.value.trim();
        const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/; // Expresión regular que permite solo letras y espacios

        if (value === "") {
            nombreUserError.textContent = "El nombre de usuario está vacío";
            nombreUser.style.borderColor = "red";
            return false;
        } else if (value.length < 3) {
            nombreUserError.textContent = "El nombre de usuario debe tener al menos 3 caracteres";
            nombreUser.style.borderColor = "red";
            return false;
        } else if (!regex.test(value)) {
            nombreUserError.textContent = "El nombre de usuario no puede contener números";
            nombreUser.style.borderColor = "red";
            return false;   
        } else {
            nombreUserError.textContent = "";
            nombreUser.style.borderColor = "";
            return true;
        }
    }

        // Función para verificar el nombre real
        function nombreRealCorrecto() {
            const value = nombreReal.value.trim();
            const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/; // Expresión regular que permite solo letras y espacios
    
            if (value === "") {
                nombreRealError.textContent = "El nombre real está vacío";
                nombreReal.style.borderColor = "red";
                return false;
            } else if (value.length < 3) {
                nombreRealError.textContent = "El nombre real debe tener al menos 3 caracteres";
                nombreReal.style.borderColor = "red";
                return false;
            } else if (!regex.test(value)) {
                nombreRealError.textContent = "El nombre real no puede contener números";
                nombreReal.style.borderColor = "red";
                return false;
            } else {
                nombreRealError.textContent = "";
                nombreReal.style.borderColor = "";
                return true;
            }
        }
    
        // Función para verificar el apellido
        function apeUsuarioCorrecto() {
            const value = apeUsuario.value.trim();
            const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/; // Expresión regular que permite solo letras y espacios
    
            if (value === "") {
                apeUsuarioError.textContent = "El apellido está vacío";
                apeUsuario.style.borderColor = "red";
                return false;
            } else if (value.length < 3) {
                apeUsuarioError.textContent = "El apellido debe tener al menos 3 caracteres";
                apeUsuario.style.borderColor = "red";
                return false;
            } else if (!regex.test(value)) {
                apeUsuarioError.textContent = "El apellido no puede contener números";
                apeUsuario.style.borderColor = "red";
                return false;
            } else {
                apeUsuarioError.textContent = "";
                apeUsuario.style.borderColor = "";
                return true;
            }
        }
    
        // Función para verificar la contraseña
        function contrasenaCorrecto() {
            const value = contrasena.value.trim();
            if (value === "") {
                contrasenaError.textContent = "La contraseña está vacía";
                contrasena.style.borderColor = "red";
                return false;
            } else if (value.length < 6) {
                contrasenaError.textContent = "La contraseña debe tener al menos 6 caracteres";
                contrasena.style.borderColor = "red";
                return false;
            } else {
                contrasenaError.textContent = "";
                contrasena.style.borderColor = "";
                return true;
            }
        }
    
        // Función para verificar el rol de usuario
        function rolUserCorrecto() {
            if (rolUser.value === "") {
                rolUserError.textContent = "Selecciona un rol válido";
                rolUser.style.borderColor = "red";
                return false;
            } else {
                rolUserError.textContent = "";
                rolUser.style.borderColor = "";
                return true;
            }
        }
    
        // Evento onsubmit para validar los campos antes de enviar el formulario
        formulario.onsubmit = function (event) {
            let formIsValid = true;
    
            // Verificar todos los campos
            if (!nombreCorrecto()) formIsValid = false;
            if (!nombreRealCorrecto()) formIsValid = false;
            if (!apeUsuarioCorrecto()) formIsValid = false;
            if (!contrasenaCorrecto()) formIsValid = false;
            if (!rolUserCorrecto()) formIsValid = false;
    
            // Si algún campo es inválido, prevenir el envío
            if (!formIsValid) {
                event.preventDefault();
            }
        };
    });
    