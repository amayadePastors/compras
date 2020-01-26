<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>CONSULTA DE ALMACENCES - Amaya de Pastors</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

	/*Función que obtiene los almacenes*/
	$almacenes = obtenerAlmacenes($db);

    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">Almacences</div>
<div class="card-body">
	<div class="form-group">
		<label for="almacen">Seleccionar Almacen:</label>
		<select name="almacen">
			<?php foreach($almacenes as $almacen) : ?>
				<option> <?php echo $almacen ?> </option>
			<?php endforeach; ?>
		</select>
	</div>
	</BR>
<?php
	echo '<div><input type="submit" value="Mostrar Stock"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit	
	$almacen=$_POST['almacen'];		
	mostrarProductosAlmacen($almacen,$db);
	mysqli_close($db);
}
?>

<?php
// Funciones utilizadas en el programa
function obtenerAlmacenes($db) {
	$almacenes = array();
	$sql = "SELECT num_almacen FROM almacen";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			array_push($almacenes,$row['num_almacen']);
		}
	}
	return $almacenes;
}
function errores ($error_level,$error_message){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
}

function mostrarProductosAlmacen($almacen,$db){
	$sql = "SELECT producto.id_producto as ID, producto.nombre as NOMBRE,producto.precio as PRECIO,almacena.cantidad as CANTIDAD FROM producto,almacena WHERE producto.id_producto=almacena.id_producto and almacena.num_almacen='$almacen' and almacena.cantidad>0";
	$resultado = mysqli_query($db, $sql);
	
	if ($resultado) {
		if (mysqli_num_rows($resultado) > 0){
			echo"<table border=1><tr><th>ID PRODUCTO</th><th>NOMBRE</th><th>PRECIO</th><th>CANTIDAD</th></tr>";
			while ($row = mysqli_fetch_assoc($resultado)) {
				echo "<tr>";
				echo "<td>". $row['ID'] . "</td>";
				echo "<td>". $row['NOMBRE'] . "</td>";
				echo "<td>". $row['PRECIO'] . "</td>";
				echo "<td>". $row['CANTIDAD'] . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}else
			echo "No hay productos en este almacén.";
	}else
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));	
}
	

?>



</body>

</html>
