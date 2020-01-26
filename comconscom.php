<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>CONSULTA DE COMPRAS - Amaya de Pastors</h1>
<?php
include "conexion.php";
set_error_handler("errores");

/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 

	/*Función que obtiene los DNIs*/
	$nifs = obtenerNIFs($db);

    /* Se inicializa la lista valores*/
	echo '<form action="" method="post">';
?>
<div class="container ">
<!--Aplicacion-->
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-body">
	<div class="form-group">
		<label for="nif">Seleccionar NIF:</label>
		<select name="nif">
			<?php foreach($nifs as $nif) : ?>
				<option> <?php echo $nif ?> </option>
			<?php endforeach; ?>
		</select><br>
		<label for="fechaini">Seleccionar Fecha de inicio:</label>
		<input type="date" name="fechaini"><br>
		<label for="fechafin">Seleccionar Fecha de fin:</label>
		<input type="date" name="fechafin">
	</div>
	</BR>
<?php
	echo '<div><input type="submit" value="Mostrar Histórico Compras"></div>
	</form>';
} else { 

	// Aquí va el código al pulsar submit	
	$nif=$_POST['nif'];	
	$fechaini=$_POST['fechaini'];	
	$fechafin=$_POST['fechafin'];
	if($fechaini>$fechafin)
		trigger_error("La feha de fin no puede ser inferior a la de inicio");
	else
		mostrarCompras($nif,$fechaini,$fechafin,$db);
	
	mysqli_close($db);
}
?>

<?php
// Funciones utilizadas en el programa
function obtenerNIFs($db) {
	$nifs = array();
	$sql = "SELECT nif FROM cliente";
	$resultado = mysqli_query($db, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			array_push($nifs,$row['nif']);
		}
	}
	return $nifs;
}
function errores ($error_level,$error_message){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
}

function mostrarCompras($nif,$fechaini,$fechafin,$db){
	$sql = "SELECT producto.id_producto as ID, producto.nombre as NOMBRE,producto.precio as PRECIO,compra.unidades as UNIDADES, compra.fecha_compra as FECHA FROM producto,compra WHERE producto.id_producto=compra.id_producto and compra.nif='$nif' and date_format(compra.fecha_compra,'%Y-%m-%d') >='$fechaini' && date_format(compra.fecha_compra,'%Y-%m-%d') <='$fechafin'";
	$resultado = mysqli_query($db, $sql);
	
	if ($resultado) {
		if (mysqli_num_rows($resultado) > 0){
			echo"<table border=1><tr><th>ID PRODUCTO</th><th>NOMBRE</th><th>PRECIO</th><th>UNIDADES COMPRADAS</th><th>FECHA COMPRA</th></tr>";
			while ($row = mysqli_fetch_assoc($resultado)) {
				echo "<tr>";
				echo "<td>". $row['ID'] . "</td>";
				echo "<td>". $row['NOMBRE'] . "</td>";
				echo "<td>". $row['PRECIO'] . "</td>";
				echo "<td>". $row['UNIDADES'] . "</td>";
				echo "<td>". $row['FECHA'] . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}else
			echo "No hay compras de este cliente en estas fechas.";
	}else
		trigger_error("Error: " . $sql . "<br/>" . mysqli_error($db));	
}
	

?>



</body>

</html>
