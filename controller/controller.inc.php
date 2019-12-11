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
                        show_menu();
                        show_chats();
                        break;

                    case 'nuevo_chat':
                        show_menu();
                        show_nuevo_chat();
                        break;

                    case 'perfil':
                        show_menu();
                        show_perfil();
                        break;

                    case 'ajustes':
                        show_menu();
                        show_ajustes();
                        break;

                    case 'borrar_chat':
                        $chat_id = $_GET['id'];
                        show_menu();
                        show_borrar_chat($chat_id);
                        break;

                    case 'ver_chat':
                        show_menu();
                        show_contacto_chat();
                        break;

                    case 'logout':
                        show_menu();
                        show_login();
                        show_msg("Ha cerrado la sesión");
                        break;

                    default:
                        show_msg('Comando no valido');
                        show_menu();
                        show_chats();
                }
            } else {
                show_chats();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['contestar'])) {
                if (tamaño_img()) {
                    if (guardar_mensaje()) {
                        show_msg("Mensaje enviado");
                        show_menu();
                        show_chats();
                    } else {
                        show_msg("Error no enviado");
                    }
                } else {
                    show_msg("Error imagen demasiado grande 20mb como maximo");
                }
            } else if (isset($_POST['editar_estado'])) {
                if (maximo_caracteres_estado()) {
                    if (editar_perfil()) {
                        show_msg("Perfil editado");
                        show_menu();
                        show_chats();
                    } else {
                        show_msg("Error no editado");
                    }
                } else {
                    show_msg("Error máximo de caracteres");
                }
            } else if (isset($_POST['editar_imagen'])) {
                $imagen = $_FILES['imagen_perfil'];
                $telefono = $_SESSION['telefono'];

                if (imagen_subida($imagen)) {
                    $nombre_imagen = generar_nombre_foto_perfil($imagen, $telefono);
                    $destino_imagen = getcwd() . "/content/profile_images/$nombre_imagen";

                    if (file_exists($destino_imagen)) {
                        chmod($destino_imagen,0755);
                        unlink($destino_imagen);
                    }

                    editar_imagen($telefono, $nombre_imagen);

                    move_uploaded_file($imagen['tmp_name'], $destino_imagen);

                    show_menu();
                    show_chats();
                } else {
                    show_msg("No se ha subido ninguna imagen");
                    show_menu();
                    show_perfil();
                }
            } else if (isset($_POST['guardar_color'])) {
                if (color_seleccionado()) {
                    show_msg("Color cambiado");
                    show_menu();
                    show_chats();
                } else {
                    show_menu();
                    show_msg("Error no se cambio de color");
                    show_ajustes();
                }
            } else if (isset($_POST['backup'])) {
                if (backup_chat()) {
                } else {
                    show_menu();
                    show_msg("Error no realizar el backup");
                }
            } else {
                show_menu();
                show_chats();
            }
        }
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['cmd'])) {
                switch ($_GET['cmd']) {
                    case 'registrarse':
                        show_menu();
                        show_register();
                        break;
                    default:
                        show_menu();
                        show_login();
                }
            } else {
                show_menu();
                show_login();
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
                    show_menu();
                    show_login();
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
                        $destino_imagen = getcwd() . "/content/profile_images/$nombre_imagen";
                        move_uploaded_file($imagen['tmp_name'], $destino_imagen);
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
                        show_menu();
                        show_register();
                    } else {
                        show_msg('Has sido dado de alta correctamente');
                        show_login();
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

function controlar_imagen($campo_imagen) {
    if ($campo_imagen !== null) {
        return "content/profile_images/$campo_imagen";
    } else {
        return "view/images/avatar.png";
    }
}

function generar_nombre_foto_perfil($imagen, $telefono) {
    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    return "foto-$telefono.$extension";
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

    if (imagen_subida($imagen)) {
        if ($imagen['type'] !== "image/png" &&
            $imagen['type'] !== "image/gif" &&
            $imagen['type'] !== "image/jpeg") {
            return "La imagen de perfil que has subido no es valida";
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

function sesion_iniciada() {
    return isset($_SESSION["telefono"]);
}
