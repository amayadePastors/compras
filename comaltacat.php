<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>ALTA CATEGORÍAS - Amaya de Pastors</h1>
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
<div class="card-header">Datos Categoría</div>
<div class="card-body">
		<div class="form-group">
        ID CATEGORIA <input type="text" name="idcategoria" placeholder="idcategoria" class="form-control">
        </div>
		<div class="form-group">
        NOMBRE CATEGORIA <input type="text" name="nombre" placeholder="nombre" class="form-control">
        </div>

		</BR>
<?php
	echo '<div><input type="submit" value="Alta Categoría"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit
	$idcategoria= limpiarCampo($_POST['idcategoria']);
	$nombre= limpiarCampo($_POST['nombre']);
	
	insertarCategoria($idcategoria,$nombre,$db);
	
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

function insertarCategoria($idcategoria,$nombre,$db){
	$sql = "INSERT INTO categoria (ID_CATEGORIA, NOMBRE) VALUES ('$idcategoria', '$nombre')";
	if (mysqli_query($db, $sql)) {
		echo "Categoria dada de alta.";
	} else {
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}
}


?>

</body>

</html>
