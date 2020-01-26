<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>COMPRAR PRODUCTOS - Amaya de Pastors</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

	/*Función que obtiene los productos*/
	$productos = obtenerProductos($db);

    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-header">Datos Compra</div>
<div class="card-body">
	<div class="form-group">
        NIF cliente: 
		<input type="text" name="nif" placeholder="nif" class="form-control">
    </div>
	<div class="form-group">
		<label for="producto">Seleccionar Producto:</label>
		<select name="producto">
			<?php foreach($productos as $producto) : ?>
				<option> <?php echo $producto ?> </option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-group">
        Unidades a comprar: 
		<input type="number" name="unidades" placeholder="unidades" class="form-control">
    </div>
	</BR>
<?php
	echo '<div><input type="submit" value="Comprar Producto"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit	
	if(empty($_POST['nif'])){//El nif no puede estar vacío
		trigger_error("El campo nif es obligatorio");
	}else{
		$nif=strtoupper(limpiarCampo($_POST['nif']));
		if(!preg_match("/^[0-9]{8}[A-Z]$/",$nif)){
			trigger_error("El campo nif debe componerse de 8 digitos mas una letra");
		}else if(!existeNif($nif,$db)){//Comprobamos que el cliente esta dado de alta en la BD
			trigger_error("No existe un usuario con ese nif en la base de datos");
		}else{
			$producto=$_POST['producto'];
			$unidades= (integer)$_POST['unidades'];
			if($unidades < 0){
				trigger_error("Error: No se puede comprar un numero negativo de unidades");
			}else{
				$codigoProducto=obtenerCodigoProducto($producto,$db);
				if(!compobarUnidadesSuficientes($codigoProducto,$unidades,$db))
					trigger_error("Error: No hay unidades suficientes de producto");
				else
					comprarProducto($nif,$codigoProducto,$unidades,$db);
			}
		}
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

function compobarUnidadesSuficientes($codigoProducto,$unidades,$db){
	$haySuficientes=true;
	$sql = "SELECT sum(cantidad) as CANTIDAD FROM almacena WHERE id_producto = '$codigoProducto'";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$cantidad = $row['CANTIDAD'];
			if($cantidad<$unidades)
				$haySuficientes=false;
		}
	return $haySuficientes;	
	}else
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));	
}

function comprarProducto($nif,$codigoProducto,$unidades,$db){
	$unidadesaux=$unidades;
	$sql1 = "SELECT cantidad,num_almacen FROM almacena WHERE id_producto = '$codigoProducto'";
	$resultado = mysqli_query($db, $sql1);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			if($unidadesaux>0){
				$cantidad = $row['cantidad'];
				$numalmacen = $row['num_almacen'];
				if($cantidad > $unidadesaux){
					$sql2 = "update almacena set cantidad=cantidad-$unidadesaux WHERE id_producto = '$codigoProducto' and num_almacen='$numalmacen'";
					if (mysqli_query($db, $sql2)) {
						$unidadesaux=0;
					}else 
						trigger_error("Error: " . $sql2 . "<br/>" . mysqli_error($db));
				}else{
					$sql3 = "delete from almacena WHERE id_producto = '$codigoProducto' and num_almacen='$numalmacen'";
					if (mysqli_query($db, $sql3)) {
						$unidadesaux-=$cantidad;
					}else 
						trigger_error("Error: " . $sql3 . "<br/>" . mysqli_error($db));
				}
			}
		}
		
		$sql4 = "INSERT INTO compra (nif, id_producto, fecha_compra, unidades) VALUES ('$nif','$codigoProducto',sysdate(),'$unidades')";
		if (mysqli_query($db, $sql4)) 
			echo "Compra realizada.";
		else 
			trigger_error("Error: " . $sql4 . "<br/>" . mysqli_error($db));	
	}else
		trigger_error("Error: " . $sql1 . "<br/>" . mysqli_error($db));
}
	

?>



</body>

</html>
