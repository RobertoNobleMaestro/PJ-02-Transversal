document.addEventListener("DOMContentLoaded", function () {
    // Comprobar si hay un parámetro 'error' o 'mensaje' en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const mensaje = urlParams.get('mensaje');

    // Condiciones para mostrar el SweetAlert dependiendo del parámetro 'error'
    if (error) {
        switch (error) {
            case 'sesion_no_iniciada':
                Swal.fire({
                    icon: 'error',
                    title: 'Sesión no iniciada',
                    text: 'Por favor, inicie sesión para continuar.'
                });
                break;
            case 'duplicado_mesa':
                Swal.fire({
                    icon: 'error',
                    title: 'Número de mesa duplicado',
                    text: 'Ya existe una mesa con el mismo número en esta sala.'
                });
                break;
            case 'duplicado':
                Swal.fire({
                    icon: 'error',
                    title: 'Sala duplicada',
                    text: 'Ya existe una sala con ese nombre.'
                });
                break;
            case 'supera_stock_sillas':
                Swal.fire({
                    icon: 'error',
                    title: 'Stock de sillas excedido',
                    text: 'No puedes añadir más sillas de las disponibles.'
                });
                break;
            case 'datos_incompletos':
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos incompletos',
                    text: 'Por favor, complete todos los campos requeridos.'
                });
                break;
            case 'accion_no_valida':
                Swal.fire({
                    icon: 'error',
                    title: 'Acción no válida',
                    text: 'La acción que intentas realizar no es válida.'
                });
                break;
            case 'imagen_no_permitida':
                Swal.fire({
                    icon: 'error',
                    title: 'Formato de imagen no permitido',
                    text: 'Solo se permiten imágenes con extensión .jpg, .jpeg, .png, o .gif.'
                });
                break;
            case 'error_imagen':
                Swal.fire({
                    icon: 'error',
                    title: 'Error al subir la imagen',
                    text: 'Ocurrió un error al mover la imagen al servidor.'
                });
                break;
            case 'error_base_datos':
                Swal.fire({
                    icon: 'error',
                    title: 'Error de base de datos',
                    text: 'No se pudieron actualizar los datos correctamente. Intente de nuevo.'
                });
                break;
            case 'fecha_pasada':
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha pasada',
                    text: 'No puedes realizar una reserva en el pasado.'
                });
                break;
            case 'hora_fuera_turno':
                Swal.fire({
                    icon: 'error',
                    title: 'Hora fuera de turno',
                    text: 'La hora seleccionada está fuera del rango permitido para este turno.'
                });
                break;
            case 'mesa_invalida':
                Swal.fire({
                    icon: 'error',
                    title: 'Mesa inválida',
                    text: 'La mesa seleccionada no pertenece a esta sala.'
                });
                break;
            case 'solapamiento':
                Swal.fire({
                    icon: 'error',
                    title: 'Solapamiento de reserva',
                    text: 'Ya existe una reserva en el horario seleccionado para esta mesa.'
                });
                break;
            case 'usuario_existente':
                Swal.fire({
                    icon: 'error',
                    title: 'Usuario existente',
                    text: 'Ya existe un usuario con ese nombre. Por favor, elija otro nombre.'
                });
                break;
            case 'Error al eliminar las reservas de recursos relacionadas.':
                Swal.fire({
                    icon: 'error',
                    title: 'Error al eliminar las reservas',
                    text: 'Hubo un problema al eliminar las reservas del usuario.'
                });
                break;
            case 'Error al eliminar el usuario.':
                Swal.fire({
                    icon: 'error',
                    title: 'Error al eliminar el usuario',
                    text: 'No se pudo eliminar el usuario, inténtelo más tarde.'
                });
                break;
            default:
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un error inesperado. Por favor, inténtelo nuevamente.'
                });
        }
    }

    // Condiciones para mostrar el SweetAlert dependiendo del parámetro 'mensaje'
    if (mensaje) {
        switch (mensaje) {
            case 'recurso_actualizado':
                Swal.fire({
                    icon: 'success',
                    title: 'Recurso actualizado',
                    text: 'Los datos de la sala y mesa se han actualizado con éxito.'
                });
                break;
            case 'sala_creada':
                Swal.fire({
                    icon: 'success',
                    title: 'Sala creada',
                    text: 'La sala se ha creado con éxito.'
                });
                break;
            case 'mesas_agregadas':
                Swal.fire({
                    icon: 'success',
                    title: 'Mesas agregadas',
                    text: 'Las mesas han sido agregadas exitosamente.'
                });
                break;
            case 'recurso_eliminado':
                Swal.fire({
                    icon: 'success',
                    title: 'Recurso eliminado',
                    text: 'El recurso ha sido eliminado exitosamente.'
                });
                break;
            case 'Usuario_eliminado_correctamente':
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario eliminado',
                    text: 'El usuario se ha eliminado correctamente.'
                });
                break;
            case 'usuario_creado':
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario creado',
                    text: 'El usuario se ha creado correctamente.'
                });
                break;
            case 'usuario_actualizado':
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario actualizado',
                    text: 'El usuario se ha actualizado correctamente.'
                });
                break;
            case 'reserva_creada':
                Swal.fire({
                    icon: 'success',
                    title: 'Reserva creada',
                    text: 'La reserva ha sido creada correctamente.'
                });
                break;
            case 'reserva_editada':
                Swal.fire({
                    icon: 'success',
                    title: 'Reserva editada',
                    text: 'La reserva ha sido editada correctamente.'
                });
                break;
            case 'Reserva_eliminada_correctamente':
                Swal.fire({
                    icon: 'success',
                    title: 'Reserva eliminada',
                    text: 'La reserva ha sido eliminada correctamente.'
                });
                break;
            default:
                break;
        }
    }
});
