document.addEventListener("DOMContentLoaded", function () {
    // Referencias a elementos
    const turno = document.getElementById('turno');
    const formReserva = document.getElementById('form_reserva');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const fechaReserva = document.getElementById('fecha_reserva');
    const formulario = document.getElementById('reservaForm');

    // Referencias a mensajes de error
    const turnoError = document.getElementById('turnoError');
    const fechaInicioError = document.getElementById('fechaInicioError');
    const fechaReservaError = document.getElementById('fechaReservaError');

    // Función para actualizar dinámicamente el formulario
    function updateForm() {
        if (validateTurno()) {
            formReserva.classList.remove('hidden');
            loadTurnos(turno.value);
        } else {
            formReserva.classList.add('hidden');
        }
    }

    // Función para cargar horarios dinámicamente
    function loadTurnos(turnoValue) {
        fechaInicio.innerHTML = "<option value='' disabled select>Selecciona una hora</option>";
        const horas = turnoValue === '1' ? ['12:00', '13:00', '14:00', '15:00'] : ['19:00', '20:00', '21:00', '22:00'];

        horas.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora;
            option.textContent = hora;
            fechaInicio.appendChild(option);
        });
    }

    // Función para actualizar la hora final
    function updateHoraFin() {
        if (validateFechaInicio()) {
            const [hora] = fechaInicio.value.split(':');
            fechaFin.value = `${parseInt(hora) + 1}:00`;
        } else {
            fechaFin.value = '';
        }
    }

    // Validaciones
    function validateTurno() {
        if (turno.value === "") {
            turnoError.textContent = "Debes seleccionar un turno.";
            turno.style.borderColor = "red";
            return false;
        } else {
            turnoError.textContent = "";
            turno.style.borderColor = "";
            return true;
        }
    }

    function validateFechaInicio() {
        if (fechaInicio.value === "") {
            fechaInicioError.textContent = "Debes seleccionar una hora de inicio.";
            fechaInicio.style.borderColor = "red";
            return false;
        } else {
            fechaInicioError.textContent = "";
            fechaInicio.style.borderColor = "";
            return true;
        }
    }

    function validateFechaReserva() {
        if (fechaReserva.value === "") {
            fechaReservaError.textContent = "Debes seleccionar una fecha.";
            fechaReserva.style.borderColor = "red";
            return false;
        } else {
            const hoy = new Date();
            const fechaSeleccionada = new Date(fechaReserva.value);
            if (fechaSeleccionada < hoy) {
                fechaReservaError.textContent = "La fecha debe ser igual o posterior a hoy.";
                fechaReserva.style.borderColor = "red";
                return false;
            } else {
                fechaReservaError.textContent = "";
                fechaReserva.style.borderColor = "";
                return true;
            }
        }
    }

    // Eventos
    turno.onchange = updateForm;
    fechaInicio.onchange = function () {
        validateFechaInicio();
        updateHoraFin();
    };
    fechaReserva.onblur = validateFechaReserva;

    formulario.onsubmit = function (event) {
        let isValid = true;

        if (!validateTurno()) isValid = false;
        if (!validateFechaInicio()) isValid = false;
        if (!validateFechaReserva()) isValid = false;

        if (!isValid) {
            event.preventDefault();
        }
    };
});
