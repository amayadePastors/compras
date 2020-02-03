<?php
session_start();

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>COMPRAR PRODUCTOS - <?php echo $_SESSION["user"]?></h1>
<?php
include "conexion.php";
set_error_handler("errores");

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
	echo '<div><input type="submit" value="Seleccionar Producto"></div></form>';
	echo '<a href="logout.php">Cerrar Sesión</a>';
	
// Aquí va el código al pulsar submit
if (isset($_POST) && !empty($_POST)) { 
	$producto=$_POST['producto'];
	$unidades= (integer)$_POST['unidades'];

if($unidades < 0){
	trigger_error("Error: No se puede comprar un numero negativo de unidades");
}else{
	$codigoProducto=obtenerCodigoProducto($producto,$db);
	if(!compobarUnidadesSuficientes($codigoProducto,$unidades,$db))
		trigger_error("Error: No hay unidades suficientes de producto");
	else
			annadirAlCarrito($codigoProducto,$unidades,$db);
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

function errores ($error_level,$error_message,$error_file,$error_line){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message. $error_file.$error_line </b><br>";
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

function annadirAlCarrito($codigoProducto,$unidades,$db){
	 if(!array_key_exists($codigoProducto,$_SESSION["carrito"]))
		 $_SESSION["carrito"][$codigoProducto]=$unidades;
	 else{
		foreach($_SESSION["carrito"] as $clave=>$valor){
			if($clave==$codigoProducto){
				$valor+=$unidades;
			if(!compobarUnidadesSuficientes($clave,$valor,$db))
				echo "<h3>No hay unidades suficientes</h3>";
			else
				$_SESSION["carrito"][$clave]=$valor;
				
			}
		
		}
	
	}
	
	mostrarCarrito();
	
}
	
function mostrarCarrito() {
	echo '<form action="comprar.php" method="post">';
	echo "<br/><table border=1><tr><th>PRODUCTO</th><th>UNIDADES</th><th>PVP</th><th>PRECIO TOTAL</th></tr>";
	foreach($_SESSION["carrito"] as $clave=>$valor){
		echo "<tr>";
		echo "<td>".$clave."</td>";
		echo "<td>".$valor."</td>";
		echo "<td> PRECIO </td>";
		echo "<td> PRECIO TOTAL</td>";
		echo "</tr>";
	}

	echo '<div><input type="submit" value="Confirmar Compra"></div>
		</form>';
	
}

?>

</body>

</html>
