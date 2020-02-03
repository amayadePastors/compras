<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>LOGIN</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">LOGIN</div>
<div class="card-body">
	<label for="nombre">Nombre de usuario:</label>
	<input type='text' name='nombre' value='' size=9><br/>
	<br/>
	<label for="password">Password:</label>
	<input type='text' name='password' value='' size=40><br/>
	<br/>
</div>
</BR>
<?php
	echo '<div><input type="submit" value="Acceder al Portal"></div>
	</form>';
	echo "<br/><a href='registro.php'>No tienes cuenta? Registrate</a><br/>";	
} else { 
	// Aquí va el código al pulsar submit
	$nombre=strtoupper(limpiarCampo($_POST['nombre']));
	$password=strtoupper(limpiarCampo($_POST['password']));
	$nif=comprobarPassword($nombre,$password,$db);
	mysqli_close($db);	
	if($nif !=""){
		session_start();
		$_SESSION["user"] =$nombre;
		$_SESSION["nif"] =$nif;
		$_SESSION["carrito"]=array();
		header("Location: menucliente.php");
	}else
		trigger_error ("El usuario y/o la contraseña no son correctos");
}

// Funciones utilizadas en el programa

function limpiarCampo($campoformulario) {
  $campoformulario = trim($campoformulario); 
  $campoformulario = stripslashes($campoformulario); 
  $campoformulario = htmlspecialchars($campoformulario);  

  return $campoformulario;   
}

function errores ($error_level,$error_message){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
}

function comprobarPassword($nombre,$password,$db){
	$nif="";
	$sql = "SELECT nif FROM cliente WHERE nombre = '$nombre' and password='$password'";
	$resultado = mysqli_query($db, $sql);
	if($resultado){
		while ($row = mysqli_fetch_assoc($resultado))
			$nif= $row['nif'];			
	}
	else{
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}
	return $nif;
}

?>



</body>

</html>