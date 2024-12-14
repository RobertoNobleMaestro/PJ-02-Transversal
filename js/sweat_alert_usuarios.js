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

    if (mensaje) {
        switch (mensaje) {
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
            default:
                break;
        }
    }
    