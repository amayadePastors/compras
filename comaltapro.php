<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>ALTA PRODUCTOS - Nombre del alumno</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 
	/*Función que obtiene las categorias*/
	$categorias = obtenerCategorias($db);

	
    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">Datos Producto</div>
<div class="card-body">
		<div class="form-group">
        ID PRODUCTO <input type="text" name="idproducto" placeholder="idproducto" class="form-control">
        </div>
		<div class="form-group">
        NOMBRE PRODUCTO <input type="text" name="nombre" placeholder="nombre" class="form-control">
        </div>
		<div class="form-group">
        PRECIO PRODUCTO <input type="text" name="precio" placeholder="precio" class="form-control">
        </div>
	<div class="form-group">
	<label for="categoria">Categorías:</label>
	<select name="categoria">
		<?php foreach($categorias as $categoria) : ?>
			<option> <?php echo $categoria ?> </option>
		<?php endforeach; ?>
	</select>
	</div>
	</BR>
<?php
	echo '<div><input type="submit" value="Alta Producto"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit
	
    $idproducto= limpiarCampo($_POST['idproducto']);
	$nombre= limpiarCampo($_POST['nombre']);
	$precio= (double)limpiarCampo($_POST['precio']);
	$categoria= $_POST['categoria'];
	
	$idCategoria=obtenerIdCategoria($categoria,$db);
	
	insertarProducto($idproducto,$nombre,$precio,$idCategoria,$db);
	
	mysqli_close($db);
	
}
?>

<?php
// Funciones utilizadas en el programa

function obtenerCategorias($db) {
	$categorias = array();
	$sql = "SELECT nombre FROM categoria";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$categorias[] = $row['nombre'];
		}
	}
	return $categorias;
}

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

function obtenerIdCategoria($categoria,$db) {
	$idCategoria = null;
	$sql = "SELECT ID_CATEGORIA FROM categoria WHERE nombre = '$categoria'";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$idCategoria = $row['ID_CATEGORIA'];
		}
	}
	return $idCategoria;

}

function insertarProducto($idproducto,$nombre,$precio,$idCategoria,$db){

	$sql = "INSERT INTO producto (ID_PRODUCTO,NOMBRE,PRECIO,ID_CATEGORIA) VALUES ('$idproducto','$nombre',$precio,'$idCategoria')";

	if (mysqli_query($db, $sql)) {
		echo "Producto dado de alta. <br/>";
	} else {
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));
	}

}
	

?>



</body>

</html>