<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>FORMULARIO REGISTRO NUEVO CLIENTE</h1>
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
<div class="card-header">Alta cliente</div>
<div class="card-body">
	<label for="nif">NIF:</label>
	<input type='text' name='nif' value='' size=9><br/>
	<br/>
	<label for="nombre">NOMBRE:</label>
	<input type='text' name='nombre' value='' size=40><br/>
	<br/>
	<label for="apellido">APELLIDO:</label>
	<input type='text' name='apellido' value='' size=40><br/>
	<br/>
	<label for="cp">CODIGO POSTAL:</label>
	<input type='text' name='cp' value='' size=5><br/>
	<br/>
	<label for="direccion">DIRECCION:</label>
	<input type='text' name='direccion' value='' size=40><br/>
	<br/>
	<label for="ciudad">CIUDAD:</label>
	<input type='text' name='ciudad' value='' size=40><br/>
	<br/>
</div>
</BR>
<?php
	echo '<div><input type="submit" value="Alta Cliente"></div>
	</form>';
	echo "<br/><a href='login.php'>Acceder a la ventana de login</a><br/>";	
} else { 
	// Aqu� va el c�digo al pulsar submit
    if(empty($_POST['nif'])){//El nif no puede estar vac�o
		trigger_error("El campo nif es obligatorio");
	}else{
		$nif=strtoupper(limpiarCampo($_POST['nif']));
		if(!preg_match("/^[0-9]{8}[A-Z]$/",$nif)){
			trigger_error("El campo nif debe componerse de 8 digitos mas una letra");
		}else if(existeNif($nif,$db)){//El nif no puede repetirse
			trigger_error("Ya existe un usuario con ese nif");
		}else{
			$nombre= strtoupper(limpiarCampo($_POST['nombre']));
			$apellido= strtoupper((limpiarCampo($_POST['apellido'])));
			$cp= limpiarCampo($_POST['cp']);
			if(!preg_match("/^(0?[1-9][0-9]{3})$|^([1-4][0-9]{4})$|^(5[0-2][0-9]{3})$/i",$cp)){
				trigger_error("El codigo postal no es valido");
			}
			else{
			$direccion= strtoupper(limpiarCampo($_POST['direccion']));
			$ciudad= strtoupper(limpiarCampo($_POST['ciudad']));
			$password=generarClave($apellido);
			
			$escorrecto=insertarCliente($nif,$nombre,$apellido,$cp,$direccion,$ciudad,$password,$db);
			}
		}
	}
	mysqli_close($db);	
}
?>

<?php
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

function existeNif($nif,$db){
	$existe=false;
	$sql = "SELECT nif FROM cliente WHERE nif = '$nif'";
	$resultado = mysqli_query($db, $sql);
	if($resultado){
		if (mysqli_num_rows($resultado)>0) 
				$existe = true;
	}
	else{
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}
	return $existe;
}

function generarClave($apellido){
	$apellidosinespacios=str_replace(" ","",strtolower($apellido));
	return strrev($apellidosinespacios);	
}

function insertarCliente($nif,$nombre,$apellido,$cp,$direccion,$ciudad,$password,$db){
	$escorrecto=false;
	$sql = "INSERT INTO cliente (nif, nombre, apellido, cp, direccion, ciudad,password) VALUES ('$nif','$nombre','$apellido','$cp','$direccion','$ciudad','$password')";
	if (mysqli_query($db, $sql)) {
		echo "Cliente dado de alta.</br>Usuario: $nombre </br>Password: $password </br>";
		echo "<br/><a href='login.php'>Acceder al Login</a><br/>";	
		$escorrecto=true;
	}else 
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	return $escorrecto;
		
}

?>



</body>

</html>