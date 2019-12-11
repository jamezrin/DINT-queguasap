<?php

/*
*	Muestra el menú
*	E: nada
*	S: nada
*	SQL: select logo, texto from usuario
*/
function show_menu() {
    if (sesion_iniciada()) {
        $telefono = $_SESSION['telefono'];
        $props = consultar_usuario($telefono);
        $imagen_perfil = controlar_imagen_perfil($props['imagen']);
        $estado = $props['estado'];

        echo "
            <header>
                <section id=\"estado\">
                  <img src=\"$imagen_perfil\" class=\"imgRedonda\"/><br>
                  <p class='estado'>$estado</p>
                </section>
    
                <nav class=\"menu\">
                  <ul>
                    <li><a href=\"index.php?cmd=chat\" class=\"btn\">Chats activos</a></li>
                    <li><a href=\"index.php?cmd=nuevo_chat\" class=\"btn\">Nuevo chat</a></li>
                    <li><a href=\"index.php?cmd=perfil\" class=\"btn\">Perfil</a></li>
                    <li><a href=\"index.php?cmd=ajustes\" class=\"btn\">
                        <img src=\"view/images/ajustes.png\" width=30 height=30 /></a></li>
                    <li><a href=\"index.php?cmd=logout\" class=\"btn\">Logout</a></li>
                  </ul>
                </nav>
		   </header>";
    } else {
        echo '
            <header>
                <br>
                <h1>CHATING</h1>
			</header>';
    }
}


/*
*	Muestra el formulario de inicio de sesión
*	E:
*	S:
*	SQL:
*/
function show_login() {
    echo '
		<section id="slider">
			<form action="index.php" method="post" role="form">
					<h2>Entrar</h2>
					<input id="numero" type="text" name="telefono" placeholder="número de telefono" required="" ><br><br>
					<input id="pass" type="password" name="contrasena" placeholder="password" required="" ><br><br>
					<button type="submit" name="login">Login</button><br><br>    
			</form>
			
			<a href="index.php?cmd=registrarse">
			    <button name="registrarse">Registrarse</button>
			</a>
			
			<br><br>    
		</section>';
}

function show_chats() {
    $conn = connection();
    $telefono = $_SESSION['telefono'];

    try {
        $stmt = $conn->prepare("
            SELECT usuarios.telefono, usuarios.conectado, usuarios.nombre FROM (
                SELECT DISTINCT receptor AS telefono FROM envia_mensaje WHERE emisor = ?
                UNION
                SELECT DISTINCT emisor AS telefono FROM envia_mensaje WHERE receptor = ?
            ) conversacion INNER JOIN usuarios ON usuarios.telefono = conversacion.telefono;
        ");

        /*
        alternativa
        SELECT DISTINCT usuarios.telefono, envia_mensaje.emisor AS emisor, envia_mensaje.receptor AS receptor, usuarios.conectado, usuarios.nombre
        FROM usuarios
        INNER JOIN envia_mensaje
        ON (envia_mensaje.receptor = usuarios.telefono AND envia_mensaje.emisor = ?)
        OR (envia_mensaje.emisor = usuarios.telefono AND envia_mensaje.receptor = ?);
         */

        $stmt->bind_param("ss",
            $telefono,
            $telefono);

        $stmt->execute();
        $result = $stmt->get_result();

        echo "<section id=\"chats\">";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $otro_telefono = $row['telefono'];
                $nombre = $row['nombre'];
                $conectado = $row['conectado'];
                $imagen_conectado = $conectado ?
                    "view/images/verde.png" :
                    "view/images/rojo.png";

                echo "
                <h3>
                    <a href=\"index.php?cmd=ver_chat&telefono=$otro_telefono\" class=\"btn\">$nombre
                       <img src=\"$imagen_conectado\" width=10 height=10 />
                    </a>
                    
                    <a href=\"index.php?cmd=borrar_chat&telefono=$otro_telefono\">
                        <img src=\"view/images/equis.png\" width=10 height=10 />
                    </a>
                </h3>
                <br><br><br>
            ";
            }
        } else {
            echo "<h3>¿Estás más solo que la una?</h3>
                  <img src=\"view/images/pulgar.png\" width=250 height=275 />
                  <h3>Pulsa en \"Nuevo chat\" y conoce gente única</h3>
                  ";
        }
        echo "</section>";

        $stmt->close();
        return 0;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function show_nuevo_chat() {
    $conn = connection();
    $telefono = $_SESSION['telefono'];

    $stmt = $conn->prepare("
        SELECT DISTINCT telefono, conectado, nombre 
        FROM usuarios
        WHERE telefono NOT IN (
            SELECT usuarios.telefono FROM (
                SELECT DISTINCT receptor AS telefono FROM envia_mensaje WHERE emisor = ?
                UNION
                SELECT DISTINCT emisor AS telefono FROM envia_mensaje WHERE receptor = ?
            ) conversacion INNER JOIN usuarios ON usuarios.telefono = conversacion.telefono
        );
    ");

    $stmt->bind_param("ss",
        $telefono,
        $telefono);

    $stmt->execute();
    $result = $stmt->get_result();

    echo "<section id=\"chats\">";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $otro_telefono = $row['telefono'];
            $nombre = $row['nombre'];
            $conectado = $row['conectado'];
            $imagen_conectado = $conectado ?
                "view/images/verde.png" :
                "view/images/rojo.png";

            echo "
                <h3>
                    <a href=\"index.php?cmd=ver_chat&telefono=$otro_telefono\" class=\"btn\">$nombre
                       <img src=\"$imagen_conectado\" width=10 height=10 />
                    </a>
                    
                    <a href=\"index.php?cmd=borrar_chat&telefono=$otro_telefono\">
                        <img src=\"view/images/equis.png\" width=10 height=10 />
                    </a>
                </h3>
                <br><br><br>
            ";
        }
    } else {
        echo "<h3>Ya hablas con todos, ¡genial!</h3>
                  <img src=\"view/images/pulgar.png\" width=250 height=275 />
                  ";
    }
    echo "</section>";

    $stmt->close();
    return 0;
}

/*
*	Muestra un mensaje de tipo alert
*	E: $msg (mensaje que se quiere mostrar en alert)
*	S: nada
*/
function show_msg($msg) {
    echo "<script type='text/javascript'>alert('" . $msg . "');</script>";
}

function show_contacto_chat() {
    $conn = connection();

    $telefono = $_SESSION['telefono'];
    $telefono_contacto = $_GET['telefono'];

    try {
        $props = consultar_usuario($telefono_contacto);

        if ($props) {
            $imagen_perfil = controlar_imagen_perfil($props['imagen']);
            $nombre_contacto = $props['nombre'];
            $estado_contacto = $props['estado'];

            $stmt = $conn->prepare("
                SELECT usuarios.nombre AS nombre_emisor, telefono, momento, texto, archivo 
                FROM envia_mensaje 
                INNER JOIN usuarios
                    ON envia_mensaje.emisor = usuarios.telefono
                WHERE (emisor = ? AND receptor = ?) 
                    OR (receptor = ? AND emisor = ?)
                ORDER BY momento DESC;
            ");

            $stmt->bind_param("ssss",
                $telefono, $telefono_contacto,
                $telefono, $telefono_contacto);

            $stmt->execute();
            $result = $stmt->get_result();

            echo "
                <section id=\"datosP\">
                    <section class=\"datosU\">
                        <img src=\"$imagen_perfil\" class=\"imgRedonda\"/>
                        <h3>$nombre_contacto: $estado_contacto</h3><br><br><br>
            ";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $nombre_emisor = $row['nombre_emisor'];
                    $momento = $row['momento'];
                    $texto = $row['texto'];
                    $archivo = $row['archivo'];
                    echo "
                  <section class=\"mensajeU\">
                      <h4>$nombre_emisor $momento</h4>
                      <p>$texto</p>
                      <div></div>";

                    if ($archivo !== null) {
                        echo "<img src=\"content/attachments/$archivo\" width=100 height=100 />";
                    }

                    echo "</section>";
                }
            } else {
                echo "<h3 class='mensajeU'>Dile algo a tu amigo</h3>";
            }

            echo "
                </section>
                
                <section class=\"contestar_mensaje\">
                    <form id=\"vb\" action=\"index.php\" method=\"post\" role=\"form\" enctype='multipart/form-data'>
                        <textarea id=\"mensaje\" name=\"mensaje\" placeholder=\"Mensaje\" rows=\"5\" cols=\"40\" required=\"\" style=\"resize: none;\" ></textarea>
                        
                        <br>
                        <br>
        
                        <span>Elegir archivo<input type=\"file\" name=\"adjunto\" multiple></span>
                        <input type=\"hidden\" name=\"telefono_contacto\" value=\"$telefono_contacto\">
               
                        <button type=\"submit\" name=\"contestar\" >Contestar</button><br><br>
        
                    </form>
    
                    <form id=\"vb\" action=\"index.php\" method=\"post\" role=\"form\">
                        <h5>Realiza un backup de este chat y asignale un nombre al fichero</h5>
        
                        <input id=\"nombre\" type=\"text\" name=\"nombre\" placeholder=\"nombre del fichero\" required=\"\" ><br><br>
                        <input type=\"hidden\" name=\"telefono_contacto\" value=\"$telefono_contacto\">
                        <button type=\"submit\" name=\"backup\" >Backup</button><br><br>
                    </form>
                </section>
            </section>
            ";

            $stmt->close();
        } else {
            show_msg("No existe ningun usuario con ese numero de telefono");
            show_chats();
        }

        return 0;
    } catch (Exception $e) {
        echo $e;
        return $e->getCode();
    }
}

/*
*	Muestra la página modificar el perfil
*	E:
*	S:
*	SQL:
*/
function show_perfil() {
    global $config;
    $long_texto = $config["LONG_TEXTO"];

    $telefono = $_SESSION['telefono'];
    $info = consultar_usuario($telefono);
    $estado = $info['estado'];
    echo "
        <section id=\"perfil\">
            <form action=\"index.php\" method=\"POST\" role=\"form\" enctype=\"multipart/form-data\">
                <label for=\"imagen_perfil\">Cambiar imagen de perfil</label><br>
                <input type=\"file\" name=\"imagen_perfil\" id=\"imagen_perfil\"><br>
                <button type=\"submit\" name=\"editar_imagen\">Editar Imagen</button>
            </form>
             
            <br><br>
             
            <form action=\"index.php\" method=\"POST\" role=\"form\">
                <label for=\"nuevo_estado\">Cambiar estado</label><br>
                <textarea id=\"nuevo_estado\" name=\"nuevo_estado\" rows=\"5\" cols=\"40\" required maxlength=\"$long_texto\" style=\"resize: none;\">$estado</textarea><br>
                <button type=\"submit\" name=\"editar_estado\">Editar Estado</button>
            </form>
        </section>";
}

/*
* Muestra los ajustes para cambiar el color del fondo de la pagina web
* E:
* S
* SQL:
*/
function show_ajustes() {
    echo '
        <section id="ajustes">
            <form id="vb" action="index.php" method="POST" role="form">
        
              <h4>Selecciona un color de fondo
                  <select name="color">
                        <option value="defecto">Defecto</option>
                        <option value="rojo">Rojo</option>
                        <option value="verde">Verde</option>
                        <option value="azul">Azul</option>
                        <option value="blanco">Blanco</option>
                        <option value="rosa">Rosa</option>
                  </select>
              </h4>
        
              <button type="submit" name="guardar_color">Guardar</button>
            </form>
        </section>';
}

function show_register() {
    echo ' 
        <section id="slider">
        <h2>Registrate</h2>
            <form enctype="multipart/form-data" action="" method="post">
                  <div>
                      <label>Numero de telefono</label>
                      <input type="text" placeholder="Numero de telefono" id="telefono" name="telefono">
                  </div>
                  <div>
                      <label>Contraseña</label>
                      <input type="password" name="password" id="password" placeholder="Contraseña">
                  </div>
                  <div>
                      <label>Confirmar Contraseña</label>
                      <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmar Contraseña">
                  </div>
                   <div>
                      <label for="nombre">Nombre de usuario</label>
                      <input type="text" id="nombre" name="nombre" placeholder="Tu nombre de usuario">
                  </div>
                  <div>
                      <label for="imagen_perfil">Foto de perfil</label>
                      <input type="file" name="imagen_perfil" id="imagen_perfil" >
                  </div>
                  <button type="submit" name="alta_usuario">Registrarse</button>
            </form>
        </section>';
}
