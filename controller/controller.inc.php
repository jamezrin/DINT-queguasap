<?php
/*
*	Muestra el contenido de la parte central de la página
*	E:
*	S:
*	SQL:
*/
function show_content()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {    // GET
        if (!isset($_GET['cmd'])) {                // carga inicial de la página
            show_login();
        } else {
            if ($_GET["cmd"] === "start"
                || $_GET["cmd"] === "logout"
                || $_GET["cmd"] === "registrarse") {
                switch ($_GET['cmd']) {
                    case 'start':
                        show_login();
                        break;

                    case 'logout':
                        show_login();
                        show_msg("Ha cerrado la sesión");
                        break;

                    case 'registrarse':
                        show_register();
                        break;

                    default:
                        "error de conexión";
                        break;
                }
            } elseif (isset ($_SESSION["user"])) {
                switch ($_GET['cmd']) {
                    case 'chat':
                        show_chats();
                        break;

                    case 'nuevo_chat':
                        show_nuevo_chat();
                        break;

                    case 'ver_chat':
                        show_contacto_chat();
                        break;

                    case 'perfil':
                        show_perfil();
                        break;

                    case 'ajustes':
                        show_ajustes();
                        break;

                    case 'borrar_chat':
                        $chat_id = $_GET['id'];
                        show_borrar_chat($chat_id);
                        break;

                    default:
                        "error de conexión";
                        break;
                }
            } else {
                show_login();
            }
        }
    } else {
        if (isset($_POST['login'])) {
            $telefono = $_POST['numero'];
            $contrasena = $_POST['pass_user'];

            if (inicio_usuario_ok($telefono, $contrasena)) {
                show_chats();
            } else {
                show_msg("Error no enviado");
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
                    show_register();
                } else {
                    show_msg('Has sido dado de alta correctamente');
                    show_login();
                }
            } else {
                show_msg($error_validacion);
                show_register();
            }
        } else if (isset($_POST['contestar'])) {
            if (tamaño_img()) {
                if (guardar_mensaje()) {
                    show_msg("Mensaje enviado");
                    show_chats();
                } else {
                    show_msg("Error no enviado");
                }

            } else {
                show_msg("Error imagen demasiado grande 20mb como maximo");
            }
        } else if (isset($_POST['editar'])) {
            if (maximo_caracteres_estado()) {
                if (editar_perfil()) {
                    show_msg("Perfil editado");
                    show_chats();
                } else {
                    show_msg("Error no editado");
                }
            } else {
                show_msg("Error máximo de caracteres");
            }
        } else if (isset($_POST['guardar_color'])) {

            if (color_seleccionado()) {
                show_msg("Color cambiado");
                show_chats();
            } else {
                show_msg("Error no se cambio de color");
            }
        } else if (isset($_POST['backup'])) {

            if (backup_chat()) {
                show_msg("backup guardado");
                show_chats();
            } else {
                show_msg("Error no realizar el backup");
            }
        }

    }
}

function imagen_subida($imagen) {
    return $imagen !== null &&
        $imagen['error'] === UPLOAD_ERR_OK;
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
            $telefono = $_POST['numero'];
            $contrasena = $_POST['pass_user'];

            if (inicio_usuario_ok($telefono, $contrasena)) {
                $_SESSION['user'] = $_POST['numero'];
            }
        }
    } else {
        if (isset($_GET['cmd'])) {
            if ($_GET['cmd'] == 'logout') {
                unset($_SESSION);
                session_destroy();
            }
        }
    }
}
