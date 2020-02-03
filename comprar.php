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
<h1>COMPRAR PRODUCTOS - Amaya de Pastors</h1>
<a href="logout.php">Cerrar Sesión</a><br/>
<a href="menucliente.php">Volver al menu principal</a><br/>
<?php
include "conexion.php";
set_error_handler("errores");
foreach($_SESSION["carrito"] as $clave=>$valor){
	if(!compobarUnidadesSuficientes($clave,$valor,$db))
		echo "Error: No hay unidades suficientes del producto ".$clave;
	else
		comprarProducto($_SESSION["nif"],$clave,$valor,$db);
}

mysqli_close($db);

function errores ($error_level,$error_message){
  echo "Codigo error: </b> $error_level  - <b> Mensaje: $error_message </b><br>";
  die();  
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
