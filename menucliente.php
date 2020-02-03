<?php
session_start();
?>
<HTML>
<HEAD> <TITLE> MENU CLIENTE</TITLE>
<?php
echo "Has iniciado sesión como " . $_SESSION["user"];
?>
</HEAD>
<BODY>
<h1>MENU CLIENTE</h1>
	<br/>
	<a href="compracliente.php">Compra de Productos</a><br/>
	<a href="mostrarcompras.php">Consulta de Compras</a><br/>
	<a href="logout.php">Cerrar Sesión</a>
</BODY>
</HTML>


