document.addEventListener("DOMContentLoaded", function () {
    // Referencias a elementos
    const turno = document.getElementById('turno');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const fechaReserva = document.getElementById('fecha_reserva');
    const formulario = document.getElementById('editReservaForm');

    // Referencias a mensajes de error
    const turnoError = document.getElementById('turnoError');
    const fechaInicioError = document.getElementById('fechaInicioError');
    const fechaReservaError = document.getElementById('fechaReservaError');

    // Función para cargar horarios dinámicamente
    function loadTurnos(turnoValue) {
        fechaInicio.innerHTML = "<option value='' selected disabled>Selecciona una hora</option>";
        let horas = turnoValue === '1' ? ['12:00', '13:00', '14:00', '15:00'] : ['19:00', '20:00', '21:00', '22:00'];

        // Verificar si la fecha seleccionada es hoy
        const hoy = new Date();
        const fechaSeleccionada = new Date(fechaReserva.value);
        hoy.setHours(0, 0, 0, 0);
        fechaSeleccionada.setHours(0, 0, 0, 0);

        if (fechaSeleccionada.getTime() === hoy.getTime()) {
            const horaActual = new Date().getHours();
            horas = horas.filter(hora => parseInt(hora.split(':')[0]) > horaActual);
        }

        horas.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora;
            option.textContent = hora;
            fechaInicio.appendChild(option);
        });

        validateForm();
    }

    // Función para actualizar la hora final
    function updateHoraFin() {
        if (validateFechaInicio()) {
            const [hora] = fechaInicio.value.split(':');
            fechaFin.value = `${parseInt(hora) + 1}:00`;
        } else {
            fechaFin.value = ''; // Limpiar la hora de fin si no se seleccionó hora de inicio
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

            hoy.setHours(0, 0, 0, 0);
            fechaSeleccionada.setHours(0, 0, 0, 0);

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

    function validateForm() {
        let isValid = true;
        turnoError.textContent = '';
        fechaInicioError.textContent = '';

        if (!validateTurno()) isValid = false;
        if (!validateFechaInicio()) isValid = false;
        if (!validateFechaReserva()) isValid = false;

        return isValid;
    }

    // Eventos
    turno.onchange = function () {
        loadTurnos(turno.value);
        updateHoraFin();
    };

    fechaInicio.onchange = function () {
        validateFechaInicio();
        updateHoraFin();
    };

    fechaReserva.onblur = function () {
        if (validateFechaReserva()) {
            loadTurnos(turno.value);
        }
    };

    formulario.onsubmit = function (event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    };

    // Si ya hay un turno seleccionado, cargar las horas al cargar la página
    if (turno.value) {
        loadTurnos(turno.value);
    }

    // Si ya hay una hora de inicio seleccionada, actualizar la hora de fin
    if (fechaInicio.value) {
        updateHoraFin();
    }
});
