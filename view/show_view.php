<?php

/*
*	Muestra el menú
*	E:
*	S:
*	SQL: select logo, texto from usuario
*/
function show_menu()
{

    if (isset($_SESSION['user'])) {

        echo '<header>

			<section id="estado">
			  <img src="view/images/f.jpg" class="imgRedonda"/><br>
			  <p>I am working</p>
			</section>

			<nav class="menu">

			  <ul>
				<li><a href="index.php?cmd=chat" class="btn">CHAT</a></li>
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
*	Muestra el formulario de contacto
*	E:
*	S:
*	SQL:
*/
function show_loging()
{

    echo '


		<section id="slider">
			<form action="index.php" method="post" role="form">

					<h2>LOG IN</h2>

					<input id="numero" type="text" name="numero" placeholder="número de telefono" required="" ><br><br>
   
					<input id="pass" type="password" name="pass_user" placeholder="password" required="" ><br><br>
	   
					<button type="submit" name="login">Login</button><br><br>

			</form>
		</section>';
}

/*
* Muestra los diferentes tipos de chat
* E:
* S:
* SQL: select idChat, telefono from TIENE where numero =  $_SESSION['user'];
*/
function show_chats()
{
    echo '

	<section id="chats">
	  <h3><a href="index.php?cmd=ver_chat" class="btn">Fulanito
	  <img src="view/images/verde.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Menganito
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Mariano
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Sefora
	  <img src="view/images/verde.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Romero
	  <img src="view/images/verde.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Goku
	  <img src="view/images/verde.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>

	  <h3><a href="index.php?cmd=ver_chat" class="btn">Vegeta
	  <img src="view/images/rojo.png" width=10 height=10 /></a></h3><br>
	  <div></div><br><br>
	</section>


';

}

/*
*	Muestra un mensaje de tipo alert
*	E: $msg (mensaje que se quiere mostrar en alert)
*	S:
*	SQL:
*/
function show_msg($msg)
{
    echo "<script type='text/javascript'>alert('" . $msg . "');</script>";
}


/*
* Muestra el chat del contacto con los mensajes y el estado del contacto
* E:
* S:
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


?>
