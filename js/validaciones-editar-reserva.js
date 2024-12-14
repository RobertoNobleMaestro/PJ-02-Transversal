document.addEventListener("DOMContentLoaded", function () {
    const turno = document.getElementById('turno');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const formulario = document.getElementById('editReservaForm');
    const turnoError = document.getElementById('turnoError');
    const fechaInicioError = document.getElementById('fechaInicioError');

    function loadTurnos(turnoValue) {
        fechaInicio.innerHTML = "<option value='' disabled>Selecciona una hora</option>";
        const horas = turnoValue === '1' ? ['12:00', '13:00', '14:00', '15:00'] : ['19:00', '20:00', '21:00', '22:00'];

        horas.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora;
            option.textContent = hora;
            fechaInicio.appendChild(option);
        });

        validateForm();
    }

    function updateHoraFin() {
        if (fechaInicio.value) {
            const [hora] = fechaInicio.value.split(':');
            fechaFin.value = `${parseInt(hora) + 1}:00`;
        } else {
            fechaFin.value = ''; // Limpiar la hora de fin si no se seleccionó hora de inicio
        }
    }

    function validateForm() {
        let isValid = true;
        turnoError.textContent = '';
        fechaInicioError.textContent = '';

        if (!turno.value) {
            isValid = false;
            turnoError.textContent = 'Por favor, selecciona un turno.';
        }

        if (!fechaInicio.value) {
            isValid = false;
            fechaInicioError.textContent = 'Por favor, selecciona una hora de inicio.';
        }

        return isValid;
    }

    turno.onchange = function () {
        loadTurnos(turno.value);
        updateHoraFin();
    };
    
    fechaInicio.onchange = updateHoraFin;  // Ahora escuchamos el cambio de fechaInicio para actualizar fechaFin

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
