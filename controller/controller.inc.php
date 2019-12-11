<?php
/*
*	Muestra el contenido de la parte central de la página
*	E:
*	S:
*	SQL:
*/
function handle_main() {
    if (sesion_iniciada()) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET["cmd"])) {
                switch ($_GET['cmd']) {
                    case 'chat':
                        wrap_skeleton('show_chats');
                        break;

                    case 'nuevo_chat':
                        wrap_skeleton('show_nuevo_chat');
                        break;

                    case 'perfil':
                        wrap_skeleton('show_perfil');
                        break;

                    case 'ajustes':
                        wrap_skeleton('show_ajustes');
                        break;

                    case 'borrar_chat':
                        show_msg("Esta funcionalidad no ha sido implementada todavía");
                        wrap_skeleton('show_chats');
                        break;

                    case 'ver_chat':
                        wrap_skeleton('show_contacto_chat');
                        break;

                    case 'logout':
                        show_msg("Ha cerrado la sesión");
                        wrap_skeleton('show_login');
                        break;

                    default:
                        show_msg('Comando no valido');
                        wrap_skeleton('show_chats');
                }
            } else {
                wrap_skeleton('show_chats');
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['contestar'])) {
                $telefono = $_SESSION['telefono'];
                $telefono_contacto = $_POST['telefono_contacto'];
                $adjunto = $_FILES['adjunto'];
                $mensaje = $_POST['mensaje'];

                $error_mensaje = validar_mensaje_chat($telefono, $telefono_contacto, $adjunto, $mensaje);
                if (!$error_mensaje) {
                    if (enviar_mensaje($telefono, $telefono_contacto, null, $mensaje)) {
                        show_msg("Mensaje enviado");
                        wrap_skeleton(function() {
                            show_contacto_chat();
                        });
                    } else {
                        show_msg("No se ha podido enviar tu mensaje");
                    }
                } else {
                    show_msg($error_mensaje);
                    wrap_skeleton(function() {
                        show_contacto_chat();
                    });
                }
            } else if (isset($_POST['editar_estado'])) {
                $nuevo_estado = $_POST['nuevo_estado'];
                $telefono = $_SESSION['telefono'];

                if (editar_estado($telefono, $nuevo_estado)) {
                    show_msg("Perfil editado");
                } else {
                    show_msg("Error no editado");
                }

                wrap_skeleton('show_chats');
            } else if (isset($_POST['editar_imagen'])) {
                $imagen = $_FILES['imagen_perfil'];
                $telefono = $_SESSION['telefono'];

                if (imagen_subida($imagen)) {
                    $error_validacion_imagen = validar_imagen($imagen);

                    if (!$error_validacion_imagen) {
                        controlar_cambio_imagen_perfil($telefono, $imagen);

                        wrap_skeleton('show_chats');
                    } else {
                        show_msg($error_validacion_imagen);
                        wrap_skeleton('show_perfil');
                    }
                } else {
                    show_msg("No se ha subido ninguna imagen");
                    wrap_skeleton('show_perfil');
                }
            } else if (isset($_POST['guardar_color'])) {
                $telefono = $_SESSION['telefono'];
                $color = $_POST['color'];
                if (cambiar_color($telefono, $color)) {
                    wrap_skeleton('show_ajustes');
                } else {
                    show_msg("Error no se cambio de color");
                    wrap_skeleton('show_ajustes');
                }
            } else if (isset($_POST['backup'])) {
                $telefono = $_SESSION['telefono'];
                $telefono_contacto = $_POST['telefono_contacto'];
                $nombre_archivo = $_POST['nombre'];
                if (!backup_chat($telefono, $telefono_contacto, $nombre_archivo)) {
                    wrap_skeleton('show_chats');
                }
            } else {
                wrap_skeleton('show_chats');
            }
        }
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['cmd'])) {
                switch ($_GET['cmd']) {
                    case 'registrarse':
                        wrap_skeleton('show_register');
                        break;
                    default:
                        wrap_skeleton('show_login');
                }
            } else {
                wrap_skeleton('show_login');
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['login'])) {
                $telefono = $_POST['telefono'];
                $contrasena = $_POST['contrasena'];

                if (inicio_usuario_ok($telefono, $contrasena)) {
                    show_menu();
                    show_chats();
                } else {
                    show_msg("Has introducido un telefono o contraseña no validos");
                    wrap_skeleton('show_login');
                }
            } else if (isset($_POST['alta_usuario'])) {
                $telefono = $_POST['telefono'];
                $contrasena = $_POST['password'];
                $contrasena_confirm = $_POST['password_confirm'];
                $nombre = $_POST['nombre'];
                $imagen = $_FILES['imagen_perfil'];

                $error_validacion = validar_datos_registro($telefono, $contrasena,
                    $contrasena_confirm, $nombre, $imagen);

                if (!$error_validacion) {
                    $nombre_imagen = null;

                    if (imagen_subida($imagen)) {
                        $nombre_imagen = generar_nombre_foto_perfil($imagen, $telefono);
                    }

                    $error_alta = alta_usuario_ok($telefono, $contrasena, $nombre, $nombre_imagen);
                    if ($error_alta) {
                        if ($error_alta === 1406) {
                            show_msg("Se ha introducido un campo no valido");
                        } else if ($error_alta === 1062) {
                            show_msg("Ya existe un usuario con ese telefono");
                        } else {
                            show_msg("Ha ocurrido el error al darte de alta $error_alta");
                        }

                        wrap_skeleton('show_register');
                    } else {
                        if ($nombre_imagen !== null) {
                            $destino_imagen = generar_ruta_imagen_perfil($nombre_imagen);
                            move_uploaded_file($imagen['tmp_name'], $destino_imagen);
                        }

                        show_msg('Has sido dado de alta correctamente');

                        wrap_skeleton('show_login');
                    }
                } else {
                    show_msg($error_validacion);
                    show_register();
                }
            }
        }
    }
}

function imagen_subida($imagen) {
    return $imagen !== null &&
        $imagen['error'] === UPLOAD_ERR_OK;
}

function controlar_imagen_perfil($campo_imagen) {
    if ($campo_imagen !== null) {
        return "content/profile_images/$campo_imagen";
    } else {
        return "view/images/avatar.png";
    }
}

function controlar_cambio_imagen_perfil($telefono, $nueva_imagen) {
    $info = consultar_usuario($telefono);
    $imagen_actual = $info['imagen'];

    if ($imagen_actual) {
        $ruta_imagen_actual = generar_ruta_imagen_perfil($imagen_actual);

        if (file_exists($ruta_imagen_actual)) {
            chmod($ruta_imagen_actual,0755);
            unlink($ruta_imagen_actual);
        }
    }

    $nombre_imagen = generar_nombre_foto_perfil($nueva_imagen, $telefono);
    $destino_imagen = generar_ruta_imagen_perfil($nombre_imagen);

    editar_imagen($telefono, $nombre_imagen);
    move_uploaded_file($nueva_imagen['tmp_name'], $destino_imagen);
}

function generar_nombre_foto_perfil($imagen, $telefono) {
    // numero aleatorio para que la imagen cambie en la cache
    $aleatorio = rand();

    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    return "foto-$telefono-$aleatorio.$extension";
}

function generar_ruta_imagen_perfil($nombre_imagen) {
    return getcwd() . "/content/profile_images/$nombre_imagen";
}

function validar_datos_registro($telefono, $contrasena, $contrasena_confirm, $nombre, $imagen) {
    if (strlen($telefono) !== 9 || !ctype_digit($telefono)) {
        return "El telefono tiene que tener 9 numeros";
    }

    if (trim($telefono) === '' || trim($contrasena) === ''
        || trim($contrasena_confirm) === '' || trim($nombre) === '') {
        return "Tienes que rellenar todos los campos";
    }

    if ($contrasena !== $contrasena_confirm) {
        return 'Las contraseñas no son iguales';
    }

    $error_validar_imagen = validar_imagen($imagen);
    if ($error_validar_imagen) {
        return $error_validar_imagen;
    }

    return null;
}

function validar_mensaje_chat($telefono, $telefono_contacto, $adjunto, $mensaje) {
    if (trim($mensaje) == '') {
        return "El mensaje no puede estar vacio";
    }

    // todo: permitir otros archivos que no sean imagenes?
    $error_validar_imagen = validar_imagen($adjunto);
    if ($error_validar_imagen) {
        return $error_validar_imagen;
    }

    return null;
}

function validar_imagen($imagen) {
    if (imagen_subida($imagen)) {
        if ($imagen['type'] !== "image/png" &&
            $imagen['type'] !== "image/gif" &&
            $imagen['type'] !== "image/jpeg") {
            return "La imagen de perfil que has subido no es valida";
        }

        global $config;
        $limite = (int) $config['TAM_IMAGEN'];

        if ($imagen ['size'] > $limite * 1000 * 1000) {
            return "La imagen que has elegido es mas grande que $limite megas";
        }
    }

    return null;
}

/*
* Realiza algunas acciones según esté la sesión abierta o no
* E:
* S:
* SQL:
*/
function actualizar_sesion() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['login'])) {
            $telefono = $_POST['telefono'];
            $contrasena = $_POST['contrasena'];

            if (inicio_usuario_ok($telefono, $contrasena)) {
                $_SESSION['telefono'] = $telefono;
                cambiar_conectado($telefono, true);
            }
        }
    } else {
        if (isset($_GET['cmd'])) {
            if ($_GET['cmd'] == 'logout') {
                $telefono = $_SESSION['telefono'];
                cambiar_conectado($telefono, false);

                unset($_SESSION);
                session_destroy();
            }
        }
    }
}

// Envuelve la vista en el esqueleto con la cabecera, el menu, el propio contenido y el footer
function wrap_skeleton($func) {
    show_header();
    show_menu();
    call_user_func($func);
    show_footer();
}

function sesion_iniciada() {
    return isset($_SESSION["telefono"]);
}

function mapear_color($color) {
    global $config;
    switch ($color) {
        case "defecto":
            return $config['BACK_COLOR'];
        case "verde":
            return "#008f39";
        case "rojo":
            return "#cb3234";
        case "blanco":
            return "#ffffff";
        case "azul":
            return "#3b83bd";
        case "rosa":
            return "#ff0080";
        default:
            return "#000000";
    }
}