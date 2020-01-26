<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>CONSULTA DE STOCK - Amaya de Pastors</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

	/*Función que obtiene los productos y los almacenes*/
	$productos = obtenerProductos($db);

    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">Productos</div>
<div class="card-body">
	<div class="form-group">
		<label for="producto">Seleccione un producto:</label>
		<select name="producto">
			<?php foreach($productos as $producto) : ?>
				<option> <?php echo $producto ?> </option>
			<?php endforeach; ?>
		</select>
	</div>
	</BR>
<?php
	echo '<div><input type="submit" value="Mostrar Stock"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit	
	$producto=$_POST['producto'];		
	$codigoProducto=obtenerCodigoProducto($producto,$db);
	mostrarStock($producto,$codigoProducto,$db);
	mysqli_close($db);
}
?>

<?php
// Funciones utilizadas en el programa

function obtenerProductos($db) {
	$productos = array();
	$sql = "SELECT nombre FROM producto";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			array_push($productos,$row['nombre']);
		}
	}
	return $productos;
}

function errores ($error_level,$error_message){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
}

function obtenerCodigoProducto($producto,$db){
	$codigo = null;
	$sql = "SELECT id_producto FROM producto WHERE nombre = '$producto'";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$codigo = $row['id_producto'];
		}
	}
	return $codigo;
	
}

function mostrarStock($producto,$codigoProducto,$db){
	$sql = "SELECT producto.nombre as NOMBRE,almacena.num_almacen as ALMACEN,almacena.cantidad as CANTIDAD FROM producto,almacena WHERE producto.id_producto=almacena.id_producto and producto.id_producto='$codigoProducto'";
	$resultado = mysqli_query($db, $sql);
	
	if ($resultado) {
		if (mysqli_num_rows($resultado) > 0){
			echo "PRODUCTO: $producto";
			echo"<table border=1><tr><th>ALMACEN</th><th>CANTIDAD</th></tr>";
			while ($row = mysqli_fetch_assoc($resultado)) {
				echo "<tr>";
				echo "<td>". $row['ALMACEN'] . "</td>";
				echo "<td>". $row['CANTIDAD'] . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}else
			echo "No hay stock de este producto";
	}else
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));	
}
	

?>



</body>

</html>
