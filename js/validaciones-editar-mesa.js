document.addEventListener("DOMContentLoaded", function () {
    // Referencias a elementos del formulario
    const nombreSala = document.getElementById('nombre_sala');
    const numeroSillas = document.getElementById('numero_sillas');
    const imagenSala = document.getElementById('imagen_sala');
    const formulario = document.getElementById('editForm');

    // Referencias a los spans de error
    const nombreSalaError = document.getElementById('nombreSalaError');
    const numeroSillasError = document.getElementById('numeroSillasError');
    const imagenSalaError = document.getElementById('imagenSalaError');

    // Funciones de validación
    function nombreCorrecto() {
        if (nombreSala.value.trim() === "") {
            nombreSalaError.textContent = "El nombre de la sala no puede estar vacío.";
            nombreSala.style.borderColor = "red";
            return false;
        } else if (nombreSala.value.trim().length < 3) {
            nombreSalaError.textContent = "El nombre de la sala debe tener al menos 3 caracteres.";
            nombreSala.style.borderColor = "red";
            return false;
        } else {
            nombreSalaError.textContent = "";
            nombreSala.style.borderColor = "";
            return true;
        }
    }

    function numeroSillasCorrecto() {
        const numSillas = parseInt(numeroSillas.value);
        if (isNaN(numSillas) || numSillas <= 0 || numSillas > 18) {
            numeroSillasError.textContent = "El número de sillas debe ser entre 1 y 18.";
            numeroSillas.style.borderColor = "red";
            return false;
        } else {
            numeroSillasError.textContent = "";
            numeroSillas.style.borderColor = "";
            return true;
        }
    }

    function imagenCorrecta() {
        if (imagenSala.value !== "" && !/\.(jpg|jpeg|png|gif)$/i.test(imagenSala.value)) {
            imagenSalaError.textContent = "Solo se permiten imágenes en formatos JPG, JPEG, PNG o GIF.";
            imagenSala.style.borderColor = "red";
            return false;
        } else {
            imagenSalaError.textContent = "";
            imagenSala.style.borderColor = "";
            return true;
        }
    }

    // Validación en el envío del formulario
    formulario.onsubmit = function (event) {
        let formIsValid = true;

        // Verificar cada campo usando sus funciones respectivas
        if (!nombreCorrecto()) formIsValid = false;
        if (!numeroSillasCorrecto()) formIsValid = false;
        if (!imagenCorrecta()) formIsValid = false;

        // Si algún campo no es válido, prevenir el envío
        if (!formIsValid) {
            event.preventDefault();
        }
    };
});
