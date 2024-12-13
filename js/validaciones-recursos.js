document.addEventListener("DOMContentLoaded", function () {
    // Referencias a elementos
    const accion = document.getElementById('accion');
    const formMesa = document.getElementById('form_mesa');
    const formSala = document.getElementById('form_sala');
    const salaMesa = document.getElementById('sala_mesa');
    const numeroMesa = document.getElementById('numero_mesa');
    const numeroSillas = document.getElementById('numero_sillas');
    const nombreSala = document.getElementById('nombre_sala');
    const tipoSala = document.getElementById('tipo_sala');
    const numeroMesas = document.getElementById('numero_mesas');
    const imagenSala = document.getElementById('imagen_sala');
    const formulario = document.getElementById('dynamicForm');

    // Referencias a mensajes de error
    const accionError = document.getElementById('accionError');
    const salaMesaError = document.getElementById('salaMesaError');
    const numeroMesaError = document.getElementById('numeroMesaError');
    const numeroSillasError = document.getElementById('numeroSillasError');
    const nombreSalaError = document.getElementById('nombreSalaError');
    const tipoSalaError = document.getElementById('tipoSalaError');
    const numeroMesasError = document.getElementById('numeroMesasError');
    const imagenSalaError = document.getElementById('imagenSalaError');

    // Función para cambiar dinámicamente el formulario
    function updateForm() {
        const accionValue = accion.value;

        if (accionValue === 'añadir_mesa') {
            formMesa.classList.remove('hidden');
            formSala.classList.add('hidden');
        } else if (accionValue === 'crear_sala') {
            formMesa.classList.add('hidden');
            formSala.classList.remove('hidden');
        } else {
            formMesa.classList.add('hidden');
            formSala.classList.add('hidden');
        }
    }

    // Funciones de validación
    function validateAccion() {
        if (accion.value === "") {
            accionError.textContent = "Debes seleccionar una acción.";
            accion.style.borderColor = "red";
            return false;
        } else {
            accionError.textContent = "";
            accion.style.borderColor = "";
            return true;
        }
    }

    function validateSalaMesa() {
        if (salaMesa.value === "") {
            salaMesaError.textContent = "Debe seleccionar una sala.";
            salaMesa.style.borderColor = "red";
            return false;
        } else {
            salaMesaError.textContent = "";
            salaMesa.style.borderColor = "";
            return true;
        }
    }

    function validateNumeroMesa() {
        if (numeroMesa.value === "" || parseInt(numeroMesa.value) > 5 || parseInt(numeroMesa.value) <= 0) {
            numeroMesaError.textContent = "Debes añadir entre 1 y 5 mesas.";
            numeroMesa.style.borderColor = "red";
            return false;
        } else {
            numeroMesaError.textContent = "";
            numeroMesa.style.borderColor = "";
            return true;
        }
    }

    function validateNumeroSillas() {
        if (numeroSillas.value === "" || parseInt(numeroSillas.value) > 18 || parseInt(numeroSillas.value) <= 0) {
            numeroSillasError.textContent = "El número de sillas debe ser entre 1 y 18.";
            numeroSillas.style.borderColor = "red";
            return false;
        } else {
            numeroSillasError.textContent = "";
            numeroSillas.style.borderColor = "";
            return true;
        }
    }

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

    function validateTipoSala() {
        if (tipoSala.value === "") {
            tipoSalaError.textContent = "Debes seleccionar un tipo de sala.";
            tipoSala.style.borderColor = "red";
            return false;
        } else {
            tipoSalaError.textContent = "";
            tipoSala.style.borderColor = "";
            return true;
        }
    }

    function validateNumeroMesas() {
        if (numeroMesas.value === "" || parseInt(numeroMesas.value) <= 0) {
            numeroMesasError.textContent = "Debes especificar un número de mesas mayor a 0.";
            numeroMesas.style.borderColor = "red";
            return false;
        } else {
            numeroMesasError.textContent = "";
            numeroMesas.style.borderColor = "";
            return true;
        }
    }

    function validateImagenSala() {
        if (imagenSala.value === "") {
            imagenSalaError.textContent = "Debes subir una imagen de la sala.";
            imagenSala.style.borderColor = "red";
            return false;
        } else {
            imagenSalaError.textContent = "";
            imagenSala.style.borderColor = "";
            return true;
        }
    }

    // Asignación de eventos
    accion.onchange = function () {
        updateForm();
        validateAccion();
    };
    salaMesa.onblur = validateSalaMesa;
    numeroMesa.onblur = validateNumeroMesa;
    numeroSillas.onblur = validateNumeroSillas;
    nombreSala.onblur = validateNombreSala;
    tipoSala.onchange = validateTipoSala;
    numeroMesas.onblur = validateNumeroMesas;
    imagenSala.onchange = validateImagenSala;

    // Validación en el envío del formulario
    formulario.onsubmit = function (event) {
        let isValid = true;

        if (!validateAccion()) isValid = false;

        if (accion.value === 'añadir_mesa') {
            if (!validateSalaMesa()) isValid = false;
            if (!validateNumeroMesa()) isValid = false;
            if (!validateNumeroSillas()) isValid = false;
        }

        if (accion.value === 'crear_sala') {
            if (!validateNombreSala()) isValid = false;
            if (!validateTipoSala()) isValid = false;
            if (!validateNumeroMesas()) isValid = false;
            if (!validateImagenSala()) isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    };
});
