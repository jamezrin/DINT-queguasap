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
            show_loging();
        } else {
            if ($_GET["cmd"] === "start"
                || $_GET["cmd"] === "logout"
                || $_GET["cmd"] === "registrarse") {
                switch ($_GET['cmd']) {
                    case 'start':
                        show_loging();
                        break;

                    case 'logout':
                        show_loging();
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
                show_loging();
            }


        }
    } else {                                        // POST
        if (isset($_POST['login'])) {
            if (login_ok()) {
                show_chats();
            } else {
                show_msg("Error no enviado");
            }
        } else if (isset($_POST['alta_usuario'])) {
            $telefono = $_POST['telefono'];
            $contrasena = $_POST['password'];
            $contrasena_confirm = $_POST['password_confirm'];
            $nombre = $_POST['nombre'];
            $imagen = $_FILES['imagen_perfil'];

            $error_validacion = validar_datos_registro($telefono, $contrasena,
                $contrasena_confirm, $nombre, $imagen);

            if ($error_validacion) {
                show_msg($error_validacion);
            } else {
                $error_alta = alta_usuario_ok($telefono, $contrasena, $nombre, $imagen);
                if ($error_alta) {
                    show_msg(mapear_error_sql($error_alta));
                } else {
                    show_msg('Has sido dado de alta correctamente');
                }

            }
/*
            if (validar_datos_registro()) {
                if (alta_usuario_ok()) {
                    show_msg("Usuario registrado");
                    show_loging();
                } else {
                    show_msg("No se ha podido dar de alta a ese usuario");
                    show_register();
                }
            } else {
                show_msg("Los datos que has introducido no son validos");
                show_register();
            }*/
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

/*
* Realiza algunas acciones según esté la sesión abierta o no
* E:
* S:
* SQL:
*/
function actualizar_sesion()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['login'])) {
            if (login_ok()) {
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

function mapear_error_sql($codigo_error) {
    switch ($codigo_error) {
        case 1406: return "Se ha introducido un campo no valido";
        case 1062: return "Ya existe un usuario con ese telefono";
        default: return $codigo_error;
    }
}
?>
