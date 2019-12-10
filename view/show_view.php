<?php

/*
*	Muestra el menú
*	E: nada
*	S: nada
*	SQL: select logo, texto from usuario
*/
function show_menu() {

    if (isset($_SESSION['user'])) {

        echo '<header>

			<section id="estado">
			  <img src="view/images/f.jpg" class="imgRedonda"/><br>
			  <p>I am working</p>
			</section>

			<nav class="menu">

			  <ul>
				<li><a href="index.php?cmd=chat" class="btn">Chats activos</a></li>
				<li><a href="index.php?cmd=nuevo_chat" class="btn">Nuevo chat</a></li>
				<li><a href="index.php?cmd=perfil" class="btn">Perfil</a></li>
				<li><a href="index.php?cmd=ajustes" class="btn"><img src="view/images/ajustes.png" width=30 height=30 /></a></li>
				<li><a href="index.php?cmd=logout" class="btn">Logout</a></li>
			  </ul>
			</nav>
		   </header>';
    } else {


        echo '<header>
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
					<h2>LOG IN</h2>
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

/*
* Muestra los diferentes tipos de chat
* E: nada
* S: nada
* SQL: select idChat, telefono from TIENE where numero =  $_SESSION['user'];
*/
function show_chats() {
    echo '
	<section id="chats">
	  <h3><a href="index.php?cmd=ver_chat" class="btn">Fulanito
	  <img src="view/images/verde.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=1"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Menganito
	  <img src="view/images/rojo.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=2"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Mariano
	  <img src="view/images/rojo.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=3"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Sefora
	  <img src="view/images/verde.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=4"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Romero
	  <img src="view/images/verde.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=5"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Goku
	  <img src="view/images/verde.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=6"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Vegeta
	  <img src="view/images/rojo.png" width=10 height=10 /></a>
	  <a href="index.php?cmd=borrar_chat&id=7"><img src="view/images/equis.png" width=10 height=10 /></a>
	  </h3><br>
	  <div></div><br><br>
	</section>
';
}

/*
* Crea un nuevo chat con gente con la que nunca se ha hablado antes
* E: nada
* S: nada
* SQL: select idChat, telefono from TIENE where numero not in (select telefono from TIENE )
*/
function show_nuevo_chat()
{
    echo '
	<section id="chats">
	  <h3><a href="index.php?cmd=ver_chat" class="btn">Iñaki
	  <img src="view/images/verde.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Miguel
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>
	  
	  <h3><a href="index.php?cmd=ver_chat" class="btn">Alex
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>
	  
	  <h3><a href="index.php?cmd=ver_chat" class="btn">Robert
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>
	</section>
	';
}

/*
 * Pregunta si el usuario quiere eliminar el chat en contexto
 * E: $chat_id (el identificador del chat)
 * S: nada
 */
function preguntar_borrar_chat($chat_id)
{
    show_confirm("¿Quieres borrar la conversación con el identificador $chat_id?");
    return true;
}

/*
 * Pide confirmación si quiere borar el chat y lo borra
 * E: $chat_id (el identificador del chat)
 * S: nada
 * SQL: nada
 */
function show_borrar_chat($chat_id)
{
    if (preguntar_borrar_chat($chat_id)) {
        if (borrar_chat_ok()) {
            echo "<p class=\"centro\">Has borrado el chat con el identificador $chat_id</p>";
        } else {
            echo "<p class=\"centro\">No se ha podido borrar ese chat</p>";
        }
    }

    show_chats();
}

/*
*	Muestra un mensaje de tipo alert
*	E: $msg (mensaje que se quiere mostrar en alert)
*	S: nada
*/
function show_msg($msg)
{
    echo "<script type='text/javascript'>alert('" . $msg . "');</script>";
}

/*
*	Muestra un mensaje de tipo confirm
*	E: $msg (mensaje que se quiere mostrar en confirm)
*	S: nada
*/
function show_confirm($msg)
{
    echo "<script type='text/javascript'>confirm('" . $msg . "');</script>";
}


/*
* Muestra el chat del contacto con los mensajes y el estado del contacto
* E: nada
* S: nada
* SQL: select idMensaje, texto, fecha, hora, fichero, idChat, telefono from mensajes 
*/

function show_contacto_chat()
{
    echo '

           <section id="datosP">
              <section class="datosU">
            <img src="view/images/chica.jpg" class="imgRedonda"/>
            <h3>Fulanito: Trabajando</h3><br><br><br>

			<section class="mensajeU">
			  <h4>Fulanito  19/05/20119  10:35</h4>
			  <p>En casa</p>
			  <div><div>
			</section>

			<section class="mensajeU">
			  <h4>Fulanito  19/05/20119  10:30</h4>
			  <p>Mira a coco</p>
			  <img src="view/images/perro.jpg" width=100 height=100 />
			  <div><div>
			</section>

			<section class="mensajeU">
			  <h4>Menganito  19/05/20119  10:28</h4>
			  <p>Voy a salir</p>
			  <div><div>
			</section>

			<section class="mensajeU">
			  <h4>Menganito  19/05/20119  10:20</h4>
			  <p>Estoy esperando a mi madre</p>
			  <div><div>
			</section>
			
			<br><br>
			
		  </section>

		  <section class="contestar_mensaje">
			<form id="vb" action="index.php" method="post" role="form">

				<textarea id="ta" placeholder="Mensaje" rows="5" cols="40" required="" style="resize: none;" >
				</textarea>
				
				<br>
				<br>

				<span>
				  Elegir archivo<input type="file" name="b1" multiple>
				</span>
	   
				<button type="submit" name="contestar" >Contestar</button><br><br>

			</form>

			<form id="vb" action="index.php" method="post" role="form">

				<h5>Realiza un backup de este chat y asignale un nombre al fichero</h5>

				<input id="nombre" type="text" name="nombre" placeholder="nombre del fichero" required="" ><br><br>
	   
				<button type="submit" name="backup" >Backup</button><br><br>

			</form>

		  </section>
		 </section> 

		

		';

}

/*
*	Muestra la página modificar el perfil
*	E:
*	S:
*	SQL:
*/
function show_perfil()
{
    global $config;
    $long_texto = $config["LONG_TEXTO"];
    echo '

		<section id="perfil">

	<form id="vb" action="index.php" method="post" role="form">

			  <span>
			Cambiar imagen de perfil<input type="file" name="b1" multiple>
	  </span><br><br>

	  <textarea id="ta" rows="5" cols="40" required="" maxlength="' . $long_texto . '" style="resize: none;">I am working</textarea><br><br>
	   
	  <button type="submit" name="editar" >Editar</button>

	</form>

  </section>	

		';
}

/*
* Muestra los ajustes para cambiar el color del fondo de la pagina web
* E:
* S
* SQL:
*/
function show_ajustes()
{

    echo '

  <section id="ajustes">

	<form id="vb" action="index.php" method="post" role="form">

	  <h4>Selecciona un color de fondo
	  <select name="order" method="GET">
				<option value="entry_select_todo">Rojo</option>
				<option value="entry_select_pavo">Verde</option>
				<option value="entry_select_memo">Azul</option>
				<option value="entry_select_memo">Blanco</option>
				<option value="entry_select_memo">Rosa</option>
	  </select></h4>

	  <button type="submit" name="guardar_color" >Guardar</button>

	</form>

  </section>

';
}

function show_register()
{
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
        </section>
    ';
}
