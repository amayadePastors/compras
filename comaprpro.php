<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>APROVISIONAR PRODUCTOS - Amaya de Pastors</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

	/*Función que obtiene los productos y los almacenes*/
	$productos = obtenerProductos($db);
	$almacenes = obtenerAlmacenes($db);
	
    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">Datos Producto</div>
<div class="card-body">
	<div class="form-group">
		<label for="producto">Producto:</label>
		<select name="producto">
			<?php foreach($productos as $producto) : ?>
				<option> <?php echo $producto ?> </option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-group">
		<label for="almacen">Almacen:</label>
		<select name="almacen">
			<?php foreach($almacenes as $almacen) : ?>
				<option> <?php echo $almacen ?> </option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-group">
        CANTIDAD PRODUCTO 
		<input type="number" name="cantidad" placeholder="cantidad" class="form-control">
    </div>
	</BR>
<?php
	echo '<div><input type="submit" value="Alta Producto"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit	
    $almacen=$_POST['almacen'];
	$producto=$_POST['producto'];
	$cantidad= (integer)$_POST['cantidad'];
	if($cantidad < 0){
		trigger_error("Error: No se puede aprovisionar un numero negativo");
	}else{		
		$codigoProducto=obtenerCodigoProducto($producto,$db);
		aprovisionarProducto($almacen,$codigoProducto,$cantidad,$db);
	}

	
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

function aprovisionarProducto($almacen,$codigoProducto,$cantidad,$db){
	
	$sql1 = "select * from almacena where num_almacen='$almacen' and id_producto='$codigoProducto'";
	$sql2;
	$resultado = mysqli_query($db, $sql1);
	if ($resultado) {
		if (mysqli_num_rows($resultado) > 0)
			$sql2 = "update almacena set cantidad=cantidad+'$cantidad' where num_almacen='$almacen' and id_producto='$codigoProducto'";	
		else
			$sql2 = "INSERT INTO almacena (num_almacen,id_producto,cantidad) VALUES ('$almacen','$codigoProducto','$cantidad')";
		
		if (mysqli_query($db, $sql2)) 
			echo "Producto aprovisionado. <br/>";
		else 
			trigger_error("Error: " . $sql2 . "<br/>" . mysqli_error($db));	
		
	}else
		trigger_error("Error: " . $sql1 . "<br/>" . mysqli_error($db));	
}
	

?>



</body>

</html>
