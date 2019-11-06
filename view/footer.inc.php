<?php

/*
*	Muestra el footer
*	E:
*	S:
*	SQL:
*/
function show_footer(){
	global $config;
	$email = $config["EMAIL_ADMIN"];
	echo '<footer>
			<p>
				(c) Todos los derechos reservados - FCL 2019 <br>
				Diseñado por <a href="mailto:' . $email . '">mi</a>
			</p>
		</footer>
		</div>

	</body>
	</html>';
}

?>
