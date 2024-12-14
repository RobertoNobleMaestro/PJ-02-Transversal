    // Comprobar si hay un parámetro 'error' o 'mensaje' en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const mensaje = urlParams.get('mensaje');

    // Condiciones para mostrar el SweetAlert dependiendo del parámetro
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
            default:
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error inesperado.'
                });
        }
    }

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
            default:
                break;
        }
    }
