<!DOCTYPE html>
<html>
	<meta charset="UTF-8">
    </meta>
    <head>
        <title>SUBASTAS</title>
        <link rel="stylesheet" href="css/estilos.css" type="text/css" media="all" />
    </head>

    <body>

<?php
    
    include("escribirLog.php");

	$idSubasta = $_GET['id'];

	$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");
	if(session_id() == '') {
		session_start();
	}
	$selectSubastas = "SELECT tipo, idsubastador, fechainicio, fechacierre FROM subastas WHERE id='$idSubasta'";
	$resultSubastas = $conn->query($selectSubastas);
	$tipoSubasta; $tipoSubastaString; $producto; $subastador; $fechaInicio; $fechaCierre;
	
	foreach (array_keys($_SESSION['user']) as $field)
	{
			
	}
	
	include("listaSubastas.php");
	
	if($resultSubastas->num_rows > 0){
		
		while($row = $resultSubastas->fetch_assoc()) {
			
			$tipoSubasta = $row['tipo'];
			$tipoSubastaString = pasarTipoSubastaAString($tipoSubasta);
			
			$fechaInicio = $row['fechainicio'];
			$fechaCierre = $row['fechacierre'];
			
			$idSubastador = $row['idsubastador'];
			$selectSubastador = "SELECT nombre, apellidos FROM usuarios WHERE id='$idSubastador'";
			$resultSubastador = $conn->query($selectSubastador);
			
			if($resultSubastador->num_rows > 0){
		
				while($rowSubastador = $resultSubastador->fetch_assoc()) {
				
					$nombre = $rowSubastador['nombre'];
					$apellidos = $rowSubastador['apellidos'];

				}	
			}
						
			
			?>

			<div id="header">		
				<button class="buttonVolver" onclick="location.href='<?php echo $field; ?>.php'">Volver</button>
    			<h2 style="font-size: 30px; font-style: italic;"> <?php echo $tipoSubastaString; ?> </h2>
	    	</div>

	    		<table style="width:100%; padding: 10px; padding-left: 15px; margin-top: 130px; font-family:'Segoe UI'; font-weight: bold;">
	                <tr>
	                	<td style="width: 135px; text-align: center;">ID SUBASTA</td>
	                    <td style="width: 135px; text-align: center;">SUBASTADOR</td>
	                    <td style="width: 130px; text-align: center;">LOTE/PRODUCTO</td>
	                    <td style="width: 150px; text-align: center;">DESCRIPCIÓN</td>
	                </tr>
	            </table>


	            <table style="width:100%; padding: 10px; padding-left: 15px; margin-top: 0px; margin-bottom: 10px; font-family:'Segoe UI';">

	            <td style="width: 135px; text-align: center;"> <?php echo $idSubasta; ?> </td>
					<td style="width: 135px; text-align: center;"> <?php echo $nombre." ".$apellidos; ?> </td>

					<?php

					$selectProducto = "SELECT nombre, descripcion FROM productos WHERE idsubasta='$idSubasta'";
					$resultProducto = $conn->query($selectProducto);
					$selectLote = "SELECT nombre, descripcion FROM lotes WHERE idsubasta='$idSubasta'";
					$resultLote = $conn->query($selectLote);
					
					if($resultProducto->num_rows > 0){
				
						while($rowProducto= $resultProducto->fetch_assoc()) {
						
							$nombreProducto = $rowProducto['nombre'];
							$descripcionProducto = $rowProducto['descripcion'];
							
							?>
								<td style="width: 130px; text-align: center;"> <?php echo $nombreProducto; ?> </td>
								<td style="width: 150px; text-align: center;"> <?php echo $descripcionProducto; ?> </td>
							<?php
						}	
					}

					else if ($resultLote->num_rows > 0){
				
						while($rowLote= $resultLote->fetch_assoc()) {
						
							$nombreLote = $rowLote['nombre'];
							$descripcionLote = $rowLote['descripcion'];
							?>
								<td style="width: 130px; text-align: center;"> <?php echo $nombreLote; ?> </td>
								<td style="width: 150px; text-align: center;"> <?php echo $descripcionLote; ?> </td>
							<?php
						}	
					}


					?>

				</table>


				<table style="width:100%; padding: 10px; padding-left: 15px; margin-left: 15px; margin-top: 20px; font-family:'Segoe UI'; font-weight: bold;">
	                <tr>
	                    <td style="width: 100px; text-align: center;">FECHA INICIO</td>
	                    <td style="width: 100px; text-align: center;">SEGUNDA FECHA</td>
	                    <td style="width: 135px; text-align: center;">FECHA CIERRE</td>
	                </tr>
				</table>

			<script type="text/javascript">
			
			var anterior = "";
			function compararDate(){
				var xhttp = new XMLHttpRequest();
					console.log(xhttp.status);
					xhttp.onreadystatechange = function () {
						if ((xhttp.readyState == 4) && (xhttp.status == 200)) {
							
							if(anterior!=xhttp.responseText){
								document.getElementById("contenido").innerHTML = xhttp.responseText;
							}
							
							anterior = xhttp.responseText;
							
						}
					};
					xhttp.open("GET", "gestionarRoundRobin.php?id=<?php echo $idSubasta; ?>", true);
					xhttp.send();
			}
			
			setInterval(function () {
                compararDate();
            }, 500);
			
			</script>
			
			<?php 
			
			//visualizarPujas($idSubasta, $tipoUsuario, $momento);
			
			
		}		
	}
	

	if(isset($_POST['puja'])){
			$fecha = date("Y-m-d H:i:s");
			$cantidad = $_POST['puja'];
			
			
				$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");	
									
					$date = date('Y-m-d H:i:s');
					$select = "INSERT INTO pujas (fecha, cantidad, idsubasta, idpostor) VALUES ('$date', '$cantidad', '$idSubasta', '".$_SESSION['user']['postor']."')";
					if ($conn->query($select) === TRUE) {
						?>
						<script type="text/javascript">
							alert('Usuario Puja Correcta');
						</script>
						<?php
                        
                        
                        //escribir en el log
                    
                        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='".$_SESSION['user']['postor']."'");
                        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
                        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
                        $nombreUsuario = $rowNombreUsuario['usuario'];
        
                        $queryIdpuja= "SELECT id FROM pujas WHERE idsubasta='$idSubasta' AND idpostor = '".$_SESSION['user']['postor']."'";
                        $resultidpuja = $conn->query( $queryIdpuja);
                        $rowIdpuja = $resultidpuja->fetch_assoc();
                        $idpuja = $rowIdpuja['id'];
        
                        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
                        $resultNombreProd = $conn->query( $queryBuscarProd);
                        if($resultNombreProd->num_rows > 0){
                            $rowNombreProd = $resultNombreProd->fetch_assoc();
                            $idprod = $rowNombreProd['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, $idprod, "NULL", "NULL");
                        }else{
                            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
                            $resultNombreLote = $conn->query( $queryBuscarLote);
                            $rowNombreLote = $resultNombreLote->fetch_assoc();
                            $idlote = $rowNombreLote['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, "NULL", $idlote, "NULL");
                        }
                        //fin de escribir en el log
    
					}
			
	}
	
	if(isset($_POST['pujaSegunda'])){
			$fecha = date("Y-m-d H:i:s");
			$cantidad = $_POST['pujaSegunda'];
			include("valorMinimo.php");
			if($tipoSubasta==11){
				//COMO ES ASCENDENTE TENEMOS QUE COMPROBAR QUE ES MAYOR QUE LA PUJA ACTUAL MAS ALTA
				if($cantidad>cantidadSegundaPuja($idSubasta)){
					$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");	
									
					$date = date('Y-m-d H:i:s');
					$select = "INSERT INTO pujas (fecha, cantidad, idsubasta, idpostor) VALUES ('$date', '$cantidad', '$idSubasta', '".$_SESSION['user']['postor']."')";
					if ($conn->query($select) === TRUE) {
						?>
						<script type="text/javascript">
							alert('Usuario Puja Correcta');
						</script>
						<?php

                        
                        //escribir en el log
                    
                        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='".$_SESSION['user']['postor']."'");
                        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
                        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
                        $nombreUsuario = $rowNombreUsuario['usuario'];
        
                        $queryIdpuja= "SELECT id FROM pujas WHERE idsubasta='$idSubasta' AND idpostor = '".$_SESSION['user']['postor']."'";
                        $resultidpuja = $conn->query( $queryIdpuja);
                        $rowIdpuja = $resultidpuja->fetch_assoc();
                        $idpuja = $rowIdpuja['id'];
        
                        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
                        $resultNombreProd = $conn->query( $queryBuscarProd);
                        if($resultNombreProd->num_rows > 0){
                            $rowNombreProd = $resultNombreProd->fetch_assoc();
                            $idprod = $rowNombreProd['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, $idprod, "NULL", "NULL");
                        }else{
                            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
                            $resultNombreLote = $conn->query( $queryBuscarLote);
                            $rowNombreLote = $resultNombreLote->fetch_assoc();
                            $idlote = $rowNombreLote['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, "NULL", $idlote, "NULL");
                        }
                        //fin de escribir en el log
                 
					} else {
					   // echo "Error updating record: " . $conn->error;
					}
				}else{
					?>
						<script type="text/javascript">
							alert('La puja tiene un valor incorrecto!');
						</script>
					<?php
                    
                        $conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");	
                        //escribir en el log
                    
                        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='".$_SESSION['user']['postor']."'");
                        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
                        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
                        $nombreUsuario = $rowNombreUsuario['usuario'];
        
                        $queryIdpuja= "SELECT id FROM pujas WHERE idsubasta='$idSubasta' AND idpostor = '".$_SESSION['user']['postor']."'";
                        $resultidpuja = $conn->query( $queryIdpuja);
                        $rowIdpuja = $resultidpuja->fetch_assoc();
                        $idpuja = $rowIdpuja['id'];
        
                        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
                        $resultNombreProd = $conn->query( $queryBuscarProd);
                        if($resultNombreProd->num_rows > 0){
                            $rowNombreProd = $resultNombreProd->fetch_assoc();
                            $idprod = $rowNombreProd['id'];
                            escribirLog("Puja inválida realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, $idprod, "NULL", "NULL");
                        }else{
                            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
                            $resultNombreLote = $conn->query( $queryBuscarLote);
                            $rowNombreLote = $resultNombreLote->fetch_assoc();
                            $idlote = $rowNombreLote['id'];
                            escribirLog("Puja inválida realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, "NULL", $idlote, "NULL");
                        }
                        //fin de escribir en el log
                    
                    
					echo "";

				}
			}else if($tipoSubasta==12){
				//COMO ES DESCENDENTE TENEMOS QUE COMPROBAR QUE ES MENOR QUE LA PUJA ACTUAL MAS BAJA
				if($cantidad<cantidadSegundaPuja($idSubasta)){
					$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");	
									
					$date = date('Y-m-d H:i:s');
					$select = "INSERT INTO pujas (fecha, cantidad, idsubasta, idpostor) VALUES ('$date', '$cantidad', '$idSubasta', '".$_SESSION['user']['postor']."')";
					if ($conn->query($select) === TRUE) {
						?>
						<script type="text/javascript">
							alert('Usuario Puja Correcta');
						</script>
						<?php
                        
                        
                        //escribir en el log
                    
                        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='".$_SESSION['user']['postor']."'");
                        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
                        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
                        $nombreUsuario = $rowNombreUsuario['usuario'];
        
                        $queryIdpuja= "SELECT id FROM pujas WHERE idsubasta='$idSubasta' AND idpostor = '".$_SESSION['user']['postor']."'";
                        $resultidpuja = $conn->query( $queryIdpuja);
                        $rowIdpuja = $resultidpuja->fetch_assoc();
                        $idpuja = $rowIdpuja['id'];
        
                        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
                        $resultNombreProd = $conn->query( $queryBuscarProd);
                        if($resultNombreProd->num_rows > 0){
                            $rowNombreProd = $resultNombreProd->fetch_assoc();
                            $idprod = $rowNombreProd['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, $idprod, "NULL", "NULL");
                        }else{
                            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
                            $resultNombreLote = $conn->query( $queryBuscarLote);
                            $rowNombreLote = $resultNombreLote->fetch_assoc();
                            $idlote = $rowNombreLote['id'];
                            escribirLog("Puja de ".$cantidad." € realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, "NULL", $idlote, "NULL");
                        }
                        //fin de escribir en el log
       
					} else {
					   // echo "Error updating record: " . $conn->error;
					}
				}else{
					?>
						<script type="text/javascript">
							alert('La puja tiene un valor incorrecto!');
						</script>
					<?php
                    
                    $conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");	
                        //escribir en el log
                    
                        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='".$_SESSION['user']['postor']."'");
                        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
                        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
                        $nombreUsuario = $rowNombreUsuario['usuario'];
        
                        $queryIdpuja= "SELECT id FROM pujas WHERE idsubasta='$idSubasta' AND idpostor = '".$_SESSION['user']['postor']."'";
                        $resultidpuja = $conn->query( $queryIdpuja);
                        $rowIdpuja = $resultidpuja->fetch_assoc();
                        $idpuja = $rowIdpuja['id'];
        
                        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
                        $resultNombreProd = $conn->query( $queryBuscarProd);
                        if($resultNombreProd->num_rows > 0){
                            $rowNombreProd = $resultNombreProd->fetch_assoc();
                            $idprod = $rowNombreProd['id'];
                            escribirLog("Puja inválida realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, $idprod, "NULL", "NULL");
                        }else{
                            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
                            $resultNombreLote = $conn->query( $queryBuscarLote);
                            $rowNombreLote = $resultNombreLote->fetch_assoc();
                            $idlote = $rowNombreLote['id'];
                            escribirLog("Puja inválida realizada por: \""."$nombreUsuario"."\".", $_SESSION['user']['postor'], $idSubasta, "NULL", $idlote, "NULL");
                        }
                        //fin de escribir en el log
                    
 
				}
			}
	}
	?>
	<div id="contenido" ></div>
	<?php
			
?>


	</body>
</html>