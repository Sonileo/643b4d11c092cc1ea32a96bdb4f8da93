<?php
		
	/*TIPOS DE SUBASTA
		Dinámica Descubierta ascendente - 1
		Dinámica Descubierta descendente - 2
		Dinámica anónima A - 3
		Dinámica anónima D - 4
		Dinámica holandesa A - 5
		Dinámica holandesa D - 6
		
		Sobre cerrado primer precio A - 7
		Sobre cerrado primer precio D - 8
		Sobre cerrado segundo precio A - 9
		Sobre cerrado segundo precio D -10
		
		Round Robin A - 11
		Round Robin D - 12
	*/
	
	function pasarTipoSubastaAString($tipoSubasta){
		
		switch($tipoSubasta){
			case 1:
			return "Din&aacutemica descubierta ascendente";
			break;
			case 2: "Din&aacutemica descubierta descendente";
			break;
			case 3:
			return "Din&aacutemica an&oacutenima ascendente";
			break;
			case 4:
			return "Din&aacutemica an&oacutenima descendente";
			break;
			case 5:
			return "Din&aacutemica holandesa ascendente";
			break;
			case 6:
			return "Din&aacutemica holandesa descendente";
			break;
			case 7:
			return "Sobre cerrado primer precio ascendente";
			break;
			case 8:
			return "Sobre cerrado primer precio descendente";
			break;
			case 9:
			return "Sobre cerrado segundo precio ascendente";
			break;
			case 10:
			return "Sobre cerrado segundo precio descendente";
			break;
			case 11:
			return "Round Robin ascendente";
			break;
			case 12:
			return "Robin Robin descendente";
			break;
			
		}
		
	}
	
	function crearTablaSubastas($tipoUsuario){
		
		$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");
		$selectSubastas;
		$resultSubastas;
		
		session_start();
		
		if($tipoUsuario = ""){
			$selectSubastas = "SELECT * FROM subastas ORDER BY fechacierre DESC";
			$resultSubastas = $conn->query($selectSubastas);
		}
		
		if($tipoUsuario = 'subastador'){
			$selectSubastas = "SELECT * FROM subastas WHERE idsubastador = '".$_SESSION['user']['subastador']."' ORDER BY fechacierre DESC";
			$resultSubastas = $conn->query($selectSubastas);
		}
		
			if($resultSubastas->num_rows > 0){//LISTA DE SUBASTAS
				while($rowSubasta = $resultSubastas->fetch_assoc()) {//ITERACION SOBRE LAS SUBASTAS
					//VARIABLES A MOSTRAR
					//**************************************************************************
					$idSubasta = '';
					$tipoSubasta;
					$fechaIniSubasta; //fecha inicio subasta
					$fechaFinSubasta; //fecha fin subasta
					
					$producto_lote = -1; //Variable para fijar si es un producto = 0 y si es lote = 1. Por si acaso luego queremos saber en que tabla buscar
					$nombreObjeto = ''; 
					$descripcionObjeto = ''; //Si es un lote no tiene descripcion
					$imagenObjeto; //Si es un lote no tiene imagen
					$arrayProductos = array(); //Array que contiene la id de los productos si es un lote
					
					$nombreSubastador = '';
					$apellidosSubastador = '';
					
					$pujasRealizadas; //Es un array sobre el que hay que iterar, if($resultSubastas->num_rows > 0){while($rowSubasta = $resultSubastas->fetch_assoc()) {
					$pujaActual;
					//****************************************************************************
					//****************************************************************************
					
					$idSubasta = $rowSubasta['id'];
					$tipoSubasta = $rowSubasta['tipo'];
					$fechaIniSubasta = $rowSubasta['fechainicio'];
					$fechaFinSubasta = $rowSubasta['fechacierre'];
					
					$selectSubastador = "SELECT * FROM usuarios WHERE id='".$rowSubasta['idsubastador']."'";
					$resultSubastador = $conn->query($selectSubastador);
					
					$selectProductos = "SELECT * FROM productos WHERE idsubasta='".$idSubasta."'";
					$resultProductos = $conn->query($selectProductos);
					
					$selectLotes = "SELECT * FROM lotes WHERE idsubasta='".$idSubasta."'";
					$resultLotes = $conn->query($selectLotes);
					
					$selectPujas = "SELECT * FROM pujas WHERE idsubasta='".$idSubasta."' ORDER BY fecha DESC";
					$resultPujas = $conn->query($selectPujas);
					$pujasRealizadas = $resultPujas;
					
					if($resultSubastador->num_rows == 1){
						$rowSubastador = $resultSubastador->fetch_assoc();
						
						$nombreSubastador = $rowSubastador['nombre'];
						$apellidosSubastador = $rowSubastador['apellidos'];
					}
					if($resultProductos->num_rows == 1){
						$rowProductos = $resultProductos->fetch_assoc();
						
						$producto_lote = 0;
						$nombreObjeto = $rowProductos['nombre'];
						$descripcionObjeto = $rowProductos['descripcion'];
						$imagenObjeto = $rowProductos['imagen'];
					}else{
						if($resultProductos->num_rows != 0){
							$producto_lote = 1;
							while($rowProductos = $resultProductos->fetch_assoc()) {
								array_push($arrayProductos, $rowProductos['nombre']);
							}
						}
					}
					if($resultPujas->num_rows > 0){
						$pA = $resultPujas->fetch_assoc();
						$pujaActual = $pA['cantidad'];
					}else{
						$pujaActual = "Sea el primero en pujar ^^";
					}
				?>
				
				<div style="border-style: solid;">
					<a href="subasta.php?id=<?php echo $idSubasta; ?>">
						<div style="border-style: solid;">
							<h3><?php echo pasarTipoSubastaAString($tipoSubasta) ?></h3><p align="right"> <?php echo $idSubasta?></p>
						</div>
						<div style="border-style: solid;">
							<?php if(isset($imagenObjeto)){ echo "<img src='".$imagenObjeto."'"; } ?> 
						</div>
						<div style="border-style: solid;">
							<h1><?php echo $nombreObjeto; ?></h1>
						</div>
						<div style="border-style: solid; float: right;">
							<p> Fecha de finalizacion: <?php echo $fechaFinSubasta; ?></p> 
							<h3> <?php if(isset($pujaActual)){
										echo $pujaActual;
										if(is_int($pujaActual)){
											echo " euros";
										}
								} ?></h3>
						</div>
						<h3><?php if($producto_lote==0){echo "Producto"; }else{ echo "Lote";} ?></h3>
					
					</a>
				</div>
				
				
				
				<?php
				
				}
				
			}else{
				echo "No existen subastas actualmente.";
			}
	}
	?>