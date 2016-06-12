<?php
include("escribirLog.php");
function RedirectToURL($url, $tiempo)
{
	header("Refresh: $tiempo, URL=$url");
    exit;
}

if(isset($_GET['id'])){
        $idSubasta = $_GET['id'];
    }

$conn = new mysqli("localhost", "643b4d11c092cc1e", "sekret", "643b4d11c092cc1ea32a96bdb4f8da93");
$selectSubastas;
$resultSubastas;
if(session_id() == '') {
    session_start();
}

$idUser = $_SESSION['user']['postor'];

$select = "SELECT precioactual FROM subastas WHERE id='$idSubasta'";
$result = $conn->query($select);
$row=$result->fetch_assoc();
$valor = $row['precioactual'];

$date = date('Y-m-d H:i:s');

$select = "INSERT INTO pujas (fecha, cantidad, idsubasta, idpostor) VALUES ('$date', '$valor',    '$idSubasta', '$idUser')";

if ($conn->query($select) === TRUE) {
    echo "PUJA GUARDADA CORRECTAMENTE";
    $select = "SELECT id FROM pujas WHERE idsubasta='$idSubasta'";
    $result = $conn->query($select);
    $row=$result->fetch_assoc();
    $idPuja = $row['id'];   
    $update= "UPDATE subastas SET idpujaganadora='$idPuja' WHERE id='$idSubasta'";
    $conn->query($update);
    
    //escribir en el log
    $queryFinSubasta = "SELECT * FROM log WHERE descripcion = 'La puja ganadora de la subasta "  .$idSubasta.  " es " .$valor. "€.'";
    $resultQueryFinSubasta = $conn ->query($queryFinSubasta);
    if($resultQueryFinSubasta->num_rows == 0){
        $queryNombreUsuario= ("SELECT usuario FROM usuarios WHERE id ='$idUser'");
        $resultNombreUsuario = $conn->query( $queryNombreUsuario);
        $rowNombreUsuario = $resultNombreUsuario->fetch_assoc();
		$nombreUsuario = $rowNombreUsuario['usuario'];
       
        $queryBuscarProd = "SELECT id FROM productos WHERE idsubasta='$idSubasta' ";
        $resultNombreProd = $conn->query( $queryBuscarProd);
        if($resultNombreProd->num_rows > 0){
            $rowNombreProd = $resultNombreProd->fetch_assoc();
            $idprod = $rowNombreProd['id'];
            escribirLog("Puja de ".$valor." € realizada por: \""."$nombreUsuario"."\".", $idUser, $idSubasta, $idprod, "NULL", $idPuja);
            escribirLog("La puja ganadora de la subasta ".$idSubasta." es ".$valor."€.", $idUser, $idSubasta, $idprod, "NULL", $idPuja);
            escribirLog("La subasta ".$idSubasta." ha finalizado.", "NULL", $idSubasta, $idprod, "NULL", "NULL");
        }else{
            $queryBuscarLote= "SELECT id FROM lotes WHERE idsubasta='$idSubasta' ";
            $resultNombreLote = $conn->query( $queryBuscarLote);
            $rowNombreLote = $resultNombreLote->fetch_assoc();
            $idlote = $rowNombreLote['id'];
            escribirLog("La puja ganadora de la subasta ".$idSubasta." es ".$valor."€.", $idUser, $idSubasta, "NULL", $idlote, $idPuja);
        }

    }
    //fin de escribir en el log
    
    RedirectToURL("subastaHolandesa.php?id=$idSubasta", 0);

$update= "UPDATE subastas SET idpujaganadora='$idPuja' WHERE id='$idSubasta'";
} else {
    //echo "Error updating record: " . $conn->error;
}


?>