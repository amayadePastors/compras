<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>ALTA ALMACENES - Amaya de Pastors</h1>
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
<div class="card-header">Datos Almacén</div>
<div class="card-body">
		<div class="form-group">
        LOCALIDAD <input type="text" name="localidad" placeholder="localidad" class="form-control">
        </div>
		</BR>
<?php
	echo '<div><input type="submit" value="Alta Categoría"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit
	$localidad= strtoupper(limpiarCampo($_POST['localidad']));
	$numeroalmacen=calcularNumAlmacen($db);
	
	insertarAlmacen($numeroalmacen,$localidad,$db);
	
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

function errores ($error_level,$error_message)
{
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
}

function calcularNumAlmacen($db){
	$numeroalmacen = null;
	$sql = "SELECT max(num_almacen) as MAXIMO FROM almacen";
	$max = mysqli_query($db, $sql);
	if ($max) {
		if (mysqli_num_rows($max) == null) {
			$numeroalmacen=10;
		}else if (mysqli_num_rows($max) == 1) {
			$row =mysqli_fetch_assoc($max);
			$numeroalmacen=$row["MAXIMO"]+10;
		} else {
			trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
			
		}
		return $numeroalmacen;
		
	} else {
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}
}

function insertarAlmacen($numeroalmacen,$localidad,$db){
	$sql = "INSERT INTO almacen (num_almacen, localidad) VALUES ('$numeroalmacen', '$localidad')";
	if (mysqli_query($db, $sql)) {
		echo "Almacen dado de alta.";
	} else {
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}
}


?>

</body>

</html>
