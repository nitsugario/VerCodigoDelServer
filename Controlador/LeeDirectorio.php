<?php 
error_reporting(false);
include_once('../Modelo/DirectorioIO.php');
$directorio=$_POST['dir'];
$dir = new DirectorioIO();
if ($directorio==" " || $directorio=="") {
	$directorio="inicio";
}
if ($directorio=="inicio") {
	$dir->directorio=$_SERVER['DOCUMENT_ROOT'];
	echo $dir->LeerDirectorio();
}else{
	$dir->directorio=$directorio;
	echo $dir->LeerDirectorio();
}
 ?>