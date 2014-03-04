<?php 
include_once('../Modelo/SintaxisHJP.php');
$AchivoDir=$_POST['dir'].$_POST['archi'];
$codigo = new SintaxisHJP();
$codigo->archivo=$AchivoDir;
$exten = $codigo->ValidaExtencions();
if ($exten=="php"|| $exten=="js"|| $exten=="css" || $exten=="html") {
	echo $codigo->MostrarSintaxisCodigo();
}else if ($exten=="jpg" || $exten=="gif"|| $exten=="png"|| $exten=="xcf" || $exten=="JPG"){
	echo "<br><br><img src=\"".$AchivoDir."\">";
}else if($exten=="txt"){
	echo $codigo->AbrirTexto();
}else{
	echo "<h3>por el momento el Archivo no esta soportado...</h3>";
}
 ?>