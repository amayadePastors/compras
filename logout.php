<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<body>
	<p>Sesion cerrada</p>
	<a href="index.html">Volver a la ventana principal</a>
</body>
</html>