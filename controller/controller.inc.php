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
                        show_base(function() {
                            show_chats();
                        });
                        break;

                    case 'nuevo_chat':
                        show_base(function() {
                            show_nuevo_chat();
                        });
                        break;

                    case 'perfil':
                        show_base(function() {
                            show_perfil();
                        });
                        break;

                    case 'ajustes':
                        show_base(function() {
                            show_ajustes();
                        });
                        break;

                    case 'borrar_chat':
                        show_msg("Esta funcionalidad no ha sido implementada todavía");
                        show_base(function() {
                            show_chats();
                        });
                        break;

                    case 'ver_chat':
                        show_base(function() {
                            show_contacto_chat();
                        });
                        break;

                    case 'logout':
                        show_msg("Ha cerrado la sesión");
                        show_base(function() {
                            show_login();
                        });
                        break;

                    default:
                        show_msg('Comando no valido');
                        show_base(function() {
                            show_chats();
                        });
                }
            } else {
                show_base(function() {
                    show_chats();
                });
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // todo hacer esto
            if (isset($_POST['contestar'])) {
                if (tamaño_img()) {
                    if (guardar_mensaje()) {
                        show_msg("Mensaje enviado");
                        show_base(function() {
                            show_chats();
                        });
                    } else {
                        show_msg("Error no enviado");
                    }
                } else {
                    show_msg("Error imagen demasiado grande 20mb como maximo");
                }
            } else if (isset($_POST['editar_estado'])) {
                if (editar_perfil()) {
                    show_msg("Perfil editado");
                } else {
                    show_msg("Error no editado");
                }

                show_base(function() {
                    show_chats();
                });
            } else if (isset($_POST['editar_imagen'])) {
                $imagen = $_FILES['imagen_perfil'];
                $telefono = $_SESSION['telefono'];

                if (imagen_subida($imagen)) {
                    $error_validacion_imagen = validar_imagen($imagen);

                    if (!$error_validacion_imagen) {
                        $info = consultar_usuario($telefono);
                        $imagen_actual = $info['imagen'];

                        if ($imagen_actual) {
                            $ruta_imagen_actual = generar_ruta_imagen_perfil($imagen_actual);

                            if (file_exists($ruta_imagen_actual)) {
                                chmod($ruta_imagen_actual,0755);
                                unlink($ruta_imagen_actual);
                            }
                        }

                        $nombre_imagen = generar_nombre_foto_perfil($imagen, $telefono);
                        $destino_imagen = generar_ruta_imagen_perfil($nombre_imagen);

                        editar_imagen($telefono, $nombre_imagen);
                        move_uploaded_file($imagen['tmp_name'], $destino_imagen);

                        show_base(function() {
                            show_chats();
                        });
                    } else {
                        show_msg($error_validacion_imagen);
                        show_base(function() {
                            show_perfil();
                        });
                    }
                } else {
                    show_msg("No se ha subido ninguna imagen");
                    show_base(function() {
                        show_perfil();
                    });
                }
            } else if (isset($_POST['guardar_color'])) {
                if (color_seleccionado()) {
                    show_base(function() {
                        show_ajustes();
                    });
                } else {
                    show_msg("Error no se cambio de color");
                    show_base(function() {
                        show_ajustes();
                    });
                }
            } else if (isset($_POST['backup'])) {
                if (!backup_chat()) {
                    show_base(function() {
                        show_chats();
                    });
                }
            } else {
                show_base(function() {
                    show_chats();
                });
            }
        }
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['cmd'])) {
                switch ($_GET['cmd']) {
                    case 'registrarse':
                        show_base(function() {
                            show_register();
                        });
                        break;
                    default:
                        show_base(function() {
                            show_login();
                        });
                }
            } else {
                show_base(function() {
                    show_login();
                });
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
                    show_base(function() {
                        show_login();
                    });
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

                        show_base(function() {
                            show_register();
                        });
                    } else {
                        if ($nombre_imagen !== null) {
                            $destino_imagen = getcwd() . "/content/profile_images/$nombre_imagen";
                            move_uploaded_file($imagen['tmp_name'], $destino_imagen);
                        }

                        show_msg('Has sido dado de alta correctamente');

                        show_base(function() {
                            show_login();
                        });
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

function show_base($callback) {
    show_header();
    show_menu();
    $callback();
    show_footer();
}

function sesion_iniciada() {
    return isset($_SESSION["telefono"]);
}

