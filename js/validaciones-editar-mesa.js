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
    function validateNombreSala() {
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

    function validateNumeroSillas() {
        if (numeroSillas.value === "" || parseInt(numeroSillas.value) <= 0 || parseInt(numeroSillas.value) > 18) {
            numeroSillasError.textContent = "El número de sillas debe ser entre 1 y 18.";
            numeroSillas.style.borderColor = "red";
            return false;
        } else {
            numeroSillasError.textContent = "";
            numeroSillas.style.borderColor = "";
            return true;
        }
    }

    function validateImagenSala() {
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

    // Asignación de eventos
    nombreSala.onblur = validateNombreSala;
    numeroSillas.onblur = validateNumeroSillas;
    imagenSala.onchange = validateImagenSala;

    // Validación en el envío del formulario
    formulario.onsubmit = function (event) {
        let isValid = true;

        if (!validateNombreSala()) isValid = false;
        if (!validateNumeroMesa()) isValid = false;
        if (!validateNumeroSillas()) isValid = false;
        if (!validateImagenSala()) isValid = false;

        if (!isValid) {
            event.preventDefault();
        }
    };
});
                                                                                                            